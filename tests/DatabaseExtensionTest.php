<?php




    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");

    use sbf\components\value\ValueComponent;
    use sbf\components\value\arrays\ValueComponentArray;

    use sbf\extensions\debugging\DebuggingExtension;
    use sbf\extensions\validate\value\ValidateValueExtension;

    use function sbf\debugging\dtprint;

    use sbf\extensions\database\connection\mysqli\MysqliDatabaseConnectionExtension;

/*    
    $htmlElement = new ValueComponentArray(
        "htmlElements",
        [
            new ValueComponent("id", "", null, new ValidateValueExtension("validateValueExtension")),
            new ValueComponent("name"),
            new ValueComponent("email")
        ],
        [
            new MysqliDatabaseExtension(
                "mysqliDatabaseExtension",
                $databaseLogin["hostName"],
                $databaseLogin["userName"],
                $databaseLogin["password"],
                "ynwildlife"    
            ),

            new DebuggingExtension("debuggingExtension")
        ]
    );


    dtprint($htmlElement);

    dtprint("results: ", ($results = $htmlElement->Query("SELECT * FROM permits")), "\n");
    
    dtprint("results->FetchAll()", $results->FetchAll(), "\n");

    dtprint($htmlElement->GetComponentStructure());
*/

$dbExtension = new MysqliDatabaseConnectionExtension(
    "mysqliDatabaseExtension",
    $databaseLogin["hostName"],
    $databaseLogin["userName"],
    $databaseLogin["password"],
    "ynwildlife"    
);

dtprint("results: ", ($results = $dbExtension->Query("SELECT * FROM permits")), "\n");
dtprint("results->FetchAll()", $results->FetchAll(), "\n");

dtprint($dbExtension);
    


?>