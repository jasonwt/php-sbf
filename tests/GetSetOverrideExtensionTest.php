<?php
    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");

    use sbf\components\Component;
    use sbf\components\value\ValueComponent;

    use sbf\extensions\arrayaccess\GetSetOverrideExtension;
    use sbf\extensions\debugging\DebuggingExtension;    

    use function sbf\debugging\dtprint;


    $obj = new Component(
        "parent",
        [
            new ValueComponent("id", "defaultId"),
            new ValueComponent("name", "defaultName")
        ],
        [
            new GetSetOverrideExtension("GetSetOverrideExtension", "SetValue", "GetValue", "\\sbf\\components\\value\\ValueComponent"),
            new DebuggingExtension("debuggingExtension")
        ]
    );

    

    //$obj["name"] = new ValueComponent("name", "jason");

    $obj["name"] = "Jason";

    //dtprint($obj->GetComponentStructure());

//    dtprint($obj);

    dtprint($obj->GetComponentStructure());

    foreach ($obj as $k => $v) {
       dtprint($k . ": ", gettype($obj[$k]) . ":" . print_r($obj[$k], true), "\n");
    }
    


?>