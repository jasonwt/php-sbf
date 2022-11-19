<?php
    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");
   
    use function sbf\debugging\dtprint;

    use sbf\prefabs\html\HTMLCoreAttributes;

    $obj = new HTMLCoreAttributes("htmlAttributes", new HTMLCoreAttributes("sub"));
/*    
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
*/
    

//    $obj["name"] = new ValueComponent("name", "jasddon");

//    $obj["name"] = "Jasona";

    //dtprint($obj->GetComponentStructure());

//    dtprint($obj);



//$obj["sub"]["name"] = "Jason";



print_r($obj->GetErrors());

echo $obj["sub"]["name"];

print_r($obj["sub"]);


    //dtprint($obj->GetComponentStructure());

    die();

    foreach ($obj as $k => $v) {
       dtprint($k . ": ", gettype($obj[$k]) . ":" . print_r($obj[$k], true), "\n");
    }
    
    echo "name: " . $obj["name"]["name"];


?>