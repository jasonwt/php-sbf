<?php
    declare (strict_types=1);

    namespace sbf\errorhandler;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandler\ErrorHandlerException;
    use sbf\debugging\Debugging;

    trait ErrorHandlerTrait {
        private array $errors = [];

        public function GetError(?int $errorIndex = null) : ?string {
            if (count($this->errors) == 0)
                return null;

            $errorRecord = null;

            if (is_null($errorIndex)) {
                $errorRecord = array_shift($this->errors);
            } else {
                if ($errorIndex < 0)
                    $errorIndex += count($this->errors);

                if ($errorIndex < 0 || $errorIndex >= count($this->errors)) {
                    $this->AddError(E_USER_WARNING, "errorIndex '$errorIndex' is out of range.");
                    return null;
                }

                $errorRecord = $this->errors[$errorIndex];
            }

            return $errorRecord["errorCode"] . ":" . $errorRecord["errorMessage"];
        }
        public function GetErrors() : array {
            return $this->errors;
        }
        public function GetErrorCount() : int {
            return count($this->errors);
        }
        public function ClearError(int $errorIndex) : bool {
            if ($errorIndex < 0)
                $errorIndex += count($this->errors);

            if ($errorIndex < 0 || $errorIndex >= count($this->errors)) {
                $this->AddError(E_USER_WARNING, "errorIndex '$errorIndex' is out of range.");
                return false;
            }

            unset($this->errors[$errorIndex]);

            return true;
        }
        public function ClearErrors() {
            $this->errors = [];
        }
        public function AddError(int $errorCode, string $errorMessage) : bool {
            $errorMessage = Debugging::DString(2, 0, [$errorMessage]);
            
            if ($errorCode != E_ERROR && $errorCode != E_USER_ERROR) {
                $this->errors[] = [
                    "errorCode" => $errorCode,
                    "errorMessage" => $errorMessage
                ];
    
                return true;
            }
                
            throw new ErrorHandlerException($errorMessage, $errorCode);
        }
    }
?>