<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\tableio;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\extensions\Extension;

    use sbf\extensions\database\tableio\DatabaseTableIOExtensionInterface;
    

    class DatabaseTableIOExtension extends Extension implements DatabaseTableIOExtensionInterface {
        public function __construct(string $name, string $tableName, $components = null, $extensions = null, $errorHandler = null) {

        }
    }

?>