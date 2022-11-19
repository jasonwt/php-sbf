<?php
    declare(strict_types=1);

    namespace sbf\extensions;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    class Extension extends Component implements ExtensionInterface {
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);
        }

        protected function InitExtension() : bool {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND(true);
        }

        protected function DisabledPublicCanCallMethods() : array {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND([]);
        }

        public function CanCall(string $methodName) : bool {
            ComponentStartOfFunctionEvent::SEND([&$methodName]);

            if (in_array($methodName, $this->DisabledPublicCanCallMethods()))
                return ComponentEndOfFunctionEvent::SEND(false, [$methodName]);

            return ComponentEndOfFunctionEvent::SEND(parent::CanCall($methodName), [$methodName]);            
        }
    }
?>