<?php
    declare(strict_types=1);

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");

    use sbf\components\Component;
    use sbf\components\value\ValueComponent;
    use sbf\components\arrayaccess\ArrayAccessComponent;
    use sbf\extensions\debugging\DebuggingExtension;

    use function sbf\debugging\dprint;
    use function sbf\debugging\dtprint;


    $parent = new ArrayAccessComponent("parent", null, null, null, null, new DebuggingExtension("debuggingExtension"));
    dtprint($parent->Dump(false));

    $parent["child1"] = "child1value";
    $parent["child2"] = "child2value";
    $parent["child3"] = "child3value";
    $parent["child2"] = "child2valueupdated";
    //$parent["child1"] = new ValueComponent("child1", "child1");
    //$parent["child2"] = new Component("child2");
    //$parent["child2"] = new ValueComponent("child2", "child2Updated");
    //$parent["child2"] = new Component("child2");
    //$parent["child2"] = "child2";


    dtprint($parent->Dump(false));

    dtprint("count: " . count($parent) . "\n");

    foreach ($parent as $k => $v) {
        dtprint("$k: ", $v, "\n", $parent[$k], "\n");
    }
    


?>