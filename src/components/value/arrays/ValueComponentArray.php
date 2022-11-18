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
    }


?>