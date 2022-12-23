<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\tablefield;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;
    use sbf\errorhandlers\ErrorHandler;
    use sbf\extensions\database\tablefield\DatabaseTableFieldWriterExtensionInterface;

    class DatabaseTableFieldWriterExtension extends Extension implements DatabaseTableFieldWriterExtensionInterface {
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHander = null) {
            parent::__construct($name, $components, $extensions, $errorHander);
        }

        public function GetRequiredExtensions() : array {
            return [
                "\\sbf\\extensions\\database\\connection\\DatabaseConnectionExtension" => [
                    "exactExtensionClassName" => false,
                    "attemptToAutoLoad" => false
                ]
            ];
        }

        public function GetVersion() : string {
            return ("0.0.5");
        }
    }

?>