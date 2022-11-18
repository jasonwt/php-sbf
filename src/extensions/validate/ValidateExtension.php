<?php
    declare(strict_types=1);

    namespace sbf\extensions\validate;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\Extension;

    abstract class ValidateExtension extends Extension {
        public function __construct(string $name, $components = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $errorHandler);
        }
        
        abstract public function Validate();
    }

?>