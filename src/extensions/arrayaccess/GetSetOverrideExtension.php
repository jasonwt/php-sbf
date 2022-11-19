<?php
    declare(strict_types=1);

    namespace sbf\extensions\arrayaccess;
    use sbf\events\components\ComponentEndOfFunctionEvent;
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\extensions\Extension;
    use sbf\errorhandler\ErrorHandler;

    use sbf\events\components\ComponentEvent;

    use function sbf\debugging\dtprint;

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

        protected function HandleEvent(ComponentEvent $event) {
            if ($event->caller != $this->parent)
                return $event->returnValue;

            if ($event->name == "offsetSet") {
                if (!$this->setMethod || !$this->componentType)
                    return $event->returnValue;

                $newComponent = null;
                $offset = &$event->arguments[0];
                $value = &$event->arguments[1];

                if (array_key_exists($offset, $this->components))
                    $newComponent = $event->caller->GetComponent($offset);                    
                else
                    $newComponent = new $this->componentType($offset);
    
                call_user_func([$newComponent, $this->setMethod], $value);

                $value = $newComponent;

            } else if ($event->name == "offsetGet") {
                if ($event instanceof ComponentEndOfFunctionEvent) {
                    if (!$this->getMethod || !$this->componentType)
                        return $event->returnValue;

                    $event->returnValue = call_user_func([$event->returnValue, $this->getMethod]);
                }
            }

            return $event->returnValue;
        }
    }
?>