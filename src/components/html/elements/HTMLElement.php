<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttributes;
    use sbf\components\arrayaccess\ArrayAccessComponent;
    use sbf\components\html\elements\HTMLElementInterface;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    abstract class HTMLElement extends ArrayAccessComponent implements HTMLElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, null, null, null, $components, $extensions, $errorHandler);

            $this->AddComponent(new HTMLAttributes("attributes", null, null, $this->errorHandler));

            $this->GetComponent("attributes")->GetComponent("name")->SetValue($name);
            $this->GetComponent("attributes")->GetComponent("id")->SetValue($name);
        }

        public function SetAttributeValue(string $attributeName, $attributeValue) : ?HTMLElement {
            $returnValue = null;

            if (ComponentStartOfFunctionEvent::SEND([&$attributeName, &$attributeValue]) !== false) {
                if (!in_array($attributeName, $this->GetComponent("attributes")->GetComponentNames())) {
                    $this->AddError(E_USER_ERROR, "attributeName '$attributeName' does not exist.");
                    $returnValue = null;
                } else {
                    $this->GetComponent("attributes")->GetComponent($attributeName)->SetValue($attributeValue);
                    $returnValue = $this;
                }
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$attributeName, $attributeValue]);
        }

        public function GetAttributeValue(string $attributeName) {
            $returnValue = null;

            if (ComponentStartOfFunctionEvent::SEND([&$attributeName]) !== false) {
                if (!in_array($attributeName, $this->GetComponent("attributes")->GetComponentNames()))
                    $this->AddError(E_USER_ERROR, "attributeName '$attributeName' does not exist.");
                else
                    $returnValue = $this->GetComponent("attributes")->GetComponent($attributeName)->GetValue();
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$attributeName]);
        }

        public function GetInnerHTML() : string {
            $innerHTMLArray = [];

            foreach ($this["attributes"] as $attributeName => $attributeValue) {
                if (trim(strval($attributeValue)) != "")
                    $innerHTMLArray[] = $attributeName . "=\"" . $attributeValue . "\"";
            }

            return implode(" ", $innerHTMLArray);
        }

        public function GetOpeningHTML() : string {
            return "<" . $this->GetTag() . " " . $this->GetInnerHTML() . ">";
        }

        public function GetClosingHTML() : string {
            return "</" . $this->GetTag() . ">";
        }

        public function GetHTML() : string {
            $returnValue = $this->GetOpeningHTML() . "\n";

            if (($closingHTML = $this->GetClosingHTML()) != "")
                $returnValue .= $closingHTML . "\n";

            return $returnValue;
        }
    }

?>