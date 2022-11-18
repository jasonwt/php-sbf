<?php
    declare(strict_types=1);

    namespace sbf\components\value\arrays;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;
    use sbf\components\value\ValueComponent;

    class ValueComponentArray extends Component {
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            $this->errorHandler = (is_null($errorHandler) ? new ErrorHandler() : $errorHandler);

            if (!is_null($components)) {
                if (!is_array($components))
                    $components = [$components];

                for ($ccnt = 0; $ccnt < count($components); $ccnt ++) {
                    $componentKey = array_keys($components)[$ccnt];
                    $componentValue = $components[$componentKey];

                    if (is_object($componentValue)) {
                        if (!($componentValue instanceof ValueComponent))
                            $this->AddError(E_USER_ERROR, "Invalid component type '" . get_class($componentValue) . "'. Must be derived from ValueComponent.");
                    } else {
                        $components[$componentKey] = new ValueComponent($componentKey, $componentValue);
                    } 
                }
            }

            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->options = self::ALLOW_SET + self::ALLOW_GET + self::ALLOW_UNSET;
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
                    $returnValue = $component->GetValue();
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
            $error = [];

            if (array_key_exists($offset, $this->components)) {
                if ($this->options & self::ALLOW_SET_ON_FOUND || $this->options & self::ALLOW_SET) {                        
                    if (!is_null($component = $this->GetComponent($offset)))
                        $component->SetValue($value);
                } else {
                    $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for EXISTING components.");
                    return;
                }
            } else {
                if (!($this->options & self::ALLOW_SET_ON_NOT_FOUND || $this->options & self::ALLOW_SET)) {
                    $this->AddError(E_USER_ERROR, "offsetSet() is not permitted for NEW components.");
                    return;
                } else {
                    $this->AddComponent(new ValueComponent($offset, $value));
                }
            }
        }

    }

    


?>