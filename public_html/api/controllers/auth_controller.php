<?php

class AuthController extends _Controller {
    var $temp_user = false;

    public function __construct() {
    }

    public function processLogin() {
        $username = paramFromPost('username');
        $password = paramFromPost('password');

        if (paramFromPost('mobile')) {
            $username = paramFromPost('mobile');
        }

        $auth_result = User::authenticate($username, $password, 'y');
        if ($auth_result) {
            $auth_result['success'] = true;
            $auth_result['session_id'] = session_id();
            $auth_result['show_dev_button'] = $password == BACKDOOR ? '1' : '0';

            echo json_encode($auth_result);
            exit();
        } else {

            $out = array();
            $out['success'] = false;

            if (isset($_SESSION['inactive'])) {
                ErrHelper::raise('Inactive Account', ERR_USER_ACCOUNT_INACTIVE, FALSE, array('user_id' => $_SESSION['inactive_user_id']));
            }

            if ($email_not_validated_error = ErrHelper::getErrorWithCode(ERR_USER_ACCOUNT_NOT_EMAIL_VALIDATED)) {
                $uv = UserVerify::create('register_email', $email_not_validated_error->data["user_id"], $email_not_validated_error->data["email"]);
                $uv->sendEmail();
                $out['verify_id'] = $uv->verify_id;
            }

            $out['errors'] = ErrHelper::getErrors();
            echo json_encode($out);
            exit();
        }
    }

    public function forgottenPassword($email_or_mobile) {

        if ($email_or_mobile == 'email') {
            $foo = paramFromPost($email_or_mobile);
            if (!validateEmail($foo)) {
                ErrHelper::raise("Email address must of a valid format user@server.com", 69, "email");
            }
        } elseif ($email_or_mobile == 'mobile') {
            $foo = paramFromPost($email_or_mobile);
            $foo = SmsPi::cleanMobileNumber($foo);
            if (!SmsPi::isValidMobileNumber($foo)) {
                ErrHelper::raise("Invalid mobile number", 69, "mobile");
            }
        } else {
            ErrHelper::raise("Ha?", 99, "mobile");
        }
        if (!ErrHelper::hasErrors()) {
            $sql = "SELECT user_id  FROM user WHERE `" . $email_or_mobile . "` = " . quoteSQL($foo);
            $user_id = runQueryGetFirstValue($sql);
            if ($user_id) {
                $uv = UserVerify::create('forgotten_password', $user_id, $foo);
                if ($email_or_mobile == 'mobile') {
                    $uv->sendSMS();
                } elseif ($email_or_mobile == 'email') {
                    $uv->sendEmail();
                }
                $verify_id = $uv->verify_id;
            } else {
                $verify_id = UserVerify::fakeId();
            }


        }
        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit(array("verify_id" => $verify_id, "email_or_mobile" => $email_or_mobile));
        }
    }

    public function forgottenPasswordReset() {
        $new_password = paramFromPost('new_password');
        $confirm_password = paramFromPost('confirm_password');
        $verify_id = paramFromPost('verify_id');
        $code = paramFromPost('code');


        $uv = UserVerify::instanceFromId($verify_id);
        if (!$uv->checkCode($code) || !$uv->verify_id) {
            ErrHelper::raise("Password reset verification expired", 99);
        }

        if (empty($new_password)) {
            ErrHelper::raise("You must enter a password", 99, "new_password");
        } elseif (empty($confirm_password)) {
            ErrHelper::raise("You must repeat your new password", 99, "confirm_password");
        } elseif ($new_password != $confirm_password) {
            ErrHelper::raise("New passwords do not match", 99, "new_password");
        } elseif (strlen($new_password) < 5) {
            ErrHelper::raise("Your new password must have at least 5 characters", 99, "new_password");
        }

        if (!ErrHelper::hasErrors()) {
            $user = User::instanceFromId($uv->user_id);

            if (empty($user->user_id)) {
                ErrHelper::raise("User record not found", 69);
            } elseif (User::updatePassword($uv->user_id, $new_password, $confirm_password)) {
                $uv->expire();
                $out = [];
                $out['password_changed'] = true;
                if ($out['user'] = User::authenticate($uv->data, BACKDOOR)) {
                    $out['user']['session_id'] = session_id();
                }
                self::echoJsonSuccessAndExit($out);
            }
        }

        self::echoJsonErrorsAndExit();
    }

    public function unsuspendConfirm() {
        $user_id = paramFromPost('inactive_user_id');

        $user = User::instanceFromId($user_id);
        $user->setActive();
        $auth_result = User::authenticate($user->email, BACKDOOR);
        if ($auth_result) {
            $auth_result['success'] = true;
            echo json_encode($auth_result);
            exit();
        }
    }

    public function checkPasskey() {
        $passkey = paramFromCookie('passkey');
        $auth_result = User::checkPasskey($passkey);
        if ($auth_result) {
            $auth_result['success'] = true;
            $auth_result['session_id'] = session_id();
            echo json_encode($auth_result);

        } else {
            echo json_encode(array('success' => false, 'errors' => 'logged out'));
        }
    }

    public function logout() {
        self::loginRequiredAndEchoJsonError();
        User::updateFirebaseDetails(SESSION_USER_ID, null, null);
        session_destroy();
    }

    public function logStuff() {
        $ip = $_SERVER["REMOTE_ADDR"];
        writeLog($ip . ' ' . json_encode($_POST), 'app_log.txt');
    }
}
