<?
session_start();

require_once("../common/config/config.php");
require_once("../common/resources/func_all.php");
require_once('../../composer/vendor/autoload.php');

define("APP_URL", SITE_URL);

ini_set('display_errors', '1');

spl_autoload_register(function ($class_name) {
    $class_name[0] = strtolower($class_name[0]);
    $class_name = preg_replace_callback('/([A-Z])/', function($c){
        return "_" . strtolower($c[1]);
    }, $class_name);

    if (substr($class_name, -6) == "helper") {
        include_once("../common/helpers/class_" . $class_name . '.php');

    } elseif (substr($class_name, -7) == "handler") {
        include_once "handlers/class_" . $class_name . '.php';

    } elseif (substr($class_name, -10) == "controller") {
        include_once "controllers/" . $class_name . '.php';

    } else {
        include_once("../common/model/class_" . $class_name . '.php');
    }
});

$DB_connect = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME)
or die("The site database appears to be down. Please try again later.");

mysqli_query($DB_connect, 'SET NAMES utf8');
