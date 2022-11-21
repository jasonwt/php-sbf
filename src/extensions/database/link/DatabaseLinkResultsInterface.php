<?php
    declare(strict_types=1);
    
    namespace sbf\extensions\database\link;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    interface DatabaseLinkResultsInterface {
        const FETCH_MODE_BOTH = 0;
        const FETCH_MODE_ASSOC = 1;
        const FETCH_MODE_NUM = 2;

        public function NumRows() : string;
        
        public function FetchAll(int $fetchMode = self::FETCH_MODE_BOTH) : ?array;
        public function FetchArray(int $fetchMode = self::FETCH_MODE_BOTH) :?array;
        public function FetchAssoc() : ?array;
        public function FetchRow() : ?array;
        public function FetchObject(string $className, array $constructorArguments = []);
    }
    
?>