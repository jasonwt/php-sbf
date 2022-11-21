<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\databaseio;
    use sbf\extensions\database\connection\DatabaseConnectionExtension;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;

    use sbf\extensions\database\databaseio\DatabaseIOExtensionInterface;
    use sbf\extensions\database\connection\DatabaseConnectionResultsInterface;
    use sbf\extensions\database\connection\DatabaseConnectionExtensionInterface;
    
    class DatabaseIOExtension extends Extension implements DatabaseIOExtensionInterface {
        protected string $databaseName = "";
        protected string $tableName = "";

        public function __construct(string $name, string $databaseName, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            if (($this->databaseName = trim($databaseName)) == "")
                $this->AddError(E_USER_ERROR, "databaseName must be a valid database name.");
        }

        public function GetVersion() : string {
            return ("0.0.1");
        }

        static public function GetCanCallPriority() : int {
            return DatabaseConnectionExtension::GetCanCallPriority() + 1;
        }

        public function GetRequiredExtensions() : array {
            return [
                "\\sbf\\extensions\\database\\connection\\DatabaseConnectionExtension" => [
                    "exactExtensionClassName" => false,
                    "attemptToAutoLoad" => false
                ]
            ];
        }

        public function Query(string $query, int $resultsMode = DatabaseConnectionExtensionInterface::RESULTS_MODE_STORE) : ?DatabaseConnectionResultsInterface {
            
            $dbConnection = $this->parent->GetExtensions("\\sbf\\extensions\\database\\connection\\DatabaseConnectionExtension");

            return $dbConnection[array_keys($dbConnection)[0]]->Query($query, $resultsMode);
        }

        public function SelectDatabase(string $databaseName) : bool {
            $this->AddError(E_USER_NOTICE, "Can not change selected database with DatabaseIOExtension.");

            return false;
        }
    }

?>