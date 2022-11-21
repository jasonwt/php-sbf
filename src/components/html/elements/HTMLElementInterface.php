<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\arrayaccess\ArrayAccessComponentInterface;

    interface HTMLElementInterface extends ArrayAccessComponentInterface {
        public function GetInnerHTML() : string;
        public function GetTag() : string;
        public function GetHTML() : string;
        public function GetOpeningHTML() : string;
        public function GetClosingHTML() : string;
    }
    
?>