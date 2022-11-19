<?php
    declare(strict_types=1);

    namespace sbf\extensions\arrayaccess;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\extensions\Extension;
    use sbf\errorhandler\ErrorHandler;

    class GetSetOverrideExtension extends Extension {
        protected $componentType = "";
        protected $setMethod = "";
        protected $getMethod = "";

        public function __construct(string $name, string $setMethod, string $getMethod, string $componentType, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            $this->setMethod = trim($setMethod);
            $this->getMethod = trim($getMethod);
            $this->componentType = trim($componentType);
        }

        public function offsetGet_FRHOOK(Component $caller, $returnValue, $offset) {
            if ($caller != $this->parent)
                return $returnValue;

            if (!$this->getMethod || !$this->componentType)
                return $returnValue;

            //if (!method_exists($this->parent, $this->getMethod)) {
            //    $this->AddError(E_USER_ERROR, "getMethod '" . $this->getMethod . "' does not exist");
            //    return $returnValue;
            //}
            
            return call_user_func([$returnValue, $this->getMethod]);            
        }

        public function offsetSet_FIHOOK(Component $caller, &$offset, &$value) {
            if ($caller != $this->parent)
                return;

            if (!$this->setMethod || !$this->componentType)
                return;

            //if (!method_exists($caller->parent, $this->setMethod)) {
            //    $this->AddError(E_USER_ERROR, "setMethod '" . $this->setMethod . "' does not exist");
            //    return;
            //}

            $newComponent = null;

            if (array_key_exists($offset, $this->components))
                $newComponent = $this->parent->GetComponent($offset);
            else
                $newComponent = new $this->componentType($offset);

            call_user_func([$newComponent, $this->setMethod], $value);
            $value = $newComponent;
        }
    }
?>