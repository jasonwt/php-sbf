<?php
    //https://www.w3.org/TR/2010/WD-html-markup-20100624/common-attributes.html#common.attrs.core

    declare(strict_types=1);    

    namespace sbf\components\html\elements\form\input\text;
    

    error_reporting(E_ALL);
    ini_set('display_errors', '1'); 

    use sbf\components\value\ValueComponent;
    use sbf\components\html\elements\form\input\HTMLFormInput;
    use sbf\components\html\elements\form\input\text\HTMLFormInputTextInterface;

    class HTMLFormInputText extends HTMLFormInput implements HTMLFormInputTextInterface {
        
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this["attributes"]->AddComponent(new ValueComponent("type", "text"), "name");
        }    	
        
        /**
         * @return string
         */
        public function GetTag(): string {
            return "input";
        }        
}
?>