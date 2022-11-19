<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    trait ComponentErrorTraits {
        public function GetError(?int $errorIndex = null) : ?string {
            ComponentStartOfFunctionEvent::SEND([&$errorIndex]);

            $returnValue = $this->errorHandler->GetError($errorIndex);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$errorIndex]);
        }
        public function GetErrors() : array {
            ComponentStartOfFunctionEvent::SEND();

            $returnValue = $this->errorHandler->GetErrors();

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }
        public function GetErrorCount() : int {
            ComponentStartOfFunctionEvent::SEND();

            $returnValue = $this->errorHandler->GetErrorCount();

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }
        protected function ClearError(int $errorIndex) : bool {
            ComponentStartOfFunctionEvent::SEND([&$errorIndex]);

            $returnValue = $this->errorHandler->ClearError($errorIndex);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$errorIndex]);
        }
        protected function ClearErrors() {
            ComponentStartOfFunctionEvent::SEND();

            $returnValue = $this->errorHandler->ClearErrors();

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }

        protected function AddError(int $errorCode, string $errorMessage) : bool {
            ComponentStartOfFunctionEvent::SEND([&$errorCode, &$errorMessage]);

            $returnValue = $this->errorHandler->AddError($errorCode, $errorMessage);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$errorCode, $errorMessage]);
        }
    }


?>