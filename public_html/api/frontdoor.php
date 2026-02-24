<?

if (isset($_REQUEST["session_id"])) {
    session_id($_REQUEST["session_id"]);
}

require_once("resources/initial.php");

$cap_array = explode("/", paramFromGet("_cap", ""));
unset($_GET["_cap"]);

$controller_name = array_shift($cap_array);
$action = array_shift($cap_array);

if (!$controller_name) {
    exit("no controller specified");
}

$action = $action ? $action : 'index';

$action = str_ireplace('_', '', $action);

$controller_name_array = explode("_", $controller_name);
foreach ($controller_name_array as &$piece) {
    $piece = ucfirst($piece);
}

$controller_name = implode('', $controller_name_array);

$controller_class_name = $controller_name . "Controller";

if (!class_exists(ucfirst($controller_class_name), TRUE)) {
    exit($controller_class_name . '  not found');
}

if (!method_exists($controller_class_name, $action)) {
    exit('action (' . $action . ') not found');
}

_Controller::setControllerName($controller_name);
_Controller::setMethodName($action);

$controller = new $controller_class_name();

call_user_func_array(array($controller, $action), $cap_array);
CacheHelper::setPage();

die();
