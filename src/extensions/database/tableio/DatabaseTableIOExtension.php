<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\tableio;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;

    use sbf\extensions\database\tableio\DatabaseTableIOExtensionInterface;
    

    class DatabaseTableIOExtension extends Extension implements DatabaseTableIOExtensionInterface {
        protected string $databaseName = "";
        protected string $tableName = "";

        public function __construct(string $name, string $databaseName, string $tableName, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            if (($this->databaseName = trim($databaseName)) == "")
                $this->AddError(E_USER_ERROR, "databaseName must be a valid database name.");

            if (($this->tableName = trim($tableName)) == "")
                $this->AddError(E_USER_ERROR, "tableName must be a valid table name.");
        }
    }

?>