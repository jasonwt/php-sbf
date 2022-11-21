<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\databaseio;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;

    use sbf\extensions\database\databaseio\DatabaseIOExtensionInterface;
    
    class DatabaseIOExtension extends Extension implements DatabaseIOExtensionInterface {
        protected string $databaseName = "";
        protected string $tableName = "";

        public function __construct(string $name, string $databaseName, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            if (($this->databaseName = trim($databaseName)) == "")
                $this->AddError(E_USER_ERROR, "databaseName must be a valid database name.");
        }
    }

?>