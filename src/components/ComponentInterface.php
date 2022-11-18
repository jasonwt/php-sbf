<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    interface ComponentInterface extends \ArrayAccess, \Iterator, \Countable{
        public function GetError(?int $errorIndex = null) : ?string;
        public function GetErrors() : array;
        public function GetErrorCount() : int;
        public function GetName() : string;
        public function GetParent() : ?ComponentInterface;
        public function CanCall(string $methodName) : bool;
    }
?>