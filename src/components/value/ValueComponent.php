<?php
    declare(strict_types=1);

    namespace sbf\components\value;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\components\Component;
    use sbf\components\ComponentInterface;
    use sbf\errorhandlers\ErrorHandler;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    class ValueComponent extends Component implements ComponentInterface {
        private $value;

        public function __construct(string $name, $value = null, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            if (!is_null($value))
                $this->value = $value;            
        }

        public function GetValue() {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND($this->value);
        }

        public function SetValue($value) : ?ValueComponentInterface {
            $returnValue = null;

            if ((ComponentStartOfFunctionEvent::SEND([&$value])) !== false) {
                $this->value = $value;
                $returnValue = $this;
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$value]);
        }
    }


?>