<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\link\mysqli;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\extensions\database\link\DatabaseLinkExtensionInterface;    

    interface MysqliDatabaseLinkExtensionInterface extends DatabaseLinkExtensionInterface {
        const RESULTS_MODE_USE_RESULT = 1;
        const RESULTS_MODE_ASYNC = 2;    
    }

?>