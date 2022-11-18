<?php
    declare(strict_types=1);

    namespace sbf\extensions\debugging;    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\Extension;

    class DebuggingExtension extends Extension {        
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);
        }

        protected function InitExtension() : bool {
            return true;
        }

        protected function GetStructure(Component $component, int $indentLevel) : string {
            $returnValue = "";

            $returnValue .= str_repeat("\t", $indentLevel) . "CLASS TYPE : " . get_class($component) . "\n";
            $returnValue .= str_repeat("\t", $indentLevel) . "NAME       : " . $component->GetName() . "\n";

            if (!is_null($component->GetParent()))
                $returnValue .= str_repeat("\t", $indentLevel) . "PARENT     : " . $component->GetParent()->GetName() . "\n";

            if (method_exists($component, "GetValue"))
                $returnValue .= str_repeat("\t", $indentLevel) . "VALUE      : " . $component->GetValue() . "\n";

            if ($component->GetErrorCount() > 0) {
                $returnValue .= str_repeat("\t", $indentLevel) . "ERRORS:\n\n";

                while ($error = $component->GetError()) {
                    $returnValue .= str_repeat("\t", $indentLevel+1) . str_replace("\n", "\n" . str_repeat("\t", $indentLevel+1), $error) . "\n\n";
                }
            }

            if ($component->GetExtensionsCount() > 0) {
                $returnValue .= "\n" . str_repeat("\t", $indentLevel) . "EXTENSIONS:\n";

                foreach ($component->GetExtensions() as $extension) {
                    $returnValue .= "\n";
                    $returnValue .= $this->GetStructure($extension, $indentLevel+1);        
                }
            }

            if ($component->GetComponentsCount() > 0) {
                $returnValue .= "\n" . str_repeat("\t", $indentLevel) . "COMPONENTS:\n";

                foreach ($component->GetComponents() as $component) {
                    $returnValue .= "\n";
                    $returnValue .= $this->GetStructure($component, $indentLevel+1);        
                }
            }
            $returnValue .= "\n";

            return $returnValue;
        }

        public function GetComponentStructure() : string {
            if ($this->GetParent() == null)
                return "";

            return $this->GetStructure($this->GetParent(), 0);
        }        
    }

?>