<?
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/common/config/config.php");
require_once(DOCROOT . "/common/resources/func_all.php");
require_once(DOCROOT . '/../composer/vendor/autoload.php');

define("APP_URL", SITE_URL . "cr/");

spl_autoload_register(function ($class_name) {
    $class_name[0] = strtolower($class_name[0]);
    $class_name = preg_replace_callback('/([A-Z])/', function($c){
        return "_" . strtolower($c[1]);
    }, $class_name);

    if (substr($class_name, -6) == "helper") {
        include_once DOCROOT . "/common/helpers/class_$class_name.php";
    } elseif (substr($class_name, -7) == "handler") {
        include_once DOCROOT . "/cr/handlers/class_$class_name.php";
    } elseif (substr($class_name, -10) == "controller") {

        $controller_path = DOCROOT . "/cr/controllers/$class_name.php";

        if (in_array(strtolower($class_name), SKIP_CONTROLLER) || !file_exists($controller_path)) {
            http_response_code(404);
            return;
        }

        include_once $controller_path;

    } else {
        include_once DOCROOT . "/common/model/class_$class_name.php";
    }
});

$DB_connect = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME) or die("The site database appears to be down. Please try again later.");

if (!SecurityHelper::isLoggedIn()) {
    SecurityHelper::loginViaCookie();
}

if (!SecurityHelper::isLoggedIn()) {
    $cap_array = explode("/", paramFromGet("_cap", ""));

    $controller_name = array_shift($cap_array);
    $action = array_shift($cap_array);

    if ($controller_name != 'auth')  {
        redirect(APP_URL . "auth");
    }
}

