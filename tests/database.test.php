<?php
    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");

    require_once(__DIR__ . "/database.login.php");

    use sbf\database\mysqli\MysqliDatabase;

    use function sbf\debugging\dtprint;

    $mysqliLink = new MysqliDatabase();

    dtprint("mysqliLink->connect(): ", $mysqliLink->Connect($databaseLogin["hostname"], $databaseLogin["username"], $databaseLogin["password"], "ynwildlife"), "\n");

    $results = $mysqliLink->Query("SELECT * FROM permits");

    dtprint("results: ", $results, "\n");
    
    dtprint("results->FetchAll()", $results->FetchAll(), "\n");
    
    dtprint($mysqliLink);

?>