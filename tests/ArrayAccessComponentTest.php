<?php

    declare(strict_types=1);

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");

    use sbf\components\Component;
    use sbf\components\arrayaccess\ArrayAccessComponent;
    use sbf\extensions\debugging\DebuggingExtension;

    use function sbf\debugging\dprint;
    use function sbf\debugging\dtprint;


    $parent = new ArrayAccessComponent("parent", null, null, null, null, new DebuggingExtension("debuggingExtension"));

    $parent["test"] = new Component("test");

    dprint($parent);
    dtprint($parent->Dump(false));


?>