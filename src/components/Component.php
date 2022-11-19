<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\errorhandler\ErrorHandler;
    use sbf\components\ComponentInterface;
    use sbf\extensions\Extension;
    use sbf\components\ComponentObjectArrayTraits;
    use sbf\components\ComponentArrayAccessTraits;
    use sbf\components\ComponentCountableIteratorTraits;
    use sbf\components\ComponentErrorTraits;

    use sbf\events\components\ComponentEvent;
    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    use function sbf\debugging\dtprint;
    use function sbf\debugging\dprint;

    class Component implements ComponentInterface {
        use ComponentObjectArrayTraits;
        use ComponentArrayAccessTraits;
        use ComponentCountableIteratorTraits;
        use ComponentErrorTraits;

        const ALLOW_SET              = 1;
        const ALLOW_SET_ON_FOUND     = 2;
        const ALLOW_SET_ON_NOT_FOUND = 4;
        const ALLOW_UNSET            = 8;
        const ALLOW_GET              = 16;

        protected $iteratorIndex = 0;
        protected $options = 31;
        protected $name = "";
        protected ?Component $parent = null;
        protected array $components = [];
        protected array $extensions = [];

        protected ErrorHandler $errorHandler;

        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            $this->errorHandler = (is_null($errorHandler) ? new ErrorHandler() : $errorHandler);

            if (($this->name = trim($name)) == "")                 
                $this->AddError(E_USER_ERROR, "name is required and can not be all whitespaces.");

            if (!is_null($components)) {
                if (!is_array($components))
                    $components = [$components];

                foreach ($components as $component) {
                    if (!is_object($component)) {
                        $this->AddError(E_USER_ERROR, "Invalid component type '" . gettype($component) . "'. Must be derived from Component.");
                    } else if (!($component instanceof Component)) {
                        $this->AddError(E_USER_ERROR, "Invalid component type '" . get_class($component) . "'. Must be derived from Component.");
                    } else {
                        $this->AddComponent($component);
                    }
                }
            }

            if (!is_null($extensions)) {
                if (!is_array($extensions))
                    $extensions = [$extensions];

                foreach ($extensions as $extensions) {
                    if (!is_object($extensions)) {
                        $this->AddError(E_USER_ERROR, "Invalid extension type '" . gettype($extensions) . "'. Must be derived from Extension.");
                    } else if (!($extensions instanceof Extension)) {
                        $this->AddError(E_USER_ERROR, "Invalid extension type '" . get_class($extensions) . "'. Must be derived from Extension.");
                    } else {
                        $this->AddExtension($extensions);
                    }
                }
            }
        }

        public function __call(string $methodName, array $arguments) {
            if ($methodName == "SendEvent" && count($backtrace = debug_backtrace()) > 1)
                if (isset($backtrace[1]["object"]) && $backtrace[1]["object"] instanceof ComponentEvent)
                    return $this->SendEvent($arguments[0]);                                                
                    
            foreach ($this->extensions as $extensionName => $extension) {
                if ($extension->CanCall($methodName))
                    return call_user_func_array([$extension, $methodName], $arguments);
            }

            throw new \BadMethodCallException(get_class($this) . "::" . $methodName);
        }

        public function CanCall(string $methodName) : bool {            
            ComponentStartOfFunctionEvent::SEND([&$methodName]);

            $canCall = false;

            if (method_exists($this, $methodName)) {
                $reflection = new \ReflectionMethod($this, $methodName);
                $canCall = $reflection->isPublic();
            }

            return ComponentEndOfFunctionEvent::SEND($canCall, [$methodName]);
        }

        public function GetName() : string {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND($this->name);
        }

        protected function Rename(string $newName) : bool {
            if ($newName == $this->name)
                return true;

            ComponentStartOfFunctionEvent::SEND([&$newName]);

            $error = null;

            if (!is_null($this->parent)) {
                if (in_array($newName, $this->parent->GetComponentNames())) {
                    $error = ["errorCode" => E_USER_ERROR, "errorMessage" => "A component already exists with the name '$newName'"];
                } else {
                    $oldParent = $this->parent;
                    if (!$this->parent->RemoveComponent($this)) {
                        $error = ["errorCode" => E_USER_ERROR, "errorMessage" => "this->parent->RemoveComponent() failed."];
                    } else {
                        $this->name = $newName;
                        if (!$oldParent->AddComponent($this)) {
                            $error = ["errorCode" => E_USER_ERROR, "errorMessage" => "oldParent->AddComponent()."];
                        } 
                    }                    
                }                
            } else {
                $this->name = $newName;
            }

            if (!is_null($error))
                $this->AddError($error["errorCode"], $error["errorMessage"]);
            
            return ComponentEndOfFunctionEvent::SEND(is_null($error), [$newName]);
        }

        public function GetParent() : ?Component {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND($this->parent);
        }

        /* 
        EXTENSIONS METHODS 
        */

        protected function GetExtensionsCount(string $extensionType = "") : int {
            ComponentStartOfFunctionEvent::SEND([&$extensionType]);

            $returnValue = $this->GetObjectArrayElementCount($this->extensions, $extensionType);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$extensionType]);
        }

        protected function GetExtensions(string $extensionType = "") : array {
            ComponentStartOfFunctionEvent::SEND([&$extensionType]);

            $returnValue = $this->GetObjectArrayElements($this->extensions, $extensionType);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$extensionType]);
        }

        protected function GetExtensionNames(string $extensionType = "") : array {
            ComponentStartOfFunctionEvent::SEND([&$extensionType]);

            $returnValue = $this->GetObjectArrayElementKeys($this->extensions, $extensionType);            
            
            return ComponentEndOfFunctionEvent::SEND($returnValue, [$extensionType]);
        }

        protected function GetExtension(string $name) : ?Extension {
            ComponentStartOfFunctionEvent::SEND([&$name]);

            $returnValue = $this->GetObjectArrayElement($this->extensions, $name);            

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$name]);
        }

        protected function ExtensionExists(Extension $extension) : bool {
            ComponentStartOfFunctionEvent::SEND([&$extension]);

            $returnValue = $this->ObjectArrayElementExists($this->extensions, $extension);
            
            return ComponentEndOfFunctionEvent::SEND($returnValue, [$extension]);
        }

        protected function AddExtension(Extension $extension) : bool {
            ComponentStartOfFunctionEvent::SEND([&$extension]);

            $returnValue = $this->ObjectArrayAddElement($this->extensions, $extension);            

            if ($returnValue)
                $returnValue = $extension->InitExtension();            

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$extension]);
        }

        protected function RemoveExtension(Extension $extension) : bool {
            ComponentStartOfFunctionEvent::SEND([&$extension]);
            
            $returnValue = $this->ObjectArrayRemoveElement($this->extensions, $extension);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$extension]);
        }

        /* 
        COMPONENT METHODS 
        */

        protected function GetComponentsCount(string $componentType = "") : int {
            ComponentStartOfFunctionEvent::SEND([&$componentType]);

            $returnValue = $this->GetObjectArrayElementCount($this->components, $componentType);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$componentType]);
        }

        protected function GetComponents(string $componentType = "") : array {
            ComponentStartOfFunctionEvent::SEND([&$componentType]);

            $returnValue = $this->GetObjectArrayElements($this->components, $componentType);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$componentType]);
        }

        protected function GetComponentNames(string $componentType = "") : array {
            ComponentStartOfFunctionEvent::SEND([&$componentType]);

            $returnValue = $this->GetObjectArrayElementKeys($this->components, $componentType);            
            
            return ComponentEndOfFunctionEvent::SEND($returnValue, [$componentType]);
        }

        protected function GetComponent(string $name) : ?Component {
            ComponentStartOfFunctionEvent::SEND([&$name]);

            $returnValue = $this->GetObjectArrayElement($this->components, $name);            

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$name]);
        }

        protected function ComponentExists(Component $component) : bool {
            ComponentStartOfFunctionEvent::SEND([&$component]);

            $returnValue = $this->ObjectArrayElementExists($this->components, $component);
            
            return ComponentEndOfFunctionEvent::SEND($returnValue, [$component]);
        }

        protected function AddComponent(Component $component) : bool {
            if ($component instanceof Extension) {
                $this->AddError(E_USER_ERROR, "Use AddExtension rather then AddComponent when adding extensions.");
                return false;
            }

            ComponentStartOfFunctionEvent::SEND([&$component]);

            $returnValue = $this->ObjectArrayAddElement($this->components, $component);                        

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$component]);
        }

        protected function RemoveComponent(Component $component) : bool {
            ComponentStartOfFunctionEvent::SEND([&$component]);
            
            $returnValue = $this->ObjectArrayRemoveElement($this->components, $component);

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$component]);
        }

        /* 
        EVENTS
        */

        protected function SendEvent(ComponentEvent $event) {
//            echo get_class($event->caller) . ":" . $event->caller->name . ":" . get_class($event) . ":" . $event->name . "\n";
            if (method_exists($this, "HandleEvent"))
                $event->returnValue = $this->HandleEvent($event);

            foreach (array_merge($this->components, $this->extensions) as $component)
                $event->returnValue = $component->SendEvent($event);
            
            return $event->returnValue;
        }

        protected function HandleEvent(ComponentEvent $event) {
            return $event->returnValue;
        }
    }

?>