<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\form;

    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\elements\form\FormElementInterface;
    use sbf\components\html\elements\form\FormValueElement;
    use sbf\errorhandlers\ErrorHandler;

    interface FormFormElementInterface extends FormElementInterface {
        public function AddInputTextElement(string $name, string $value = "", string $placeHolder = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement;
        public function AddInputHiddenElement(string $name, string $value = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement;
        public function AddInputSubmitElement(string $name, string $value = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement;
        public function AddInputCheckboxElement(string $name, string $value = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement;
        public function AddSelectElement(string $name, string $value = "", array $selectOptions, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement;
        public function AddCustomElement(FormValueElement $element) : ?FormFormElement;

        public function GetElementValue(string $elementName);
        public function SetElementValue(string $elementName, $elementValue);

    }
    
?>