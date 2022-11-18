<?php
    declare (strict_types=1);

    namespace sbf\errorhandler;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    interface ErrorHandlerInterface {
       public function GetError(?int $errorIndex = null) : ?string;
       public function GetErrors() : array;
       public function GetErrorCount() : int;
       public function ClearError(int $errorIndex) : bool;
       public function ClearErrors();
       public function AddError(int $errorCode, string $errorMessage) : bool;
    }
?>