<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttributes;
    use sbf\components\arrayaccess\ArrayAccessComponent;
    use sbf\components\html\elements\HTMLElementInterface;

    abstract class HTMLElement extends ArrayAccessComponent implements HTMLElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, null, null, null, $components, $extensions, $errorHandler);

            $this->AddComponent(new HTMLAttributes("attributes", null, null, $this->errorHandler));

            $this->GetComponent("attributes")->GetComponent("name")->SetValue($name);
            $this->GetComponent("attributes")->GetComponent("id")->SetValue($name);
        }

        public function GetInnerHTML() : string {
            $innerHTMLArray = [];

            foreach ($this["attributes"] as $attributeName => $attributeValue) {
                if (trim(strval($attributeValue)) != "")
                    $innerHTMLArray[] = $attributeName . "=\"" . $attributeValue . "\"";
            }

            return implode(" ", $innerHTMLArray);
        }
    }

?>