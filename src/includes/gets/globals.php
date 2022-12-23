<?php
    declare(strict_types=1);    

    namespace sbf\includes\gets;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    function GetRequestValue(string $name, $defaultValue = "") {
        return (isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defaultValue);
    }

    function GetGetValue(string $name, $defaultValue = "") {
        return (isset($_GET[$name]) ? $_GET[$name] : $defaultValue);
    }

    function GetPostValue(string $name, $defaultValue = "") {
        return (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }

    function GetSessionValue(string $name, $defaultValue = "") {
        return (isset($_SESSION[$name]) ? $_SESSION[$name] : $defaultValue);
    }

    function GetCookieValue(string $name, $defaultValue = "") {
        return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $defaultValue);
    }

    function GetServerValue(string $name, $defaultValue = "") {
        return (isset($_SERVER[$name]) ? $_SERVER[$name] : $defaultValue);
    }

    function GetEnvValue(string $name, $defaultValue = "") {
        return (isset($_ENV[$name]) ? $_ENV[$name] : $defaultValue);
    }

    function GetFilesValue(string $name, $defaultValue = "") {
        return (isset($_FILES[$name]) ? $_FILES[$name] : $defaultValue);
    }
?>