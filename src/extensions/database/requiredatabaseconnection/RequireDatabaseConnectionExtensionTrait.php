<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\requiredatabaseconnection;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\events\components\ComponentEndOfFunctionEvent;
    use sbf\events\components\ComponentStartOfFunctionEvent;
    
    use sbf\extensions\database\connection\DatabaseConnectionExtensionInterface;
    
    trait RequireDatabaseConnectionExtensionTrait {
        protected DatabaseConnectionExtensionInterface $databaseConnection;                         

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
                if (($returnValue = parent::InitExtension()) != false) {
                    $databaseConnectionsArray = $this->parent->GetExtensions("\\sbf\\extensions\\database\\connection\\DatabaseConnectionExtension");

                    if (count($databaseConnectionsArray) > 0) {
                        $this->databaseConnection = $databaseConnectionsArray[array_keys($databaseConnectionsArray)[0]];
                        $returnValue = true;
                    } else {
                        $this->AddError(E_USER_ERROR, "Could not find any loaded database connection extensions.");
                        $returnValue = false;
                    }
                }
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }        
    }

?>