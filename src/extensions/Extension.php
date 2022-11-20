<?php
    declare(strict_types=1);

    namespace sbf\extensions;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\errorhandler\ErrorHandler;

    use sbf\events\components\ComponentStartOfFunctionEvent;
    use sbf\events\components\ComponentEndOfFunctionEvent;

    class Extension extends Component implements ExtensionInterface {
        public function __construct(string $name, $components = null, $extensions = null, ?ErrorHandler $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);
        }

        protected function InitExtension() : bool {
            ComponentStartOfFunctionEvent::SEND();

            return ComponentEndOfFunctionEvent::SEND(true);
        }

        public function CanExtensionCall(string $methodName, ?int $maxDepth = null) {
            ComponentStartOfFunctionEvent::SEND([&$methodName, &$maxDepth]);

            $canCall = false;

            if (!is_null($maxDepth)) {
                if ($maxDepth < 0)
                    return false;            
            }

            if (method_exists($this, $methodName)) {
                $reflection = new \ReflectionMethod($this, $methodName);
                $canCall = $reflection->isPublic();
            }

            foreach ($this->extensions as $extension)
                $canCall = $canCall | $extension->CanExtensionCall($methodName, (is_null($maxDepth) ? null : $maxDepth - 1));

            return ComponentEndOfFunctionEvent::SEND($canCall, [$methodName, $maxDepth]);;
        }
    }
?>