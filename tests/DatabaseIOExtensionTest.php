<?php

    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");

    use sbf\components\Component;
    use sbf\extensions\database\databaseio\DatabaseIOExtension;

    use sbf\components\value\ValueComponent;
    use sbf\components\value\arrays\ValueComponentArray;

    use sbf\extensions\debugging\DebuggingExtension;
    use sbf\extensions\validate\value\ValidateValueExtension;

    use function sbf\debugging\dtprint;

    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionExtension;

    $dbExtension = new MysqliDatabaseConnectionExtension(
        "mysqliDatabaseExtension",
        $databaseLogin["hostName"],
        $databaseLogin["userName"],
        $databaseLogin["password"],
        "ynwildlife"    
    );

    $obj = new Component(
        "testcomponent",
        null,
        [
            $dbExtension,
            new DebuggingExtension("debuggingExtension"),
            new DatabaseIOExtension("databaseIOExtension", "ynwildlife")
        ]
    );


    dtprint($obj);

    $obj->SelectDatabase("something else");
    

    dtprint("results: ", ($results = $obj->Query("SELECT * FROM permits")), "\n");
    dtprint("results->FetchAll()", $results->FetchAll(), "\n");

    dtprint($obj->Dump(false));

    //dtprint($dbExtension);
?>