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
    	/**
         * @return string
         */
        public function GetVersion(): string {
            return ("0.0.1");
        }

        public function GetRequiredExtensions() : array {
            return [
                "TestExtension2" => [
                    "exactExtensionClassName" => false,
                    "attemptToAutoLoad" => true
                ]
            ];
        }
    }

    interface TestExtention2Interface extends ExtensionInterface {

    }

    class TestExtension2 extends Extension implements TestExtention2Interface {
        static public function GetCanCallPriority() : int {
            return TestExtension1::GetCanCallPriority() + 1;
        }

        /**
         * @return string
         */
        public function GetVersion(): string {
            return ("0.0.2");
        }

        public function GetTestValue() : string {
            return "2";
        }      
    }

    class testComponent extends Component {
        public function __construct(string $name, $components = null, $extensions = null, $errorHandler = null) {
            parent::__construct($name, $components, $extensions, $errorHandler);

        }
    }
    $com = new Component(
        "testComponent",
        null,
        [
            new DebuggingExtension("debuggingExtension"),
            new TestExtension1("testExtension1")/*,
            new TestExtension2("testExtension2")*/
        ]
    );

    //dtprint($com);

    echo $com->Dump(false);

    echo "GetTestValue(): " . $com->GetTestValue() . "\n";


?>