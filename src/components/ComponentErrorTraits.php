<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\extensions\Extension;

    trait ComponentErrorTraits {
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

            return $this->ProcessHook("ClearError_FRHOOK", [$this, $this->errorHandler->ClearError($errorIndex), $errorIndex]);
        }
        protected function ClearErrors() {
            $this->ProcessHook("ClearErrors_FIHOOK", [$this]);

            $this->ProcessHook("ClearErrors_FRHOOK", [$this, $this->errorHandler->ClearErrors()]);
        }

        protected function AddError(int $errorCode, string $errorMessage) : bool {
            $this->ProcessHook("AddError_FIHOOK", [$this, &$errorCode, &$errorMessage]);

            return $this->ProcessHook("AddError_FRHOOK", [$this, $this->errorHandler->AddError($errorCode, $errorMessage), $errorCode, $errorMessage]);
        }
    }


?>