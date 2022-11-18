<?php
    declare(strict_types=1);

    namespace sbf\events;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    interface EventInterface {
        public function GetError(?int $errorIndex = null) : ?string;
        public function GetErrors() : array;
        public function GetErrorCount() : int;
        public function GetName() : string;
        public function GetParent() : ?ComponentInterface;
        public function CanCall(string $methodName) : bool;
    }
?>