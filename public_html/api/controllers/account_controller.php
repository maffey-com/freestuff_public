<?php

class AccountController extends _Controller {
    public function __construct() {
        self::loginRequiredAndEchoJsonError();
    }

    public function changeName() {
        $firstname = paramFromPost('firstname');
        $firstname = preg_replace("/[^A-Za-z0-9 ]/", '', $firstname);

        if (!strlen($firstname)) {
            ErrHelper::raise('Invalid firstname', 'firstname');
            self::echoJsonErrorsAndExit();
        }

        $user = User::instanceFromId(SESSION_USER_ID);

        $user->setFirstname($firstname);
        $_SESSION["session_firstname"] = $user->firstname;
        $out = array();

        self::echoJsonSuccessAndExit(array("firstname" => $firstname));
    }

    public function changeLocation() {
//TODO:as per desktop
        $user = User::instanceFromId(SESSION_USER_ID);
        ErrHelper::raise('Cannot find place', 'location_entry');
        self::echoJsonErrorsAndExit();
    }

    public function changeEmailRequest() {
        $email = paramFromPost("email");
        $user = User::instanceFromId(SESSION_USER_ID);
        $user->email = $email;
        $user->_validateUserEmail();

        if (!ErrHelper::hasErrors()) {
            $uv = UserVerify::create('change_email', SESSION_USER_ID, $email);
            $uv->sendEmail();
        }


        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit(array("verify_id" => $uv->verify_id));
        }
    }

    public function changeMobileRequest() {
        $mobile_prefix = paramFromRequest('mobile_prefix');
        $mobile = $mobile_prefix . paramFromRequest('mobile');

        $mobile = SmsPi::cleanMobileNumber($mobile);

        if (!SmsPi::isValidMobileNumber($mobile)) {
            ErrHelper::raise('Invalid Mobile Number', 69, 'mobile');
        } else {
            User::isValidMobile($mobile, SESSION_USER_ID);
        }


        if (!ErrHelper::hasErrors()) {
            $uv = UserVerify::create('change_mobile', SESSION_USER_ID, $mobile);
            $uv->sendSMS();
        }
        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit(array("verify_id" => $uv->verify_id));
        }
    }

    public function updatePassword() {
        $old_password = paramFromRequest('old_password');
        $new_password = paramFromRequest('new_password');
        $new_password_again = paramFromRequest('confirm_password');

        if (strlen($new_password) < 5) {
            ErrHelper::raise("Password must be at least 5 characters.", "99", 'new_password');
        }

        User::changePassword($old_password, $new_password, $new_password_again);

        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit();
        }
    }
    public function deleteAccount() {
        $user = User::instanceFromId(SESSION_USER_ID);
        $user->deleteAccount();

        session_destroy();
        self::echoJsonSuccessAndExit();
    }

    public function updateFirebaseDetails() {
        self::loginRequiredAndEchoJsonError();
        User::updateFirebaseDetails(SESSION_USER_ID, paramFromRequest('firebase_token'), paramFromRequest('os_version'));
        if (hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit();
        }
    }
}
