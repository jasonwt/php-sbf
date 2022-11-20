<?php
    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");
   
    use function sbf\debugging\dtprint;

    use sbf\extensions\debugging\DebuggingExtension;
    use sbf\components\html\elements\HTMLElement;
    use sbf\components\html\elements\form\input\text\HTMLFormInputText;

    $obj = new HTMLFormInputText(
        "asdf",
        null,
        [
            new DebuggingExtension("debuggingExtension")
        ]
    );

    //$obj["attributes"]["name"] = "Jason";

    $obj["attributes"]["draggable"] = "ASDFASDF";
    //unset($obj["attributes"]["name"]);

    dtprint($obj->Dump());

//    dtprint($obj["attributes"]["name"]);

    echo "\n" . $obj->GetInnerHTML();

    

    foreach ($obj as $k => $v) {
//       dtprint($k . ": ", gettype($obj[$k]) . ":" . print_r($obj[$k], true), "\n");
    }
    
    


?>