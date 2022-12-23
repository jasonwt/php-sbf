<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\form\FormElement;
    use sbf\components\html\elements\form\FormValueElementInterface;

    abstract class FormValueElement extends FormElement implements FormValueElementInterface {
        public function __construct(string $name, string $value = "", $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("value", $value), "id");
        }

        public function GetValue() {
            return $this->GetComponent("attributes")->GetComponent("value")->GetValue() ;
        }

        public function SetValue($value) {
            return $this->GetComponent("attributes")->GetComponent("value")->SetValue($value);
        }
    }

?>