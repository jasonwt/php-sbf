<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\select;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\form\FormValueElement;
    use sbf\components\html\elements\form\select\FormSelectElementInterface;

    class FormSelectElement extends FormValueElement implements FormSelectElementInterface {
        protected array $selectOptionsArray = [];

        public function __construct(string $name, string $value = "", array $selectOptionsArray = [], $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $value, $components, $extensions, $errorHandler);

            $this->selectOptionsArray = $selectOptionsArray;
        }

        static public function GetArraySelectOptionsArray(array $arr, string $selected, bool $valueAsDescription) : array {
            $returnValue = [];

            foreach ($arr as $value => $description) {
                $value = ($valueAsDescription ? $description : $value);

                if ($selected != ""  && $value == $selected)
                    $returnValue[$value] = "<option value=\"$value\" SELECTED>$description</option>";
                else
                    $returnValue[$value] = "<option value=\"$value\">$description</option>";
            }

            return $returnValue;
        }

        public function GetInnerHTML() : string {
            $innerHTMLArray = [];

            foreach ($this["attributes"] as $attributeName => $attributeValue) {
                if (trim(strval($attributeValue)) != "" && $attributeName != "value")
                    $innerHTMLArray[] = $attributeName . "=\"" . $attributeValue . "\"";
            }

            return implode(" ", $innerHTMLArray);
        }

        /**
         * @return string
         */
        public function GetTag(): string {
            return "select";
        }

        public function GetClosingHTML() : string {
            $selectValue = $this->GetComponent("attributes")->GetComponent("value")->GetValue();

            return implode("\n", static::GetArraySelectOptionsArray($this->selectOptionsArray, $selectValue, false)) . "\n<select>\n";            
        }
    }

?>