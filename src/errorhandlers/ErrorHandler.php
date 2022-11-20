<?php
    declare (strict_types=1);

    namespace sbf\errorhandlers;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');    

    use sbf\errorhandlers\ErrorHandlerInterface;
    use sbf\traits\errorhandlers\ErrorHandlerTrait;
    
    class ErrorHandler implements ErrorHandlerInterface {
        use ErrorHandlerTrait;
    }
?>