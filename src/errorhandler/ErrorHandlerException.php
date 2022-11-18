<?php
    declare (strict_types=1);

    namespace sbf\errorhandler;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    class ErrorHandlerException extends \Exception {
        public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
            parent::__construct($message, $code, $previous);
        }
        public function __toString() {
            return $this->getMessage();
        }
    }
?>