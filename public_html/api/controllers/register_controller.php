<?php

class RegisterController extends _Controller {
    public function __construct() {

    }

    public function processRegistration() {
        $user = new User();
        $user->buildFromPostFrontEnd();
        if ($user->validate("register")) {
//            echo json_encode(['success' => true]);
            if ($user->registerUser()) {
                $uv = UserVerify::create('register_email',$user->user_id,$user->email);
                $uv->sendEmail();
                self::echoJsonSuccessAndExit(array("verify_id" => $uv->verify_id, 'user_id' => $user->user_id));
            } else {

//                echo json_encode(getErrors());
                self::echoJsonErrorsAndExit();
            }
        } else {
            if (fieldHasError('email') == 'Invalid email') {
                unset($GLOBALS['errors']['email']);
            }
            self::echoJsonErrorsAndExit();
        }
        exit();
    }

    public function sendCodeViaSms() {
        $user_id = intval(trim(paramFromPost("user_id"), '"'));
        $mobile = paramFromPost('mobile');
        $mobile = SmsPi::cleanMobileNumber($mobile);

        if (!SmsPi::isValidMobileNumber($mobile)) {
            ErrHelper::raise('Invalid Mobile Number', 69, 'mobile');
        } else {
            User::isValidMobile($mobile, $user_id);
        }

        if (!ErrHelper::hasErrors()) {
            $uv = UserVerify::create('register_mobile', $user_id, $mobile);
            $uv->sendSMS();
        }
        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit(array("verify_id" => $uv->verify_id));
        }
    }

    public function hasMobileBeenValidated() {
        $out = array();
        $out['success'] = $this->temp_user->mobile_validated ? true : false;
        echo json_encode($out);
        exit();
    }

    public function validationProgress() {
        self::loginRequiredAndEchoJsonError();
        $user = User::instanceFromId(SESSION_USER_ID);
        $email_validated = !empty($user->email_validated);
        $mobile_validated = !empty($user->mobile_validated);
        if (!$email_validated) {
            $uv = UserVerify::create('register_email',$user->user_id, $user->email);
            $uv->sendEmail();
            $verify_id = $uv->verify_id;
        }

        $out = [];
        $out['email_validated'] = $email_validated;
        $out['email'] = $user->email;
        $out['mobile_validated'] = $mobile_validated;
        $out['mobile'] = $user->mobile;
        if (isset($verify_id)) {
            $out['verify_id'] = $verify_id;
        }
        self::echoJsonSuccessAndExit($out);
    }

    public function sendValidationSms() {
        if ($this->temp_user->mobile_validated) {
            ErrHelper::raise('Mobile already validated', MOBILE_ALREADY_VALIDATED);
        } else {
            $mobile_prefix = paramFromPost('mobile_prefix');
            $mobile = paramFromPost('mobile');

            $this->temp_user->mobile = empty($mobile) ? '' : $mobile_prefix . $mobile;
            $this->temp_user->updateAndValidatePhone(FALSE);
            ErrHelper::backwardsCompatibleBuild(getErrors());
        }

        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit(array());
        }
    }

    public function validateBySMS() {
        $mobile_activation_code = paramFromPost('mobile_activation_code');
        $validated = User::validateMobile($this->temp_user->mobile, $mobile_activation_code);
        if ($validated) {
            self::echoJsonSuccessAndExit(array());

        } else {
            ErrHelper::raise('Invalid activation code', 99, 'mobile_activation_code');
            self::echoJsonErrorsAndExit();
        }
    }

    public function changeEmailConfirm() {
        $user = User::instanceFromId($_SESSION["session_user_id"]);

        $pending_id = (int)paramFromGet('id');

        if (!$user->changeEmailConfirmed($pending_id)) {
            ErrHelper::raise('Failed to change email address', 99);
        }

        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit(array("email" => $user->email));
        }
    }


}
