<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\select;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\elements\form\FormValueElementInterface;

    interface FormSelectElementInterface extends FormValueElementInterface {
        static public function GetArraySelectOptionsArray(array $arr, string $selected, bool $valueAsDescription) : array;
    }
    
?>