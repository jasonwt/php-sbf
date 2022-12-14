<?php
    declare(strict_types=1);

    namespace sbf\extensions;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\ComponentInterface;

    interface ExtensionInterface extends ComponentInterface {
        static public function GetCanCallPriority() : int;

        public function GetRequiredExtensions() : array;
        public function CanExtensionCall(string $methodName, ?int $maxDepth = null);

        public function GetVersion() : string;
        public function Disable() : bool;
        public function Enable() : bool;
        public function IsEnabled() : bool;
    }
?>