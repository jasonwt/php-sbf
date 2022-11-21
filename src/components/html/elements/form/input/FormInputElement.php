<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\input;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\elements\form\FormElement;
    use sbf\components\html\elements\form\input\FormInputElementInterface;

    abstract class FormInputElement extends FormElement implements FormInputElementInterface {
        /**
         * @return string
         */
        public function GetTag(): string {
            return "input";
        }
    }

?>