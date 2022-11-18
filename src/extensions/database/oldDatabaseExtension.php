<?php
    declare(strict_types=1);

    namespace sbf\extensions\database;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\Extension;

    use sbf\database\DatabaseInterface;
    use sbf\database\DatabaseResultsInterface;

    class DatabaseExtension extends Extension implements DatabaseInterface {
        protected DatabaseInterface $databaseInterface;

        public function __construct(string $name, DatabaseInterface $databaseInterface, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->databaseInterface = $databaseInterface;
        }

        protected function AddDatabaseInterfaceErrors() {
            while ($error = $this->databaseInterface->GetError()) {            
                list ($errorCode, $errorMessage) = explode(":", $error);

                $this->AddError(intval($errorCode), $errorMessage);
            }
        }

    	/**
         * @param string $hostName
         * @param string $userName
         * @param string $password
         * @param string $database
         * @param int $port
         * @param string $socket
         * @return bool
         */
        public function Connect(string $hostName, string $userName, string $password, string $database, int $port, string $socket): bool {
            $returnValue =  $this->databaseInterface->Connect($hostName, $userName, $password, $database, $port, $socket);

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         * @return bool
         */
        public function IsConnected(): bool {
            $returnValue = $this->databaseInterface->IsConnected();

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         *
         * @param string $databaseName
         * @return bool
         */
        public function SelectDatabase(string $databaseName): bool {
            $returnValue = $this->databaseInterface->SelectDatabase($databaseName);

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         * @return bool
         */
        public function Close(): bool {
            $returnValue = $this->databaseInterface->Close();

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         *
         * @param string $str
         * @return string
         */
        public function EscapeString(string $str): string {
            $returnValue = $this->databaseInterface->EscapeString($str);

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         * @return string
         */
        public function InsertId(): string {
            $returnValue = $this->databaseInterface->InsertId();

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         * @return string
         */
        public function AffectedRows(): string {
            $returnValue = $this->databaseInterface->AffectedRows();

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
        
        /**
         *
         * @param string $query
         * @param int $resultsMode
         * @return DatabaseResultsInterface|null
         */
        public function Query(string $query, int $resultsMode = DatabaseInterface::RESULTS_MODE_STORE): ?DatabaseResultsInterface {
            $returnValue = $this->databaseInterface->Query($query, $resultsMode);

            $this->AddDatabaseInterfaceErrors();

            return $returnValue;
        }
    }
?>