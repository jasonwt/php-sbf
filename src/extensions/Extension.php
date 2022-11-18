<?php
    declare(strict_types=1);

    namespace sbf\extensions;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;

    class Extension extends Component implements ExtensionInterface {
        public function __construct(string $name, $components = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $errorHandler);
        }

        protected function InitExtension() : bool {
            return true;
        }
    }

    
?>