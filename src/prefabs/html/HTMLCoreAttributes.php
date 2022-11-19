<?php
    //https://www.w3.org/TR/2010/WD-html-markup-20100624/common-attributes.html#common.attrs.core

    declare(strict_types=1);    

    namespace sbf\prefabs\html;

    error_reporting(E_ALL);
    ini_set('display_errors', '1'); 

    use sbf\components\Component;
    use sbf\components\value\ValueComponent;

    use sbf\extensions\arrayaccess\GetSetOverrideExtension;
    use sbf\extensions\debugging\DebuggingExtension;    

    use function sbf\debugging\dtprint;


    class HTMLCoreAttributes extends ValueComponent {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, null, $components, $extensions, $errorHandler);

            $this->AddComponent(new ValueComponent("name"));
            $this->AddComponent(new ValueComponent("id"));
            $this->AddComponent(new ValueComponent("class"));
            $this->AddComponent(new ValueComponent("style"));
            $this->AddComponent(new ValueComponent("hidden"));
            $this->AddComponent(new ValueComponent("title"));

            $this->AddComponent(new ValueComponent("accesskey"));
            $this->AddComponent(new ValueComponent("contenteditable"));
            $this->AddComponent(new ValueComponent("contextmenu"));
            $this->AddComponent(new ValueComponent("dir"));
            $this->AddComponent(new ValueComponent("draggable"));
            $this->AddComponent(new ValueComponent("lang"));
            $this->AddComponent(new ValueComponent("spellcheck"));
            $this->AddComponent(new ValueComponent("tabindex"));

            $this->AddExtension(new GetSetOverrideExtension("GetSetOverrideExtension", "SetValue", "GetValue", "\\sbf\\components\\value\\ValueComponent"));
            $this->AddExtension(new DebuggingExtension("debuggingExtension"));
        }
    }

?>