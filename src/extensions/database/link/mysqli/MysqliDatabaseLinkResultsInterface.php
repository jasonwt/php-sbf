<?php
    declare(strict_types=1);

    namespace sbf\extensions\database\link\mysqli;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\extensions\database\link\DatabaseLinkResultsInterface;    

    interface MysqliDatabaseLinkResultsInterface extends DatabaseLinkResultsInterface { 
    }

?>