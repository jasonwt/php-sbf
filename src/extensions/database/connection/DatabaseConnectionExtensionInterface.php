<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\connection;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\extensions\database\connection\DatabaseConnectionResultsInterface;
    use sbf\extensions\ExtensionInterface;

    interface DatabaseConnectionExtensionInterface extends ExtensionInterface {
        const RESULTS_MODE_STORE = 0;

        public function Connect(string $hostName, string $userName, string $password, string $database, int $port, string $socket) : bool;
        public function IsConnected() : bool;
        public function SelectDatabase(string $databaseName) : bool;
        public function Close() : bool;

        public function EscapeString(string $str) : string;
        public function InsertId() : string;
        public function AffectedRows() : string;

        public function Query(string $query, int $resultsMode = self::RESULTS_MODE_STORE) : ?DatabaseConnectionResultsInterface;
    }

?>