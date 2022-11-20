<?php
    declare(strict_types=1);

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");

    use sbf\components\Component;
    use sbf\extensions\debugging\DebuggingExtension;

    use function sbf\debugging\dprint;
    use function sbf\debugging\dtprint;


    $parent = new Component("parent", null, new DebuggingExtension("debuggingExtension"));

    dprint($parent);
    dtprint($parent->Dump(false));


?>