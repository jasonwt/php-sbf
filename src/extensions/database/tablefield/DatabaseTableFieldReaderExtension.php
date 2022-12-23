<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\tablefield;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;
    use sbf\errorhandlers\ErrorHandler;
    use sbf\extensions\database\tablefield\DatabaseTableFieldReaderExtensionInterface;
    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionResultsInterface;

    class DatabaseTableFieldReaderExtension extends Extension implements DatabaseTableFieldReaderExtensionInterface {
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

        public function TableFieldReaderQuery(string $query, string $tableName) {
            $results = $this->parent->Query("$query");

            $rows = $results->FetchAll(MysqliDatabaseConnectionResultsInterface::FETCH_MODE_ASSOC);

            if (count($rows) > 0) {
                foreach ($this->parent->GetComponents() as $componentName => $component) {
                    
                    if ($component->GetExtensions("\\sbf\\extensions\\database\\tablefield\\DatabaseTableFieldExtension")) {
                        if ($component->GetTableName() == $tableName) {
                            $dbFieldName = $component->GetFieldName();

                            if (isset($rows[0][$dbFieldName]))
                                $component->SetValue($rows[0][$dbFieldName]);

                            echo $componentName . ":$dbFieldName\n";
                        }
                    }
                }                
            }



//            print_r($rows);
  //          die();
        }
    }

?>