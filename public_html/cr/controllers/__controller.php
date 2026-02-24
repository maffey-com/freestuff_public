<?php
class _Controller  {
    static protected $_controller_name = '';
    static protected $_method_name = '';

    public function __construct() {
        if (get_class($this) != 'AuthController') {
            if (!SecurityHelper::isLoggedIn()) {
                redirect(APP_URL . "auth");
            }
            if (!in_array($_SESSION["session_email"], User::ADMIN_EMAILS)) {
                redirect(SITE_URL );
            }

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
}