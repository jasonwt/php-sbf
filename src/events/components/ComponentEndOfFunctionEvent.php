<?php
    declare(strict_types=1);    

    namespace sbf\events\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\events\components\ComponentEvent;

    class ComponentEndOfFunctionEvent extends ComponentEvent {
        public function __construct(string $name, Component $caller, $returnValue, array $arguments = []) {
            parent::__construct($name, $caller, $returnValue, $arguments);
        }

        static function SEND($returnValue, array $arguments = []) {
            $backtrace = debug_backtrace()[1];

            $event = new ComponentEndOfFunctionEvent(
                $backtrace["function"],
                $backtrace["object"],
                $returnValue,
                $arguments
            );      

            $nonNullArgsCnt = 0;

            foreach ($arguments as $v) {
                if (is_string($v)) {
                    if (trim($v) != "")
                        $nonNullArgsCnt++;
                } else if (!is_null($v)) {
                    $nonNullArgsCnt ++;
                }
            }

            if ($nonNullArgsCnt!= count($backtrace["args"])) {
                //print_r(debug_backtrace());
                print_r($arguments);
                print_r($backtrace["args"]);
                throw new \Exception();
            }
            
            return $event->SendEvent();            
        }
    }



?>