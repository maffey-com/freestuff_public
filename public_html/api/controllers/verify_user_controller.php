<?

class VerifyUserController extends _Controller {
    public function __construct() {
    }

    public function checkCode($verify_id) {
        $code = paramFromPost('code');
        $uv = new UserVerify();
        $uv->retrieveFromId($verify_id);
        $uv->checkCode($code);
        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            $array = $this->doAfterVerify($uv);

            $array["verify_type"] = $uv->verify_type;
            $array["user_id"] = $uv->user_id;
            $array["data"] = $uv->data;

            self::echoJsonSuccessAndExit($array);
        }
    }

    public function sendEmail($verify_id) {
        $uv = new UserVerify();
        $uv->retrieveFromId($verify_id);
        $uv->sendEmail();
        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit();
        }
    }

    public function codeProcess() {
//        if (!FloodControlHelper::allow('uv_attempt'.$_SERVER["REMOTE_ADDR"],50, 2614400 )) {
//            raiseError('To many attempts', 'message');
//            self::echoJsonErrorsAndExit();
//        }

        $code = paramFromPost("code");
        $verify_id = paramFromPost("verification_id");

        $uv = UserVerify::instanceFromId($verify_id);
        if ($uv->verify_id) {
            if ($uv->verify_type == 'forgotten_password') {
                $this->checkCode($verify_id);
                exit();
            } elseif ($uv->checkAndExpire($code)) {
                $out = [];
                $out['success'] = false;
                if ($uv->verify_type == 'register_email') {
                    User::manualEmailValidate($uv->user_id);
                    $out['success'] = true;
                } elseif ($uv->verify_type == 'register_mobile') {
                    $user = User::instanceFromId($uv->user_id);
                    $user->setMobile($uv->data, true);
                    if ($out = User::authenticate($uv->data, BACKDOOR)) {
                        $out['session_id'] = session_id();
                        $out['success'] = true;
                    }
                }
                if ($out['success']) {
                    echo json_encode($out);
                    exit();
                }
            }
        }

        raiseError('Invalid Code', 'message');
        self::echoJsonErrorsAndExit();
    }


    public function sendSMS($verify_id) {
        if (!FloodControlHelper::allow('uv_attempt'.$_SERVER["REMOTE_ADDR"],20, 2614400 )) {
            raiseError('To many attempts', 'message');
            self::echoJsonErrorsAndExit();
        }

        $uv = new UserVerify();
        $uv->retrieveFromId($verify_id);
        $uv->sendSMS();
        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            self::echoJsonSuccessAndExit();
        }
    }
    public function doAfterVerify($uv) {
        $array = array();
        $user = User::instanceFromId($uv->user_id);
        switch ($uv->verify_type) {
            case 'change_email':
                $user->changeEmail($uv->data);
                $uv->expire();

                break;
            case 'change_mobile':
                $user->setMobile($uv->data,true);
                $uv->expire();
                break;
            case 'register_email':
                $user->manualEmailValidate($uv->user_id);
                $uv->expire();
                break;
            case 'register_mobile':
                $user->setMobile($uv->data, true);
                $array = User::authenticate($uv->data, BACKDOOR,true);
                $uv->expire();
                break;
        }
        return $array;

    }

    // Called from app verify email page to check if the email has been verified outside of the app
    public function checkEmailVerified($user_id) {
        $user = User::instanceFromId($user_id);
        if (!empty($user->email_validated)) {
            self::echoJsonSuccessAndExit();
        } else {
            raiseError('Email not verified');
            self::echoJsonErrorsAndExit();
        }
    }

    public function resendEmail($verify_id) {
        $uv = UserVerify::instanceFromId($verify_id);
        $uv->sendEmail();
        if (hasErrors()) {
            self::echoJsonErrorsAndExit();
        }
        self::echoJsonSuccessAndExit();
    }
}
