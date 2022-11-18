<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandler\ErrorHandler;
    use sbf\components\ComponentInterface;
    use sbf\extensions\Extension;

    use function sbf\debugging\dtprint;

    class Component implements ComponentInterface {
        const ALLOW_SET              = 1;
        const ALLOW_SET_ON_FOUND     = 2;
        const ALLOW_SET_ON_NOT_FOUND = 4;
        const ALLOW_UNSET            = 8;
        const ALLOW_GET              = 16;

        protected $iteratorIndex = 0;
        protected $options = 20;
        protected $name = "";
        protected ?Component $parent = null;
        protected array $components = [];

        private ErrorHandler $errorHandler;

        public function __construct(string $name, $components = null, ?ErrorHandler $errorHandler = null) {
            $this->errorHandler = (is_null($errorHandler) ? new ErrorHandler() : $errorHandler);

            if (($this->name = trim($name)) == "")                 
                $this->AddError(E_USER_ERROR, "name is required and can not be all whitespaces.");

            if (!is_null($components)) {
                if (!is_array($components))
                    $components = [$components];

                foreach ($components as $component) {
                    if (!is_object($component)) {
                        $this->AddError(E_USER_ERROR, "Invalid component type '" . gettype($component) . "'. Must be derived from Component.");
                    } else if (!($component instanceof Component)) {
                        $this->AddError(E_USER_ERROR, "Invalid component type '" . get_class($component) . "'. Must be derived from Component.");
                    } else {
                        $this->AddComponent($component);
                    }
                }
            }
            
        }

        public function __call(string $methodName, array $arguments) {

//            dtprint("__call(", $methodName, ", ", $arguments, ")\n", $this);

            foreach ($this->GetComponents("sbf\\extensions\\ExtensionInterface") as $extension) {
                if ($extension->CanCall($methodName))
                    return call_user_func_array([$extension, $methodName], $arguments);                                
            }

            throw new \BadMethodCallException(get_class($this) . "::" . $methodName);
        }

        public function GetError(?int $errorIndex = null) : ?string {
            $this->ProcessHook("GetError_FIHOOK", [$this, &$errorIndex]);

            return $this->ProcessHook("GetError_FRHOOK", [$this, $this->errorHandler->GetError($errorIndex), $errorIndex]);
        }
        public function GetErrors() : array {
            $this->ProcessHook("GetErrors_FIHOOK", [$this]);

            return $this->ProcessHook("GetErrors_FRHOOK", [$this, $this->errorHandler->GetErrors()]);
        }
        public function GetErrorCount() : int {
            $this->ProcessHook("GetErrorCount_FIHOOK", [$this]);

            return $this->ProcessHook("GetErrorCount_FRHOOK", [$this, $this->errorHandler->GetErrorCount()]);
        }
        protected function ClearError(int $errorIndex) : bool {
            $this->ProcessHook("ClearError_FIHOOK", [$this, &$errorIndex]);

            return $this->ProcessHook("ClearError_FRHOOK", [$this, $this->ClearError($errorIndex), $errorIndex]);
        }
        protected function ClearErrors() {
            $this->ProcessHook("ClearErrors_FIHOOK", [$this]);

            $this->ProcessHook("ClearErrors_FRHOOK", [$this, $this->errorHandler->ClearErrors()]);
        }

        protected function AddError(int $errorCode, string $errorMessage) : bool {
            $this->ProcessHook("AddError_FIHOOK", [$this, &$errorCode, &$errorMessage]);

            return $this->ProcessHook("AddError_FRHOOK", [$this, $this->errorHandler->AddError($errorCode, $errorMessage), $errorCode, $errorMessage]);
        }

        public function GetName() : string {
            $this->ProcessHook("GetName_FIHOOK", [$this]);

            return $this->ProcessHook("GetName_FRHOOK", [$this, $this->name]);
        }

        protected function Rename(string $newName) : bool {
            $this->ProcessHook("Rename_FIHOOK", [$this, &$newName]);

            if (!is_null($this->parent)) {
                if (in_array($newName, $this->parent->GetComponentNames())) {
                    $this->AddError(E_USER_ERROR, "A component already exists with the name '$newName'");
                    
                    return $this->ProcessHook("Rename_FRHOOK", [$this, false, $newName]);
                }

                $tmpParent = $this->parent;

                if (!$this->SetParent(null)) {
                    $this->AddError(E_USER_ERROR, "this->SetParent(null) failed.");
                    
                    return $this->ProcessHook("Rename_FRHOOK", [$this, false, $newName]);
                }

                $oldName = $this->name;
                $this->name = $newName;

                if (!$this->SetParent($tmpParent)) {
                    $this->AddError(E_USER_ERROR, "this->SetParent() failed.");

                    $this->name = $oldName;
                    $this->SetParent($tmpParent);
                    
                    return $this->ProcessHook("Rename_FRHOOK", [$this, false, $newName]);
                }
            } else {
                $this->name = $newName;
            }

            return $this->ProcessHook("Rename_FRHOOK", [$this, true, $newName]);
        }

        public function GetParent() : ?Component {
            $this->ProcessHook("GetParent_FIHOOK", [$this]);

            return $this->ProcessHook("GetParent_FRHOOK", [$this, $this->parent]);
        }

        public function CanCall(string $methodName) : bool {
            $this->ProcessHook("CanCall_FIHOOK", [$this, &$methodName]);

            $reflection = new \ReflectionMethod($this, $methodName);

            return $this->ProcessHook("CanCall_FRHOOK", [$this, $reflection->isPublic(), $methodName]);
        }

        protected function RegisterExtension(Extension $extension) : bool {
            $this->ProcessHook("RegisterExtension_FIHOOK", [$this, &$extension]);

            return $this->ProcessHook("RegisterExtension_FRHOOK", [$this, $this->AddComponent($extension), $extension]);
        }

        /**
         * Whether an offset exists
         * Whether or not an offset exists.
         *
         * @param mixed $offset An offset to check for.
         * @return bool Returns `true` on success or `false` on failure.
         */
        public function offsetExists($offset) {
            $this->ProcessHook("offsetExists_FIHOOK", [$this, &$offset]);

            return $this->ProcessHook("offsetExists_FRHOOK", [$this, in_array($offset, $this->GetComponentNames()), $offset]);
        }
        
        /**
         * Offset to retrieve
         * Returns the value at specified offset.
         *
         * @param mixed $offset The offset to retrieve.
         * @return mixed Can return all value types.
         */
        public function offsetGet($offset) {
            $returnValue = null;

            if ($this->options && self::ALLOW_GET) {
                if (is_null($component = $this->GetComponent($offset)))
                    $this->AddError(E_USER_WARNING, "fffset '$offset' was not found.");
                else    
                    $returnValue = $component;
            } else {
                $this->AddError(E_USER_ERROR, "offsetGet() is not permitted.");
            }

            return $returnValue;
            //return $this->ProcessHook("offsetGet_FRHOOK", [$this, $returnValue, $offset]);            
        }
        
        /**
         * Assigns a value to the specified offset.
         *
         * @param mixed $offset The offset to assign the value to.
         * @param mixed $value The value to set.
         */
        public function offsetSet($offset, $value) {
            if (!$value instanceof Component) {
                $this->AddError(E_USER_ERROR, "value is not derived from Component.");
            } else if ($value->GetName() != $offset) {
                $this->AddError(E_USER_ERROR, "offset and value->GetName() are not the same");
            } else {
                $error = [];

                if (array_key_exists($offset, $this->components)) {
                    if ($this->options & self::ALLOW_SET_ON_FOUND || $this->options & self::ALLOW_SET) {                        
                        if (!is_null($component = $this->GetComponent($offset))) {
                            if ($this->RemoveComponent($component) == false)
                                $error = ["errorCode" => E_USER_ERROR, "errorMessage" => "this->RemoveComponent() failed."];
                        }                      
                    } else {
                        $error = ["errorCode" => E_USER_ERROR, "errorMessage" => "offsetSet() is not permitted for EXISTING components"];
                    }
                } else {
                    if (!($this->options & self::ALLOW_SET_ON_NOT_FOUND || $this->options & self::ALLOW_SET))
                        $error = ["errorCode" => E_USER_ERROR, "errorMessage" => "offsetSet() is not permitted for NEW components"];
                }

                if (count($error) == 0) {
                    if (!$this->AddComponent($value))                                            
                        $this->AddError(E_USER_ERROR, "offsetSet($offset, value) failed.");
                } else {
                    $this->AddError($error["errorCode"], $error["errorMessage"]);                    
                }
            }
        }
        
        /**
         * Unsets an offset.
         *
         * @param mixed $offset The offset to unset.
         */
        public function offsetUnset($offset) {
            if ($this->options & self::ALLOW_UNSET) {
                if (!is_null($component = $this->GetComponent($offset))) {
                    $this->RemoveComponent($component);
                } else {
                    $this->AddError(E_USER_WARNING, "Offset '$offset' not found.");
                }
            } else {
                $this->AddError(E_USER_ERROR, "offsetUnset($offset) is not permitted.");
            }
        }

        /**
         * Returns the current element.
         * @return mixed Can return any type.
         */
        public function current() {
            if (!$this->valid())
                return null;
            
            return $this[$this->GetComponentNames()[$this->iteratorIndex]];
            //return $this->components[$this->GetComponentNames()[$this->currentElement]];
        }
        
        /**
         * Returns the key of the current element.
         * @return mixed Returns `scalar` on success, or `null` on failure.
         */
        public function key() {
            if (!$this->valid())            
                return null;

            return array_keys($this->components)[$this->iteratorIndex];
        }
        
        /**
         * Move forward to next element
         * Moves the current position to the next element.
         */
        public function next() {
            $this->iteratorIndex ++;
            
            return $this->current();
        }
        
        /**
         * Rewind the Iterator to the first element
         * Rewinds back to the first element of the Iterator.
         */
        public function rewind() {
            $this->iteratorIndex = 0;
        }
        
        /**
         * Checks if current position is valid
         * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
         * @return bool The return value will be casted to `bool` and then evaluated. Returns `true` on success or `false` on failure.
         */
        public function valid() {            
            if ($this->iteratorIndex >= count($this->components))
                return false;

            $componentNames = $this->GetComponentNames();
        
            while ($this->iteratorIndex < count($this->components)) {
                $component = $this->components[$componentNames[$this->iteratorIndex]];

                if ($component instanceof Component && !($component instanceof Extension))
                    return true;

                $this->iteratorIndex ++;
            }


            return false;
        }
        
        /**
         * Count elements of an object
         * This method is executed when using the count() function on an object implementing Countable.
         * @return int The custom count as an `int`.
         */
        public function count() {
            return count(
                array_filter(
                    $this->GetComponents("\\sbf\\components\\Component"), 
                    function ($v, $k) { return !($v instanceof Extension);}, 
                    ARRAY_FILTER_USE_BOTH
                )
            );            
        }

        protected function GetNumberOfComponents(string $componentType = "") : int {
            $this->ProcessHook("GetNumerOfComponents_FIHOOK", [$this, &$componentType]);            

            return $this->ProcessHook("GetComponents_FRHOOK", [$this, count($this->GetComponents($componentType)), $componentType]);
        }

        protected function GetComponents(string $componentType = "") : array {
            $this->ProcessHook("GetComponents_FIHOOK", [$this, &$componentType]);

            $componentType = trim($componentType);

            $results = $this->components;

            if (($componentType = trim($componentType)) != "" ) {
                $results = array_filter($results, function ($v, $k) use ($componentType) {
                    return is_a($v, $componentType);
                }, ARRAY_FILTER_USE_BOTH);
            }

            return $this->ProcessHook("GetComponents_FRHOOK", [$this, $results, $componentType]);
        }

        protected function GetComponentNames(string $componentType = "") : array {
            $this->ProcessHook("GetComponentNames_FIHOOK", [$this]);

            $componentType = trim($componentType);
            
            $returnValue = [];

            foreach ($this->components as $componentName => $component) {
                if ($componentType == "" || is_a($component, $componentType))
                    $returnValue[] = $componentName;
            }
            
            return $this->ProcessHook("GetComponentNames_FRHOOK", [$this, $returnValue]);
        }

        protected function GetComponent(string $name) : ?Component {
            $this->ProcessHook("GetComponent_FIHOOK", [$this, &$name]);

            if (!array_key_exists($name, $this->components)) {
                $this->AddError(E_USER_ERROR, "No component exists with the name of '$name'");                

                return $this->ProcessHook("GetComponent_FRHOOK", [$this, null, $name]);
            }

            return $this->ProcessHook("GetComponent_FRHOOK", [$this, $this->components[$name], $name]);
        }

        protected function ComponentExists(Component $component) : bool {
            $this->ProcessHook("ComponentExists_FIHOOK", [$this, &$component]);
            
            return $this->ProcessHook("ComponentExists_FRHOOK", [$this, in_array($component, $this->components, true), $component]);
        }

        protected function ProcessHook($hookName, array $parameters = [])  {
//            echo "ProcessHook($hookName)\n";

            if (substr($hookName, -7) == "_FRHOOK") {
                $tmpParameters = $parameters;                

                if (method_exists($this, $hookName))
                    $tmpParameters[1] = call_user_func_array([$this, $hookName], $tmpParameters);

                foreach ($this->components as $component) {
                    if (method_exists($component, $hookName))
                        $tmpParameters[1] = call_user_func_array([$component, $hookName], $tmpParameters);
                }
                
                return $parameters[1];

            } else if (substr($hookName, -7) == "_FIHOOK") {
                if (method_exists($this, $hookName))
                    $this->ProcessHook($hookName, $parameters);                    

                foreach ($this->components as $component)
                    $component->ProcessHook($hookName, $parameters);

                return;
            }

            $this->AddError(E_USER_ERROR, "Invalid hookName '$hookName'");
            
            return;
        }



        protected function AddComponent(Component $component) : bool {
            usleep(1000);
            $this->ProcessHook("AddComponent_FIHOOK", [$this, &$component]);

            if (in_array($component, $this->components, true)) {
                $this->AddError(E_USER_ERROR, "The component already exists");
                
                return $this->ProcessHook("AddComponent_FRHOOK", [$this, false, $component]);
            }

            if (array_key_exists($component->GetName(), $this->components)) {
                $this->AddError(E_USER_ERROR, "A component already exists with the name '" . $component->GetName() . "'");

                return $this->ProcessHook("AddComponent_FRHOOK", [$this, false, $component]);
            }

            $this->components[$component->GetName()] = $component;
            
            if (!is_null($componentsParent = $component->GetParent()))
                $componentsParent->RemoveComponent($component);

            $component->parent = $this;            

            $returnValue = true;

            if ($component instanceof Extension)
                $returnValue = $component->InitExtension();

            return $this->ProcessHook("AddComponent_FRHOOK", [$this, $returnValue, $component]);
        }

        protected function RemoveComponent(Component $component) : bool {
            usleep(1000);
            $this->ProcessHook("RemoveComponent_FIHOOK", [$this, &$component]);            

            if (!array_key_exists($component->GetName(), $this->components)) {            
                $this->AddError(E_USER_ERROR, "The component does not exist");
                
                return $this->ProcessHook("RemoveComponent_FRHOOK", [$this, false, $component]);
            }            

            unset($this->components[$component->GetName()]);

            $component->parent = null;

            return $this->ProcessHook("RemoveComponent_FRHOOK", [$this, true, $component]);
        }
    }

?>