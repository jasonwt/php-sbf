<?php
    declare(strict_types=1);

    namespace sbf\extensions;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\errorhandlers\ErrorHandler;

    use sbf\events\components\ComponentEvent;
    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    abstract class Extension extends Component implements ExtensionInterface {
        protected $enabled = true;

        static public function GetCanCallPriority() : int {
            return 0;
        }

        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

            
        }

        public function GetRequiredExtensions() : array {
            return [];
        }

        public function Disable() : bool {
            $returnValue = false;

            if (ComponentStartOfFunctionEvent::SEND() !== false) {
                if ($this->enabled) {
                    $returnValue = true;
                    $this->enabled = false;                    
                } else {
                    $this->AddError(E_USER_WARNING, "Disable() failed.  Already disabled.");
                }
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }

        public function Enable() : bool {
            $returnValue = false;

            if (ComponentStartOfFunctionEvent::SEND() !== false) {
                if ($this->enabled) {
                    
                    $this->AddError(E_USER_WARNING, "Enable() failed.  Already enabled.");
                } else {
                    $returnValue = true;
                    $this->enabled = true;
                }
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue);
        }

        public function IsEnabled() : bool {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND($this->enabled);
        }

        protected function InitExtension() : bool {
            ComponentStartOfFunctionEvent::SEND();

            $requiredDependencyExtensions = $this->GetRequiredExtensions();

            if (count($requiredDependencyExtensions) > 0 && is_null($this->parent)) {
                $this->AddError(E_USER_ERROR, "a parent is required for extensions with required dependencies extensions.");
            } else {
                foreach ($this->GetRequiredExtensions() as $requiredExtensionClassName => $requiredExtensionInfo) {
                    $exactExtensionClassName = $requiredExtensionInfo["exactExtensionClassName"];
                    $attemptToAutoLoad       = $requiredExtensionInfo["attemptToAutoLoad"];        
                    
                    if ($this->parent->GetExtensionsCount($requiredExtensionClassName) == 0) {
                        if ($attemptToAutoLoad) {
                            
                            if (is_null($this->parent->AddExtension(new $requiredExtensionClassName(strtoupper($requiredExtensionClassName))))) {
                                $this->AddError(E_USER_ERROR, "autoload required extension '$requiredExtensionClassName' failed.");    
                            } else {
                                //$this->AddError(E_USER_WARNING, "autoloaded required extension '$requiredExtensionClassName'.");
                            }
                        } else {
                            $this->AddError(E_USER_ERROR, "required extension '$requiredExtensionClassName' is not loaded.");
                        }                     
                            echo $requiredExtensionClassName . ":" . $exactExtensionClassName . ":" . $attemptToAutoLoad . "\n";
                    } else {
                        echo $this->parent->GetExtensionsCount($requiredExtensionClassName);
                    }
                }
            }

            return ComponentEndOfFunctionEvent::SEND(true);
        }

        public function CanExtensionCall(string $methodName, ?int $maxDepth = null) {
            if (!$this->enabled)
                return false;

            $returnValue = false;

            if (ComponentStartOfFunctionEvent::SEND([&$methodName, &$maxDepth]) !== false) {
                if (!is_null($maxDepth)) {
                    if ($maxDepth < 0)
                        return false;            
                }

                if (method_exists($this, $methodName)) {
                    $reflection = new \ReflectionMethod($this, $methodName);
                    $returnValue = $reflection->isPublic();
                }

                foreach ($this->extensions as $extension)
                    $returnValue = $returnValue | $extension->CanExtensionCall($methodName, (is_null($maxDepth) ? null : $maxDepth - 1));
            }

            return ComponentEndOfFunctionEvent::SEND($returnValue, [$methodName, $maxDepth]);;
        }

        protected function SendEvent(ComponentEvent $event) {
            if (!$this->enabled)
                return $event->returnValue;

            return parent::SendEvent($event);
            
        }        
    }
?>