<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\form;
    use sbf\components\arrayaccess\ArrayAccessComponent;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandlers\ErrorHandler;
    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\form\FormElement;
    use sbf\components\html\elements\form\FormValueElement;
    use sbf\components\html\elements\form\form\FormFormElementInterface;

    use sbf\components\html\elements\form\input\hidden\FormInputHiddenElement;
    use sbf\components\html\elements\form\input\text\FormInputTextElement;
    use sbf\components\html\elements\form\select\FormSelectElement;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

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

        public function GetElementValue(string $elementName) {
            $returnValue = null;

            if ((ComponentStartOfFunctionEvent::SEND([&$elementName])) !== false) {
                if (!is_null($element = $this->GetComponent("elements")->GetComponent($elementName)))
                    $returnValue = $element->GetValue();                 
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue,[$elementName]);
        }
        public function SetElementValue(string $elementName, $elementValue) {
            $returnValue = null;

            if ((ComponentStartOfFunctionEvent::SEND([&$elementName,&$elementValue])) !== false) {
                if (!is_null($element = $this->GetComponent("elements")->GetComponent($elementName)))
                    $element->SetValue($elementValue);
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue,[$elementName,$elementValue]);
        }

        public function AddInputTextElement(string $name, string $value = "", string $placeHolder = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement {
            $returnValue = null;

            if ((ComponentStartOfFunctionEvent::SEND([&$name, &$value, &$placeHolder, &$components, &$extensions, &$errorHandler])) !== false) {
                if (!is_null($newElement = $this->GetComponent("elements")->AddComponent(new FormInputTextElement($name, $value, $components, $extensions, $errorHandler)))) {
                    if ($placeHolder)
                        $newElement->GetComponent("attributes")->GetComponent("placeholder")->SetValue($placeHolder);

                    $returnValue = $this;
                } 
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue,[$name, $value, $placeHolder, $components, $extensions, $errorHandler]);
        }
        public function AddInputHiddenElement(string $name, string $value = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement {
            $returnValue = null;

            if ((ComponentStartOfFunctionEvent::SEND([&$name, &$value, &$components, &$extensions, &$errorHandler])) !== false) {
                if (!is_null($newElement = $this->GetComponent("elements")->AddComponent(new FormInputHiddenElement($name, $value, $components, $extensions, $errorHandler))))                    
                    $returnValue = $this;                
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue,[$name, $value, $components, $extensions, $errorHandler]);

        }
        public function AddInputSubmitElement(string $name, string $value = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement {

        }
        public function AddInputCheckboxElement(string $name, string $value = "", $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement {

        }
        public function AddSelectElement(string $name, string $value = "", array $selectOptions, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) : ?FormFormElement {
            $returnValue = null;

            if ((ComponentStartOfFunctionEvent::SEND([&$name, &$value, &$selectOptions, &$components, &$extensions, &$errorHandler])) !== false) {
                if (!is_null($newElement = $this->GetComponent("elements")->AddComponent(new FormSelectElement($name, $value, $selectOptions, $components, $extensions, $errorHandler))))                    
                    $returnValue = $this;                
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue,[$name, $value, $selectOptions, $components, $extensions, $errorHandler]);
        }
        public function AddCustomElement(FormValueElement $element) : ?FormFormElement {

        }
    }

?>