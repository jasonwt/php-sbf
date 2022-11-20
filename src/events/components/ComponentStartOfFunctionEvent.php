<?php
    declare(strict_types=1);    

    namespace sbf\events\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;
    use sbf\events\components\ComponentEvent;

    class ComponentStartOfFunctionEvent extends ComponentEvent {
        public function __construct(string $name, Component $caller, array $arguments = []) {
            parent::__construct($name, $caller, null, $arguments);
        }

        static function SEND(array $arguments = []) {
            $backtrace = debug_backtrace()[1];

            $event = new ComponentStartOfFunctionEvent(
                $backtrace["function"],
                $backtrace["object"],
                $arguments
            );

            $c1 = count(array_filter($arguments, function ($v, $k) {
                if (is_array($v))
                    return count($v) > 0;

                if (is_string($v))
                    return trim($v) != "";

                return !is_null($v);
            }, ARRAY_FILTER_USE_BOTH));

            $c2 = count(array_filter($backtrace["args"], function ($v, $k) { 
                if (is_array($v))
                    return count($v) > 0;

                if (is_string($v))
                    return trim($v) != "";
                    
                return !is_null($v);
            }, ARRAY_FILTER_USE_BOTH));

            
            $nonNullArgsCnt = 0;

            foreach ($arguments as $v) {
                if (is_string($v)) {
                    if (trim($v) != "")
                        $nonNullArgsCnt++;
                } else if (!is_null($v)) {
                    $nonNullArgsCnt ++;
                } else {
                    echo "gettype: " . gettype($v) . "\n";
                }
            }

            //if ($nonNullArgsCnt!= count($backtrace["args"])) {
            if ($c1 != $c2) {
                //print_r(debug_backtrace());
                echo "c1: $c1\n";
                echo "c2: $c2\n";
                echo "arguments: " . print_r($arguments, true) . "\n\n";
                echo "backtrace: " . print_r($backtrace["args"], true) . "\n\n";
                
                echo "nonNullArgsCnt: $nonNullArgsCnt\n";
                echo count($backtrace["args"]) . "\n";

                
                throw new \Exception();
            }

            return $event->SendEvent();
        }
    }



?>