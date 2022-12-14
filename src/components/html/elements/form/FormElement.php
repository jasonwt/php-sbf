<?php
    declare(strict_types=1);    

    namespace sbf\components\html\elements\form;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\elements\HTMLElement;
    use sbf\components\html\elements\form\FormElementInterface;

    abstract class FormElement extends HTMLElement implements FormElementInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->GetComponent("attributes")->AddComponent(new HTMLAttribute("readonly"));
            
        }
    }

?>