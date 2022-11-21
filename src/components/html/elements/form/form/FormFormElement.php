<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\form;
    use sbf\components\arrayaccess\ArrayAccessComponent;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\form\FormElement;
    use sbf\components\html\elements\form\form\FormFormElementInterface;

    class FormFormElement extends FormElement implements FormFormElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("action"), "class");
            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("target"), "class");
            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("method"), "class");
            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("enctype"), "class");

            $this->AddComponent(new ArrayAccessComponent(
                "elements", 
                "GetValue", 
                "SetValue", 
                "\\sbf\\components\\html\\elements\\form\\input\\text\\FormInputTextElement", 
                null, 
                null, 
                $errorHandler
            ));
            
        }
        /**
         * @return string
         */
        public function GetTag(): string {
            return "form";
        }
    }

?>