<?php
class _Controller  {
    static protected $_controller_name = '';
    static protected $_method_name = '';

    const LOGIN_REQUIRED_MESSAGE = "You need to login or register to use this site.";

    public function __construct() {
        if (get_class($this) != 'AuthController') {
            // SecurityHelper::loginRequired();
            // SecurityHelper::permissionRequired("staff");
        }
    }

    public static function setControllerName($name) {
        self::$_controller_name = $name;
    }

    public static function getControllerName() {
        return self::$_controller_name;
    }

    public static function setMethodName($name) {
        self::$_method_name = $name;
    }

    public static function getMethodName() {
        return self::$_method_name;
    }

    public static function loginRequiredAndRedirect($message = self::LOGIN_REQUIRED_MESSAGE) {
        if (SecurityHelper::isLoggedIn()) {
            return TRUE;
        }

        MessageHelper::setSessionErrorMessage($message);
        redirect(APP_URL . 'login');
    }

    public static function loginRequiredAndEchoErrorMessage($message = self::LOGIN_REQUIRED_MESSAGE) {
        if (SecurityHelper::isLoggedIn()) {
            return TRUE;
        }

        echo '<p>' . $message . '</p>';
        die();
    }

    public static function loginRequiredAndEchoJsonError($message = self::LOGIN_REQUIRED_MESSAGE) {

        if (SecurityHelper::isLoggedIn()) {
            return TRUE;
        }


        raiseError($message, 'login_check');
        $output = [];
        $output['success'] = false;
        $output['errors'] = getErrors();
        echo json_encode($output);
        die();
    }

    public static function redirectIfAlreadyLoggedIn($url = NULL) {
        if (SecurityHelper::isLoggedIn()) {
            $url = empty($url) ?  APP_URL . 'home' : $url;

            redirect($url);
        }
    }
    public static function echoJsonErrorsAndExit() {
        ErrHelper::backwardsCompatibleBuild(getErrors());
        $out = array();
        $out['success'] = false;
        $out['errors'] = ErrHelper::getErrors();

        echo json_encode($out);
        exit();
    }
    public static function echoJsonSuccessAndExit($payload = array()) {
        $out = array();
        $out['success'] = true;
        $out['payload'] = $payload;

        echo json_encode($out);
        exit();
    }
}
