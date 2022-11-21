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

            $c1 = array_filter($arguments, function ($v, $k) {

//                echo "gettype: " . gettype($v) . "\n";
                if (is_array($v))
                    return count($v) > 0;

                if (is_string($v))
                    return trim($v) != "";

//                if (is_bool($v))
  //                  return false;

                return !is_null($v);
            }, ARRAY_FILTER_USE_BOTH);

            $c2 = array_filter($backtrace["args"], function ($v, $k) { 
//                echo "gettype: " . gettype($v) . "\n";
                if (is_array($v))
                    return count($v) > 0;

                if (is_string($v))
                    return trim($v) != "";

//                if (is_bool($v))
  //                  return false;
                    
                return !is_null($v);
            }, ARRAY_FILTER_USE_BOTH);

            //if ($nonNullArgsCnt!= count($backtrace["args"])) {
            if (count($c1) != count($c2)) {
                echo "event name:" . $event->name . "\n";
                echo "arguments: " . print_r($c1, true) . "\n";
                echo "backtrace: " . print_r($c2, true) . "\n";
                print_r(debug_backtrace());
/*                
                print_r(debug_backtrace());
                echo "c1: $c1\n";
                echo "c2: $c2\n";
                echo "arguments: " . print_r($arguments, true) . "\n\n";
                echo "backtrace: " . print_r($backtrace["args"], true) . "\n\n";
                
                echo "nonNullArgsCnt: $nonNullArgsCnt\n";
                echo count($backtrace["args"]) . "\n";
*/
                
                throw new \Exception();
            }

            return $event->SendEvent();
        }
    }



?>