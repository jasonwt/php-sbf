<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\input\text;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\form\input\FormInputElement;
    use sbf\components\html\elements\form\input\text\FormInputTextElementInterface;

    class FormInputTextElement extends FormInputElement implements FormInputTextElementInterface {
    	public function __construct(string $name, string $value = "", $components = null, $extensions = null, $errorHandler = null) {


            parent::__construct($name, $value, $components, $extensions, $errorHandler);

            $this->GetComponent("attributes")->GetComponent("type")->SetValue("text");
            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("placeholder"), "accesskey");
        }
    }

?>