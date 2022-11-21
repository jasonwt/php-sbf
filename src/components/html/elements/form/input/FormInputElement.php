<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\input;
    use sbf\components\html\attributes\HTMLAttribute;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\elements\form\FormElement;
    use sbf\components\html\elements\form\input\FormInputElementInterface;

    abstract class FormInputElement extends FormElement implements FormInputElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("type"), "name");
            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("value"), "id");
        }
        /**
         * @return string
         */
        public function GetTag(): string {
            return "input";
        }

        public function GetClosingHTML() : string {
            return "";
        }
    }

?>