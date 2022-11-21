<?php
    declare(strict_types=1);
    
    namespace sbf\extensions\database\connection;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\extensions\database\connection\DatabaseConnectionExtensionInterface;
    use sbf\extensions\database\connection\DatabaseConnectionResultsInterface;

    use sbf\errorhandlers\ErrorHandler;

    abstract class DatabaseConnectionResults implements DatabaseConnectionResultsInterface {
        protected ErrorHandler $errorHandler;
        protected DatabaseConnectionExtensionInterface $databaseInterface;

        public function __construct(DatabaseConnectionExtensionInterface $databaseInterface, ?ErrorHandler $errorHandler = null) {
            $this->errorHandler = (is_null($errorHandler) ? new ErrorHandler() : $errorHandler);

            $this->databaseInterface = $databaseInterface;
        }
        public function GetError(?int $errorIndex = null) : ?string {
            return $this->errorHandler->GetError($errorIndex);
        }
        public function GetErrors() : array {            
            return $this->errorHandler->GetErrors();
        }
        public function GetErrorCount() : int {
            return $this->errorHandler->GetErrorCount();
        }
        protected function ClearError(int $errorIndex) : bool {
            return $this->errorHandler->ClearError($errorIndex);
        }
        protected function ClearErrors() {
            $this->errorHandler->ClearErrors();
        }

        protected function AddError(int $errorCode, string $errorMessage) : bool {
            return $this->errorHandler->AddError($errorCode, $errorMessage);
        }
//
        protected function GetAvailableFetchModes() : array {
            return [
                "FETCH_MODE_BOTH" => self::FETCH_MODE_BOTH,
                "FETCH_MODE_ASSOC" => self::FETCH_MODE_ASSOC,
                "FETCH_MODE_NUM" => self::FETCH_MODE_NUM
            ];
        }        
//                        
        public function FetchArray(int $fetchMode = self::FETCH_MODE_BOTH) : ?array {                            
            if (is_null($row = $this->FetchAssoc()))
                return null;

            if (!in_array($fetchMode, $this->GetAvailableFetchModes())) {
                $this->AddError(E_USER_WARNING, "Invalid fetchMode '$fetchMode'.  Using FETCH_MODE_BOTH.");
                $fetchMode = self::FETCH_MODE_BOTH;
            }

            $resultsArray = array();

            foreach ($row as $k => $v) {
                if ($fetchMode == self::FETCH_MODE_NUM || $fetchMode == self::FETCH_MODE_BOTH)
                    $resultsArray[] = $v;

                if ($fetchMode == self::FETCH_MODE_ASSOC || $fetchMode == self::FETCH_MODE_BOTH)
                    $resultsArray[$k] = $v;
            }

            return $resultsArray;                    
        }
//
        public function FetchAll(int $fetchMode = self::FETCH_MODE_BOTH) : ?array {
            $resultsArray = array();

            while ($row = $this->FetchArray($fetchMode))
                $resultsArray[] = $row;

            return $resultsArray;
        }
//
        public function FetchObject(string $className, array $constructorArguments = []) {
            if (is_null($row = $this->FetchAssoc()))
                return null;

            return new $className($constructorArguments);
        }           
        
        public function FetchRow() : ?array {
            if (is_null($row = $this->FetchAssoc()))
                return null;

            return array_values($row);                
        }
    }
?>