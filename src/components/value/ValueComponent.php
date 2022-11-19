<?php
    declare(strict_types=1);

    namespace sbf\components\value;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    class ValueComponent extends Component {
        private $value;

        public function __construct(string $name, $value = null, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->value = $value;            
        }

        public function GetValue() {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND($this->value);
        }

        public function SetValue($value) {
            ComponentStartOfFunctionEvent::SEND([&$value]);

            $this->value = $value;

            return ComponentEndOfFunctionEvent::SEND(null, [$value]);
        }
    }


?>