<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\connection\mysqli;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\extensions\database\connection\DatabaseConnectionExtensionInterface;    

    interface MysqliDatabaseConnectionExtensionInterface extends DatabaseConnectionExtensionInterface {
        const RESULTS_MODE_USE_RESULT = 1;
        const RESULTS_MODE_ASYNC = 2;    
    }

?>