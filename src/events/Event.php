<?php
    declare(strict_types=1);    

    namespace sbf\events;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\events\EventInterface;

    class Event implements EventInterface {
        public $name = "";
        public $caller = null;
        public $arguments = [];

        public function __construct(string $name, object $caller, array $arguments = []) {
            if (($this->name = trim($name) == ""))
                throw new \Exception("name must not be empty");

            $this->name = $name;
            $this->caller = $caller;
            $this->arguments = $arguments;
        }
    }
?>
