<?php
class AuthController extends _Controller {

    public function index() {
        self::_redirectIfAlreadyLoggedIn();

        TemplateHandler::setMainView("views/login/login.php");
        include("templates/login.php");
    }

    public function processLogin() {
        self::_redirectIfAlreadyLoggedIn();

        $username = paramFromPost("username");
        $password = paramFromPost("password");
        $remember = paramFromPost("remember", false) !== false;

        User::authenticate($username, $password, $remember);

        redirect(APP_URL . "auth");
    }


    public function logout() {
        SecurityHelper::logout();

        redirect(APP_URL . "auth");
    }



    protected static function _redirectIfAlreadyLoggedIn() {
        if (SecurityHelper::isLoggedIn()) {
            redirect(APP_URL . "dashboard");
        }
    }
}