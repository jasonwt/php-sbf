<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\events\components\ComponentEndOfFunctionEvent;
    use sbf\events\components\ComponentStartOfFunctionEvent;

    trait ComponentArrayAccessTraits {
        /**
         * Whether an offset exists
         * Whether or not an offset exists.
         *
         * @param mixed $offset An offset to check for.
         * @return bool Returns `true` on success or `false` on failure.
         */
        public function offsetExists($offset) {
            $returnValue = false;

            if (ComponentStartOfFunctionEvent::SEND([&$offset])) {

                $returnValue = in_array($offset, $this->GetComponentNames());

            }
            
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
                    if (is_null($component = $this->GetComponent($offset)))
                        $this->AddError(E_USER_WARNING, "fffset '$offset' was not found.");
                    else    
                        $returnValue = $component;
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

                if (!$value instanceof Component) {
                    $this->AddError(E_USER_ERROR, "value is not derived from Component.");
                } else if ($value->GetName() != $offset) {
                    $this->AddError(E_USER_ERROR, "offset and value->GetName() are not the same");
                } else {
                    
                    if (array_key_exists($offset, $this->components)) {                    
                        if ($this->options & self::ALLOW_SET_ON_FOUND || $this->options & self::ALLOW_SET) {                            
                            if ($value != $this->components[$offset]) {
                                if (!is_null($component = $this->GetComponent($offset))) {
                                    $componentIndex = array_search($offset, array_keys($this->components));

                                    if ($this->RemoveComponent($component) == false) {
                                        $this->AddError(E_USER_ERROR, "this->RemoveComponent() failed.");
                                    } else {
                                        $insertIndex = (count($this->components) > 0 ? array_keys($this->components)[$componentIndex] : null);

                                        if (!$this->AddComponent($value, $insertIndex))
                                            $this->AddError(E_USER_ERROR, "this->AddComponent() failed.");                                        
                                    }
                                } else {
                                    $this->AddError(E_USER_ERROR, "this->AddComponent($offset) failed.");
                                }
                            } 
                        } else {
                            $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for EXISTING components.");
                        }
                    } else {                    
                        if (!($this->options & self::ALLOW_SET_ON_NOT_FOUND || $this->options & self::ALLOW_SET)) {
                            $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for NEW components.");
                        } else {
                            if (!$this->AddComponent($value)) {
                                $this->AddError(E_USER_ERROR, "this->AddComponent() failed.");
                            }
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
    }


?>