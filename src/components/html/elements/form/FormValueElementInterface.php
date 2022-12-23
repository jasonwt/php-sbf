<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\elements\form\FormElementInterface;

    interface FormValueElementInterface extends FormElementInterface {
        public function GetValue();
        public function SetValue($value);
    }
    
?>