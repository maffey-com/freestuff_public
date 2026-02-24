<?php

class _Controller {
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
        echo json_encode(getErrors());
        die();
    }

    public static function redirectIfAlreadyLoggedIn($url = NULL) {
        if (SecurityHelper::isLoggedIn()) {
            $url = empty($url) ? APP_URL . 'home' : $url;

            redirect($url);
        }
    }

    public function do404() {
        header("HTTP/1.0 404 Not Found");
        PageHelper::setMinifyPageCssName('404.css');
        PageHelper::addPageStylesheetFile('css/404.css');

        PageHelper::setViews("views/page/404.php");

        include("templates/main_layout.php");
        exit();
    }


}