<?php
    declare(strict_types=1);

    namespace sbf\components\value;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\components\ComponentInterface;    

    interface ValueComponentInterface extends ComponentInterface {
        public function GetValue();
        public function SetValue($value);
    }
?>