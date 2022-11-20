<?php
    declare(strict_types=1);    

    namespace sbf\events\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\events\Event;

    class ComponentEvent extends Event {
        public $returnValue = null;
        public $callDepth = 0;

        public function __construct(string $name, Component $caller, $returnValue, array $arguments = []) {
            parent::__construct($name, $caller, $arguments);

            $this->returnValue = $returnValue;
        }

        public function SendEvent() {
            return $this->caller->SendEvent($this);
        }
    }



?>