<?php
    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");

    use sbf\components\value\arrays\ValueComponentArray;
    use sbf\extensions\validate\value\ValidateValueExtension;
    use sbf\extensions\debugging\DebuggingExtension;
    use sbf\components\value\ValueComponent;
    use sbf\components\Component;
    use function sbf\debugging\dtprint;

    $parent = new ValueComponentArray("parent",
        [
            new ValueComponent("child1"),
            new ValueComponent("child2", "defaultValue", null, new ValidateValueExtension("validateValue")),
            "child3" => "child3Value"
        ],
        [
            new DebuggingExtension("debuggingExtension")
        ]
    );

    //$parent["child1"] = new Component("child1");
//    $parent["child2"] = new ValueComponent("child2", "defaultValue");
  //  $parent->RegisterExtension(new DebuggingExtension("debuggingExtension"));

  $parent["child1"] = "a";
  $parent["child1"] = "b";
  $parent["child1"] = "c";
  $parent["child1"] = "d";


    foreach ($parent as $k => $v) {
        dtprint($k . ": ", $v, "\n\n");
    }

    dtprint("\n", $parent->GetComponentStructure(), "\n");

    
?>