<?php
    declare(strict_types=1);
    
    namespace sbf\extensions\database\connection\mysqli;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandlers\ErrorHandler;
    
    use sbf\extensions\database\connection\DatabaseConnectionExtension;
    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionResults;
    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionExtensionInterface;

    class MysqliDatabaseConnectionExtension extends DatabaseConnectionExtension implements MysqliDatabaseConnectionExtensionInterface {              
        private ?\mysqli $mysqliLink = null;

        public function __construct(string $name, string $hostName = "", string $userName = "", string $password = "", string $database = "", int $port = 0, string $socket = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $hostName, $userName, $password, $database, $port, $socket, $components, $extensions, $errorHandler);
        }

        protected function GetAvailableResultModes() : array {
            return [
                "RESULTS_MODE_STORE"      => self::RESULTS_MODE_STORE,
                "RESULTS_MODE_USE_RESULT" => self::RESULTS_MODE_USE_RESULT,
                "RESULTS_MODE_ASYNC"      => self::RESULTS_MODE_ASYNC
            ];
        } 
//
        public function IsConnected() : bool {
            return $this->mysqliLink != null;
        }
//
        protected function PrepareFunction() : bool {
            if (!$this->IsConnected()) {
                $this->AddError(E_USER_ERROR, "-1:Database Not Connected.");

                return false;
            }
                
            $this->ClearErrors();
            
            return true;
        }            
//
        public function EscapeString(string $str) : string {
            if (!$this->PrepareFunction()) 
                return $str;

            return $this->mysqliLink->real_escape_string($str);
        }
//
        public function Connect(string $hostName = "", string $userName = "", string $password = "", string $database = "", int $port = 0, string $socket = "") : bool {
            if ($this->IsConnected())
                $this->Close();

            if ($hostName == "")
                $hostName = ini_get("mysqli.default_host");

            if ($userName == "")
                $userName = ini_get("mysqli.default_user");

            if ($password == "")
                $password = ini_get("mysqli.default_pw");

            if (!$port)
                $port = ini_get("mysqli.default_port");

            if ($socket == "")
                $socket = ini_get("mysqli.default_socket");

            $this->mysqliLink = new \mysqli($hostName, $userName, $password, $database, intval($port), $socket);

            if ($this->mysqliLink->connect_errno) {
                $this->AddError(E_USER_ERROR, $this->mysqliLink->connect_errno . ":" . $this->mysqliLink->connect_error);

                $this->mysqliLink = null;
                
                return false;
            }

            return true;
        }
//
        public function SelectDatabase(string $databaseName) : bool {
            if (!$this->PrepareFunction())
                return false;                

            if (!$this->mysqliLink->select_db($databaseName)) {
                $this->AddError(E_USER_ERROR, $this->mysqliLink->errno . ":" . $this->mysqliLink->error);

                return false;
            }
                
            return true;
        }            
//
        public function Close() : bool {
            if (!$this->IsConnected())
                return false;                

            $returnStatus = $this->mysqliLink->close();

            $this->mysqliLink = null;

            return $returnStatus;
        }
// 
        public function Query(string $query, int $resultsMode = self::RESULTS_MODE_STORE) : ?MysqliDatabaseConnectionResults {            
            if (!in_array($resultsMode, $this->GetAvailableResultModes())) {
                $this->AddError(E_USER_WARNING, "Invalid resultsMode '$resultsMode'.  Using RESULTS_MODE_STORE.");
                $resultsMode = self::RESULTS_MODE_STORE;
            }
 
            if (!$this->PrepareFunction())
                return null;

            if (($mysqliResults = $this->mysqliLink->query($query, [MYSQLI_STORE_RESULT, MYSQLI_USE_RESULT, MYSQLI_ASYNC][$resultsMode])) === false) {
                $this->AddError(E_USER_ERROR, $this->mysqliLink->errno . ":" . $this->mysqliLink->error);
                return null;
            }

            return new MysqliDatabaseConnectionResults($this, $mysqliResults, $this->errorHandler);

//            return (is_bool($mysqliResults) ? $mysqliResults : new MysqliDatabaseResults($mysqliResults));
        }
//
        public function InsertId() : string {                
            if (!$this->PrepareFunction())
                return "";

            return strval($this->mysqliLink->insert_id);                
        }
//
        public function AffectedRows() : string {
            if (!$this->PrepareFunction())
                return "";

            return strval($this->mysqliLink->affected_rows);                
        } 
    }
?>