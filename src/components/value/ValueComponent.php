<?php
    declare(strict_types=1);

    namespace sbf\components\value;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;

    class ValueComponent extends Component {
        private $value;

        public function __construct(string $name, $value = null, $components = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $errorHandler);

            $this->value = $value;            
        }

        public function GetValue() {
            $this->ProcessHook("GetValue_FIHOOK", [$this]);

            
            return ($this->ProcessHook("GetValue_FRHOOK", [$this, $this->value]));
        }

        public function SetValue($value) {
            $this->ProcessHook("SetValue_FIHOOK", [$this, &$value]);

            $this->value = $value;

            return ($this->ProcessHook("SetValue_FRHOOK", [$this, null, $value]));
        }
    }


?>