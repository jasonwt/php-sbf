<?php
    declare(strict_types=1);

    namespace sbf\components\arrayaccess;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\components\arrayaccess\ArrayAccessComponentInterface;
    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;
    
    class ArrayAccessComponent extends Component implements ArrayAccessComponentInterface {
        const ALLOW_SET             = 1;
        const ALLOW_SET_ON_NEW      = 2;
        const ALLOW_SET_ON_EXISTING = 4;
        const ALLOW_GET             = 8;
        const ALLOW_UNSET           = 16;

        protected int $options = 31;
        protected $iteratorIndex = 0;
        protected string $offsetGetMethod = "";
        protected string $offsetSetMethod = "";
        protected string $newComponentClass = "";

        public function __construct(string $name, string $offsetGetMethod = null, string $offsetSetMethod = null, string $newComponentClass = null, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->offsetGetMethod   = trim(strval($offsetGetMethod));
            $this->offsetSetMethod   = trim(strval($offsetSetMethod));
            $this->newComponentClass = trim(strval($newComponentClass));
        }

        protected function GetOffsetGetMethod($component) : string {
            ComponentStartOfFunctionEvent::SEND([&$component]);

            $returnValue = $this->offsetGetMethod;

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$component]);
        }

        protected function GetOffsetSetMethod($component) : string {
            ComponentStartOfFunctionEvent::SEND([&$component]);

            $returnValue = $this->offsetSetMethod;

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$component]);
        }

        protected function GetNewComponentClass(string $offset) : string {
            ComponentStartOfFunctionEvent::SEND([&$offset]);

            return ComponentEndOfFunctionEvent::SEND($this->newComponentClass, [$offset]);
        }

        /**
         * Whether an offset exists
         * Whether or not an offset exists.
         *
         * @param mixed $offset An offset to check for.
         * @return bool Returns `true` on success or `false` on failure.
         */
        public function offsetExists($offset) {
            $returnValue = false;

            if (ComponentStartOfFunctionEvent::SEND([&$offset]))
                $returnValue = in_array($offset, $this->GetComponentNames());
            
            return ComponentEndOfFunctionEvent::SEND($returnValue, [$offset]);            
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

            if (!ComponentStartOfFunctionEvent::SEND([&$offset])) {                
                if ($this->options && self::ALLOW_GET) {
                    if (is_null($component = $this->GetComponent($offset))) {
                        $this->AddError(E_USER_WARNING, "fffset '$offset' was not found.");
                    } else {
                        $getMethod = $this->GetOffsetGetMethod($component);

                        if ($getMethod != "" && method_exists($component, $getMethod))
                            $returnValue = call_user_func([$component, $getMethod]);
                        else
                            $returnValue = $component;
                    }
                } else {
                    $this->AddError(E_USER_ERROR, "offsetGet() is not permitted.");
                }                
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$offset]);                
        }
        
        /**
         * Assigns a value to the specified offset.
         *
         * @param mixed $offset The offset to assign the value to.
         * @param mixed $value The value to set.
         */
        public function offsetSet($offset, $value) {
            
            if (ComponentStartOfFunctionEvent::SEND([&$offset, &$value]) !== false) {
/*                
                if ($offset == "") {
                    for ($cnt = 0; ; $cnt ++) {                    
                        $offset = strval(count($this->components)+$cnt);

                        if (!array_key_exists($offset, $this->components))
                            break;
                    }
                }
*/
                if (array_key_exists($offset, $this->components) && !($this->options & self::ALLOW_SET) && !($this->options & self::ALLOW_SET_ON_EXISTING)) {
                    $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for EXISTING components.");
                } else if (!array_key_exists($offset, $this->components) && !($this->options & self::ALLOW_SET) && !($this->options & self::ALLOW_SET_ON_NEW)) {
                    $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for NEW components.");
                } else {
                    $existingOffset = array_key_exists($offset, $this->components);

                    if (($newComponentClass = $this->GetNewComponentClass($offset)) == "")
                        $newComponentClass = "\\sbf\\components\\Component";
                        //$newComponentClass = "\\sbf\\components\\value\\ValueComponent";

                    $newComponent = ($existingOffset ? $this->components[$offset] : new $newComponentClass($offset));

                    $newComponentCanSet = false;
                    $valueCanGet = false;

                    if (($offsetSetMethod = $this->GetOffsetSetMethod($newComponent)) != "")
                        $newComponentCanSet = (method_exists($newComponent, $offsetSetMethod));                                
                    

                    if (($offsetGetMethod = $this->GetOffsetGetMethod($value)) != "") {
                        if ($valueCanGet = (method_exists($value, $offsetGetMethod))) {
                            if ($newComponentCanSet)
                                $value = call_user_func([$value, $offsetGetMethod]);
                        }                            
                    }
    
                    if ($newComponentCanSet) {
                        call_user_func(
                            [$newComponent, $offsetSetMethod],
                            $value
                        );                            
                    } else if ($value instanceof Component) {
                        if ($existingOffset) {
                            if ($offset == $value->GetName()) {
                                $this->ReplaceComponentByName($offset, $value);
                            } else {
                                $this->AddError(E_USER_ERROR, "offset and value->GetName() are not the same");
                            }
                        } else {
                            $newComponent = $value;                            
                        }
                        
                    } else {
                        $newComponent = null;
                        $this->AddError(E_USER_ERROR, "offsetSet() new failed.");
                    }

                    if (!$existingOffset && !is_null($newComponent)) {
                        if ($offset == $newComponent->GetName()) {
                            
                            $this->AddComponent($newComponent);
                        } else {
                            $this->AddError(E_USER_ERROR, "offset and value->GetName() are not the same");
                        }
                    }
                }            
            }

            return ComponentEndOfFunctionEvent::SEND(null, [$offset, $value]);
        }
        
        /**
         * Unsets an offset.
         *
         * @param mixed $offset The offset to unset.
         */
        public function offsetUnset($offset) {
            if (ComponentStartOfFunctionEvent::SEND([&$offset]) !== false) {

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

            ComponentEndOfFunctionEvent::SEND(null, [$offset]);            
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

            return true;
        }
        
        /**
         * Count elements of an object
         * This method is executed when using the count() function on an object implementing Countable.
         * @return int The custom count as an `int`.
         */
        public function count() {
            return count($this->components);            
        }
    }
    
    
?>