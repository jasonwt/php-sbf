<?php
    declare(strict_types=1);    

    namespace sbf\components\html\attributes;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\value\ValueComponent;

    class HTMLAttribute extends ValueComponent implements HTMLAttributeInterface {
        public function __construct(string $name, $value = null, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $value, $components, $extensions, $errorHandler);
        }
    }

?>