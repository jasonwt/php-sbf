<?php


    declare(strict_types=1);    

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once("vendor/autoload.php");    

    require_once(__DIR__ . "/database.login.php");
   
    use function sbf\debugging\dtprint;

    use sbf\components\Component;
    use sbf\extensions\debugging\DebuggingExtension;

    use sbf\extensions\ExtensionInterface;
    use sbf\extensions\Extension;

    interface TestExtention1Interface extends ExtensionInterface {
        
    }

    class TestExtension1 extends Extension implements TestExtention1Interface {
        static public function GetCanCallPriority() : int {
            return 1;
        }

        public function GetTestValue() : string {
            return "1";
        }      
    }

    interface TestExtention2Interface extends ExtensionInterface {

    }

    class TestExtension2 extends Extension implements TestExtention2Interface {
        static public function GetCanCallPriority() : int {
            return TestExtension1::GetCanCallPriority() + 1;
        }

        public function GetTestValue() : string {
            return "2";
        }      
    }

    $com = new Component(
        "testComponent",
        null,
        [
            new DebuggingExtension("debuggingExtension"),
            new TestExtension1("testExtension1"),
            new TestExtension2("testExtension2")
        ]
    );

    //dtprint($com);

    echo $com->Dump(false);

    echo "GetTestValue(): " . $com->GetTestValue() . "\n";


?>