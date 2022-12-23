<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\input;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\form\FormValueElement;
    use sbf\components\html\elements\form\input\FormInputElementInterface;

    abstract class FormInputElement extends FormValueElement implements FormInputElementInterface {
        public function __construct(string $name, string $value = "", $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $value, $components, $extensions, $errorHandler);

            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("type"), "name");
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