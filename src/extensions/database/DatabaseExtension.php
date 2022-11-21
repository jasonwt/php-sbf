<?php
    declare(strict_types=1);

    namespace sbf\extensions\database;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\errorhandlers\ErrorHandler;
    use sbf\extensions\Extension;

    use sbf\extensions\database\DatabaseExtensionInterface;

    abstract class DatabaseExtension extends Extension implements DatabaseExtensionInterface {
        public function __construct(string $name, string $hostName = "", string $userName = "", string $password = "", string $database = "", int $port = 0, string $socket = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            if (($hostName = trim($hostName)) && ($userName = trim($userName)) && ($password = trim($password)))
                $this->Connect($hostName, $userName, $password, $database, $port, $socket);
        }
//
        protected function GetAvailableResultModes() : array {
            return [
                "RESULTS_MODE_STORE" => self::RESULTS_MODE_STORE
            ];
        }        
    }

?>