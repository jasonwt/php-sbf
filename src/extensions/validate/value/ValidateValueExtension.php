<?php
    declare(strict_types=1);

    namespace sbf\extensions\validate\value;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\components\value\ValueComponent;
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\validate\ValidateExtension;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    class ValidateValueExtension extends ValidateExtension {
        protected array $validationPatterns = [];

        public function __construct(string $name, array $validationPatterns = [], $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->validationPatterns = $validationPatterns;
        }

        protected function InitExtension() : bool {
            ComponentStartOfFunctionEvent::SEND();
            
            $returnValue = false;

            if ($this->GetParent() != null) {
                if ($this->GetParent() instanceof ValueComponent) {
                    $returnValue = true;
                } else {
                    $this->AddError(E_USER_ERROR, "this->GetParent() must be an instance of ValueComponent.");
                }
            } else {
                $this->AddError(E_USER_ERROR, "this->GetParent() must not be null.");
            }
                
            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }
        
        public function Validate() {
            ComponentStartOfFunctionEvent::SEND();

            $validationErrors = [];

            foreach ($this->validationPatterns as $name => $pattern) {                
                if (!preg_match($pattern, strval($this->parent->GetValue())))
                    $validationErrors[] = $name;
            }

            $returnValue = (count($validationErrors) == 0 ? true : $validationErrors);

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }
    }

?>