<?php
    declare(strict_types=1);

    namespace sbf\extensions\html;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\extensions\html\HTMLElementInterface;
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\Extension;

    abstract class HTMLElement extends Extension implements HTMLElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);
        }
        
        
        
    }

?>