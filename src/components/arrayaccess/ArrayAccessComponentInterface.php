<?php
    declare(strict_types=1);

    namespace sbf\components\arrayaccess;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\ComponentInterface;

    interface ArrayAccessComponentInterface extends ComponentInterface, \ArrayAccess, \Iterator, \Countable {    
    }
?>