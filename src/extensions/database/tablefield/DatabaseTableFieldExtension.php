<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\tablefield;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;

    use sbf\extensions\database\tablefield\DatabaseTableFieldExtensionInterface;
    

    class DatabaseTableFieldExtension extends Extension implements DatabaseTableFieldExtensionInterface {
        protected string $tableName = "";
        protected string $fieldName = "";

        public function __construct(string $name, string $tableName, string $fieldName = "", $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            if (($this->tableName = trim($tableName)) == "")
                $this->AddError(E_USER_ERROR, "tableName must be a valid table name.");            

            $this->fieldName = trim($fieldName);
        }

        public function GetVersion() : string {
            return ("0.0.5");
        }

        protected function InitExtension() : bool {
            if (!parent::InitExtension())
                return false;

            if ($this->fieldName == "")
                $this->fieldName = $this->parent->name;

            return true;
        }

        public function GetTableName() : string {
            return $this->tableName;
        }

        public function GetFieldName() : string {
            return $this->fieldName;
        }
    }

?>