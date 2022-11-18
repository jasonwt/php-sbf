<?php
    declare(strict_types=1);

    namespace sbf\extensions\validate;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\Extension;

    abstract class ValidateExtension extends Extension {
        public function __construct(string $name, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $errorHandler);
        }
        
        abstract public function Validate();
    }

?>