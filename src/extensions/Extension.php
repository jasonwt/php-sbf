<?php
    declare(strict_types=1);

    namespace sbf\extensions;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;

    class Extension extends Component implements ExtensionInterface {
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);
        }

        protected function InitExtension() : bool {
            $this->ProcessHook("InitExtension_FIHOOK", [$this]);

            return $this->ProcessHook("InitExtension_FRHOOK", [$this, true]);
        }

        protected function DisabledPublicCanCallMethods() : array {
            $this->ProcessHook("DisabledPublicCanCallMethods_FIHOOK", [$this]);

            return $this->ProcessHook("DisabledPublicCanCallMethods_FRHOOK", [$this, []]);
        }

        public function CanCall(string $methodName) : bool {
            if (in_array($methodName, $this->DisabledPublicCanCallMethods()))
                return false;            

            return parent::CanCall($methodName);            
        }
    }
?>