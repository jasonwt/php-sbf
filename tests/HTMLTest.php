<?php

    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");
   
    use function sbf\debugging\dtprint;

    use sbf\components\html\attributes\HTMLAttributeInterface;
    use sbf\components\html\attributes\HTMLAttribute;
    use sbf\components\html\attributes\HTMLAttributesInterface;
    use sbf\components\html\attributes\HTMLAttributes;
    use sbf\components\html\elements\HTMLElementInterface;
    use sbf\components\html\elements\HTMLElement;
    use sbf\components\html\elements\form\FormElementInterface;    
    use sbf\components\html\elements\form\FormElement;
    use sbf\components\html\elements\form\input\FormInputElementInterface;
    use sbf\components\html\elements\form\input\FormInputElement;
    use sbf\components\html\elements\form\input\text\FormInputTextElementInterface;
    use sbf\components\html\elements\form\input\text\FormInputTextElement;
    use sbf\components\html\elements\form\form\FormFormElementInterface;
    use sbf\components\html\elements\form\form\FormFormElement;


    use sbf\components\Component;
    use sbf\components\value\ValueComponent;
    use sbf\components\value\ValueComponentInterface;
    use sbf\extensions\debugging\DebuggingExtension;
    use sbf\components\arrayaccess\ArrayAccessComponent;
    use sbf\components\arrayaccess\ArrayAccessComponentInterface;     

    use sbf\extensions\Extension;
    use sbf\extensions\ExtensionInterface;

    use sbf\extensions\database\tableio\DatabaseTableIOExtension;




    $form = new FormFormElement(
        "form", 
        null, 
        [
            new DebuggingExtension("debuggingExtension")
        ]
    );

    print_r($form->Dump(false));

    //echo $form->GetHTML() . "\n";




    //dtprint("attributes: ", $attributes["attributes"], "\n");

    
?>