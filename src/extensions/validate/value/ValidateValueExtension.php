<?php
    declare(strict_types=1);

    namespace sbf\extensions\validate\value;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    use sbf\components\value\ValueComponent;
    use sbf\errorhandler\ErrorHandler;
    use sbf\extensions\validate\ValidateExtension;

    class ValidateValueExtension extends ValidateExtension {
        protected array $validationPatterns = [];

        public function __construct(string $name, array $validationPatterns = [], ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $errorHandler);

            $this->validationPatterns = $validationPatterns;
        }

        protected function InitExtension() : bool {
            $this->ProcessHook("InitExtension_FIHOOK", [$this]);
            
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
                
            return $this->ProcessHook("InitExtension_FRHOOK", [$this, $returnValue]);
        }
        
        public function Validate() {
            $this->ProcessHook("Validate_FIHOOK", [$this]);

            $validationErrors = [];

            foreach ($this->validationPatterns as $name => $pattern) {                
                if (!preg_match($pattern, strval($this->parent->GetValue())))
                    $validationErrors[] = $name;
            }

            return $this->ProcessHook("Validate_FRHOOK", [$this, (count($validationErrors) == 0 ? true : $validationErrors)]);
        }
    }

?>