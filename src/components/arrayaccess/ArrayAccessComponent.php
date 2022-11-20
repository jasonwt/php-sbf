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

            $this->offsetSetMethod   = trim(strval($offsetGetMethod));
            $this->offsetSetMethod   = trim(strval($offsetSetMethod));
            $this->newComponentClass = trim(strval($newComponentClass));
        }

        protected function GetOffsetGetMethod($component) : string {
            ComponentStartOfFunctionEvent::SEND([&$component]);

            return ComponentEndOfFunctionEvent::SEND($this->offsetGetMethod, [$component]);
        }

        protected function GetOffsetSetMethod($component) : string {
            ComponentStartOfFunctionEvent::SEND([&$component]);

            return ComponentEndOfFunctionEvent::SEND($this->offsetSetMethod, [$component]);
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
                if ($offset == "")
                    $offset = strval(count($this->components));

                if (array_key_exists($offset, $this->components)) {
                    if ($this->options & self::ALLOW_SET || $this->options & self::ALLOW_SET_ON_EXISTING) {
                        $component = $this->components[$offset];

                        $offsetSetMethod = $this->GetOffsetSetMethod($component);

                        if ($offsetSetMethod != "" && method_exists($component, $offsetSetMethod)) {
                            if (!call_user_func([$component, $offsetSetMethod], $value)) {
                                $this->AddError(E_USER_ERROR, "offsetSetMethod '$offsetSetMethod' failed.");
                            }
                        } else {
                            if ($value instanceof Component) {
                                if ($value->GetName() == $offset) {
                                    $this->ReplaceComponentByName($offset, $value);
                                } else {
                                    $this->AddError(E_USER_ERROR, "offset and value->GetName() are not the same");
                                }
                            } else {
                                $this->AddError(E_USER_ERROR, "value is not derived from Component.");
                            }
                        }
                    } else {
                        $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for EXISTING components.");
                    }
                } else {
                    if ($this->options & self::ALLOW_SET || $this->options & self::ALLOW_SET_ON_NEW) {                        
                        if (($newComponentClass = $this->GetNewComponentClass($offset)) == "")
                            $newComponentClass = "\\sbf\\components\\Component";
                        
                        $newComponent = new $newComponentClass($offset);

                        echo gettype($newComponent) . " ";
                        print_r($newComponent);

                        if (($offsetGetMethod = $this->GetOffsetGetMethod($value)) != "") {
                            
                            $newComponent = $value;
                        } else if (($offsetSetMethod = $this->GetOffsetSetMethod($newComponent)) != "") {
                            call_user_func([$newComponent, $offsetSetMethod], $value);
                        } else if ($value instanceof Component) {                            
                            $newComponent = $value;
                        } else {
                            $newComponent = null;
                        }
                                 
                        echo gettype($newComponent) . " ";
                        print_r($newComponent);

                        if (!is_null($newComponent)) {
                            if ($newComponent instanceof Component) {
                                $this->components[$offset] = $newComponent;
                            } else {
                                $this->AddError(E_USER_ERROR, "newComponent is not derived from Component.");
                            }
                        } else {
                            $this->AddError(E_USER_ERROR, "offsetSet() failed.");
                        }
                    } else {
                        $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for NEW components.");    
                        
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
    }
    
    
?>