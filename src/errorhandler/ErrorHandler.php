<?php
    declare (strict_types=1);

    namespace sbf\errorhandler;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');    

    use sbf\errorhandler\ErrorHandlerInterface;
    use sbf\errorhandler\ErrorHandlerTrait;
    
    class ErrorHandler implements ErrorHandlerInterface {
        use ErrorHandlerTrait;
    }
?>