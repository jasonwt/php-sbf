<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandler\ErrorHandler;
    use sbf\components\ComponentInterface;
    use sbf\extensions\Extension;
    use sbf\components\ComponentObjectArrayTraits;
    use sbf\components\ComponentArrayAccessTraits;
    use sbf\components\ComponentCountableIteratorTraits;

    use function sbf\debugging\dtprint;

    class Component implements ComponentInterface {
        use ComponentObjectArrayTraits;
        use ComponentArrayAccessTraits;
        use ComponentCountableIteratorTraits;

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
        protected array $extensions = [];

        protected ErrorHandler $errorHandler;

        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
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

            if (!is_null($extensions)) {
                if (!is_array($extensions))
                    $extensions = [$extensions];

                foreach ($extensions as $extensions) {
                    if (!is_object($extensions)) {
                        $this->AddError(E_USER_ERROR, "Invalid extension type '" . gettype($extensions) . "'. Must be derived from Extension.");
                    } else if (!($extensions instanceof Extension)) {
                        $this->AddError(E_USER_ERROR, "Invalid extension type '" . get_class($extensions) . "'. Must be derived from Extension.");
                    } else {
                        $this->AddExtension($extensions);
                    }
                }
            }
        }

        public function __call(string $methodName, array $arguments) {
            foreach ($this->extensions as $extensionName => $extension) {
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

        

        

        /* 
        EXTENSIONS METHODS 
        */

        protected function GetExtensionsCount(string $extensionType = "") : int {
            $this->ProcessHook("GetExtensionsCount_FIHOOK", [$this, &$extensionType]);

            $returnValue = $this->GetObjectArrayElementCount($this->extensions, $extensionType);

            return $this->ProcessHook("GetExtensionsCount_FRHOOK", [$this, $returnValue, $extensionType]);
        }

        protected function GetExtensions(string $extensionType = "") : array {
            $this->ProcessHook("GetExtensions_FIHOOK", [$this, &$extensionType]);

            $returnValue = $this->GetObjectArrayElements($this->extensions, $extensionType);

            return $this->ProcessHook("GetExtensions_FRHOOK", [$this, $returnValue, $extensionType]);
        }

        protected function GetExtensionNames(string $extensionType = "") : array {
            $this->ProcessHook("GetExtensionNames_FIHOOK", [$this]);

            $returnValue = $this->GetObjectArrayElementKeys($this->extensions, $extensionType);            
            
            return $this->ProcessHook("GetExtensionNames_FRHOOK", [$this, $returnValue]);
        }

        protected function GetExtension(string $name) : ?Extension {
            $this->ProcessHook("GetExtension_FIHOOK", [$this, &$name]);

            $returnValue = $this->GetObjectArrayElement($this->extensions, $name);            

            return $this->ProcessHook("GetExtension_FRHOOK", [$this, $returnValue, $name]);
        }

        protected function ExtensionExists(Extension $extension) : bool {
            $this->ProcessHook("ExtensionExists_FIHOOK", [$this, &$extension]);

            $returnValue = $this->ObjectArrayElementExists($this->extensions, $extension);
            
            return $this->ProcessHook("ExtensionExists_FRHOOK", [$this, $returnValue, $extension]);
        }

        protected function AddExtension(Extension $extension) : bool {
            $this->ProcessHook("AddExtension_FIHOOK", [$this, &$extension]);

            $returnValue = $this->ObjectArrayAddElement($this->extensions, $extension);            

            if ($returnValue)
                $returnValue = $extension->InitExtension();            

            return $this->ProcessHook("AddExtension_FRHOOK", [$this, $returnValue, $extension]);
        }

        protected function RemoveExtension(Extension $extension) : bool {
            $this->ProcessHook("RemoveExtension_FIHOOK", [$this, &$extension]);
            
            $returnValue = $this->ObjectArrayRemoveElement($this->extensions, $extension);

            return $this->ProcessHook("RemoveExtension_FRHOOK", [$this, $returnValue, $extension]);
        }

        /* 
        COMPONENT METHODS 
        */

        protected function GetComponentsCount(string $componentType = "") : int {
            $this->ProcessHook("GetComponentsCount_FIHOOK", [$this, &$componentType]);

            $returnValue = $this->GetObjectArrayElementCount($this->components, $componentType);

            return $this->ProcessHook("GetComponentsCount_FRHOOK", [$this, $returnValue, $componentType]);
        }

        protected function GetComponents(string $componentType = "") : array {
            $this->ProcessHook("GetComponents_FIHOOK", [$this, &$componentType]);

            $returnValue = $this->GetObjectArrayElements($this->components, $componentType);

            return $this->ProcessHook("GetComponents_FRHOOK", [$this, $returnValue, $componentType]);
        }

        protected function GetComponentNames(string $componentType = "") : array {
            $this->ProcessHook("GetComponentNames_FIHOOK", [$this]);

            $returnValue = $this->GetObjectArrayElementKeys($this->components, $componentType);            
            
            return $this->ProcessHook("GetComponentNames_FRHOOK", [$this, $returnValue]);
        }

        protected function GetComponent(string $name) : ?Component {
            $this->ProcessHook("GetComponent_FIHOOK", [$this, &$name]);

            $returnValue = $this->GetObjectArrayElement($this->components, $name);            

            return $this->ProcessHook("GetComponent_FRHOOK", [$this, $returnValue, $name]);
        }

        protected function ComponentExists(Component $component) : bool {
            $this->ProcessHook("ComponentExists_FIHOOK", [$this, &$component]);

            $returnValue = $this->ObjectArrayElementExists($this->components, $component);
            
            return $this->ProcessHook("ComponentExists_FRHOOK", [$this, $returnValue, $component]);
        }

        protected function AddComponent(Component $component) : bool {
            if ($component instanceof Extension) {
                $this->AddError(E_USER_ERROR, "Use AddExtension rather then AddComponent when adding extensions.");
                return false;
            }

            $this->ProcessHook("AddComponent_FIHOOK", [$this, &$component]);

            $returnValue = $this->ObjectArrayAddElement($this->components, $component);                        

            return $this->ProcessHook("AddComponent_FRHOOK", [$this, $returnValue, $component]);
        }

        protected function RemoveComponent(Component $component) : bool {
            usleep(1000);
            $this->ProcessHook("RemoveComponent_FIHOOK", [$this, &$component]);
            
            $returnValue = $this->ObjectArrayRemoveElement($this->components, $component);

            return $this->ProcessHook("RemoveComponent_FRHOOK", [$this, $returnValue, $component]);
        }

        /* 
        PROCESS HOOKS
        */

        protected function ProcessHook($hookName, array $parameters = [])  {
            if (substr($hookName, -7) == "_FRHOOK") {
                $tmpParameters = $parameters;                

                if (method_exists($this, $hookName))
                    $tmpParameters[1] = call_user_func_array([$this, $hookName], $tmpParameters);

                foreach (array_merge($this->components, $this->extensions) as $component) {
                    if (method_exists($component, $hookName))
                        $tmpParameters[1] = call_user_func_array([$component, $hookName], $tmpParameters);
                }
                
                return $parameters[1];

            } else if (substr($hookName, -7) == "_FIHOOK") {
                if (method_exists($this, $hookName))
                    $this->ProcessHook($hookName, $parameters);                    

                foreach (array_merge($this->components, $this->extensions) as $component)
                    $component->ProcessHook($hookName, $parameters);                

                return;
            }

            $this->AddError(E_USER_ERROR, "Invalid hookName '$hookName'");
            
            return;
        }
    }

?>