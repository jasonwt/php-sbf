<?php
    declare(strict_types=1);    

    namespace sbf\components\html\attributes;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\arrayaccess\ArrayAccessComponentInterface;

    interface HTMLAttributesInterface extends ArrayAccessComponentInterface {
        
    }
    
?>