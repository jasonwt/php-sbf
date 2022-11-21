<?php
    declare(strict_types=1);
    
    namespace sbf\extensions\database\connection\mysqli;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandlers\ErrorHandler;

    use sbf\extensions\database\connection\DatabaseConnectionResults;
    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionExtension;
    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionResultsInterface;

    class MysqliDatabaseConnectionResults extends DatabaseConnectionResults implements MysqliDatabaseConnectionResultsInterface {
        protected \mysqli_result $mysqliResults;
        public function __construct(MysqliDatabaseConnectionExtension $databaseInterface, \mysqli_result $mysqliResults, ?ErrorHandler $errorHandler = null) {
            parent::__construct($databaseInterface, $errorHandler);

            $this->mysqliResults = $mysqliResults;
        }
        public function NumRows(): string { 
            return $this->mysqliResults->num_rows;
        }

        public function FetchAll(int $fetchMode = 0): array {
            if (!in_array($fetchMode, $this->GetAvailableFetchModes())) {
                $this->AddError(E_USER_WARNING, "Invalid fetchMode '$fetchMode'.  Using FETCH_MODE_BOTH.");
                $fetchMode = self::FETCH_MODE_BOTH;
            }

            return $this->mysqliResults->fetch_all([MYSQLI_BOTH, MYSQLI_ASSOC, MYSQLI_NUM][$fetchMode]);
        }

        public function FetchArray(int $fetchMode = 0) : ?array {
            if (!in_array($fetchMode, $this->GetAvailableFetchModes())) {
                $this->AddError(E_USER_WARNING, "Invalid fetchMode '$fetchMode'.  Using FETCH_MODE_BOTH.");
                $fetchMode = self::FETCH_MODE_BOTH;
            }

            return $this->mysqliResults->fetch_array([MYSQLI_BOTH, MYSQLI_ASSOC, MYSQLI_NUM][$fetchMode]);
        }

        public function FetchAssoc() : ?array { 
            return $this->mysqliResults->fetch_assoc();
        }

        public function FetchRow() : ?array {
            return $this->mysqliResults->fetch_row();
        }

        public function FetchObject(string $className = "stdClass", array $constructorArguments = null) {
            if ($className == "stdClass")
                return $this->mysqliResults->fetch_object();

            return $this->mysqliResults->fetch_object($className, $constructorArguments);
        }
    }
?>