<?php
    declare(strict_types=1);    

    namespace sbf\components\html\attributes;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\arrayaccess\ArrayAccessComponent;
    use sbf\components\html\attributes\HTMLAttributesInterface;

    class HTMLAttributes extends ArrayAccessComponent implements HTMLAttributesInterface {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, "GetValue", "SetValue", "HTMLAttribute", $components, $extensions, $errorHandler);

            $this->AddComponent(new HTMLAttribute("name"));
            $this->AddComponent(new HTMLAttribute("id"));
            $this->AddComponent(new HTMLAttribute("class"));
            $this->AddComponent(new HTMLAttribute("style"));
            $this->AddComponent(new HTMLAttribute("hidden"));
            $this->AddComponent(new HTMLAttribute("title"));

            $this->AddComponent(new HTMLAttribute("accesskey"));
            $this->AddComponent(new HTMLAttribute("contenteditable"));
            $this->AddComponent(new HTMLAttribute("contextmenu"));
            $this->AddComponent(new HTMLAttribute("dir"));
            $this->AddComponent(new HTMLAttribute("draggable"));
            $this->AddComponent(new HTMLAttribute("lang"));
            $this->AddComponent(new HTMLAttribute("spellcheck"));
            $this->AddComponent(new HTMLAttribute("tabindex"));

            $this->AddComponent(new HTMLAttribute("onchange"));
            $this->AddComponent(new HTMLAttribute("onclick"));
            $this->AddComponent(new HTMLAttribute("onfocus"));

            
        }
    }
?>