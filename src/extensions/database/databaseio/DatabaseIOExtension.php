<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\databaseio;
    
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;

    use sbf\events\components\ComponentEndOfFunctionEvent;
    use sbf\events\components\ComponentStartOfFunctionEvent;
    
    use sbf\extensions\database\connection\DatabaseConnectionExtension;
    use sbf\extensions\database\databaseio\DatabaseIOExtensionInterface;
    use sbf\extensions\database\connection\DatabaseConnectionResultsInterface;
    use sbf\extensions\database\connection\DatabaseConnectionExtensionInterface;
    
    class DatabaseIOExtension extends Extension implements DatabaseIOExtensionInterface {
        protected DatabaseConnectionExtensionInterface $databaseConnection; 
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

        protected function InitExtension() : bool {
            if (($returnValue = ComponentStartOfFunctionEvent::SEND()) != false) {
                $databaseConnectionsArray = $this->parent->GetExtensions("\\sbf\\extensions\\database\\connection\\DatabaseConnectionExtension");

                if (count($databaseConnectionsArray) > 0) {
                    $this->databaseConnection = $databaseConnectionsArray[array_keys($databaseConnectionsArray)[0]];
                    $returnValue = true;
                } else {
                    $this->AddError(E_USER_ERROR, "Could not find any loaded database connection extensions.");
                    $returnValue = false;
                }
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }

        public function Query(string $query, int $resultsMode = DatabaseConnectionExtensionInterface::RESULTS_MODE_STORE) : ?DatabaseConnectionResultsInterface {
            return $this->databaseConnection->Query($query, $resultsMode);            
        }

        public function SelectDatabase(string $databaseName) : bool {
            $this->AddError(E_USER_NOTICE, "Can not change selected database with DatabaseIOExtension.");

            return false;
        }
    }

?>