<?php
    //https://www.w3.org/TR/2010/WD-html-markup-20100624/common-attributes.html#common.attrs.core

    declare(strict_types=1);    

    namespace sbf\components\html\elements;
    use sbf\components\value\ValueComponent;

    error_reporting(E_ALL);
    ini_set('display_errors', '1'); 

    use sbf\components\Component;
    use sbf\components\html\attributes\HTMLCoreAttributes;
    use sbf\components\html\elements\HTMLElementInterface;

    use sbf\events\components\ComponentEvent;
    use sbf\events\components\ComponentStartOfFunctionEvent;

    abstract class HTMLElement extends Component implements HTMLElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->AddComponent(new HTMLCoreAttributes("attributes"));

            $this->GetComponent("attributes")->GetComponent("name")->SetValue($name);
            $this->GetComponent("attributes")->GetComponent("id")->SetValue($name);

            $this->GetComponent("attributes")->AddComponent(new ValueComponent("value"), "id");
        }

        public function GetInnerHTML() : string {
            $innerHTMLArray = [];

            foreach ($this["attributes"] as $attributeName => $attributeValue) {
                if (trim(strval($attributeValue)) != "")
                    $innerHTMLArray[] = $attributeName . "=\"" . $attributeValue . "\"";
            }

            return implode(" ", $innerHTMLArray);
        }

        protected function HandleEvent(ComponentEvent $event) {

            if (in_array($event->caller, $this->components, true)) {
                if ($event instanceof ComponentStartOfFunctionEvent) {
                    if ($event->caller->name == "attributes") {
                        if ($event->name == "offsetSet") {                            
                            if ($event->arguments[0] == "id" || $event->arguments[0] == "name") {
                                $this->AddError(E_USER_WARNING, "Can not change the attribute value for name or id.");
                                $event->returnValue = false;
                            }                            
                        } else if ($event->name == "offsetUnset") {
                            $this->AddError(E_USER_WARNING, "Can not unset any attributes.");
                            $event->returnValue = false;
                        }
                    }
                }
            }

            return $event->returnValue;
        }
    }
?>