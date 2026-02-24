<?

class RegisterController extends _Controller {
    public function index() {
        # set default $user object
        $user = new User();

        PageHelper::setMetaTitle("Freestuff NZ - Register");
        PageHelper::setMetaDescription("Freestuff NZ - Register");

        BreadcrumbHelper::addBreadcrumbs('Register');

        PageHelper::setMinifyPageCssName('register');
        PageHelper::addPageJavascriptOnInitial('common/plugins_js/typeahead-1.2.0/typeahead-1.2.0.bundle.js');
        PageHelper::addPageStylesheetFile('common/plugins_js/typeahead-1.2.0/typeahead.bundle.css');

        $ac_uid = uniqid('ac_');

        $_SESSION['ac_uid'] = $ac_uid;

        PageHelper::addJsVar('ac_uid', $ac_uid);

        PageHelper::setViews("views/register/form.php");

        include("templates/main_layout.php");
    }

    public function locationLookup() {
        $term = trim(strip_tags(paramFromGet("term")));

        // Using the autoCompleteAndDisconnect function the user will be sent the requested results but the process will continue running
        // after they disconnect
        GoogleMaps::autoCompleteAndDisconnect($term);
        GoogleMaps::updateLocations($term);
        exit();
    }


    public function processRegistration() {
        if (paramFromPost('lastname')) {
            raiseError('ro bot detected','lastname');
            echo json_encode(getErrors());
            exit();
        }
        $user = new User();
        $user->buildFromPostFrontEnd();

        if ($user->validate("register")) {

            if ($user->registerUser()) {

                $uv = UserVerify::create('register_email', $user->user_id, $user->email);
                $uv->sendEmail();
                echo json_encode(array("success" => true, "verify_id" => $uv->verify_id));
            } else {
                echo json_encode(getErrors());
            }
        } else {

            $errors = getErrors();
            if (isset($errors['email'])) {
                if (stristr($errors['email'], 'account')) {
                    $errors['email'] = $errors['email'] . "<br/><a href='" . APP_URL . "login/forgotten_password'>Click here</a> if you have forgotten your password.";
                }
            }
            echo json_encode($errors);
        }
        exit();
    }

    public function emailVerified($verify_id, $code) {
        if (!FloodControlHelper::allow('uv_attempt'.$_SERVER["REMOTE_ADDR"],50, 2614400 )) {
            ErrHelper::raise('Flood Control Engaged',203);
            return true;
        }
        $uv = UserVerify::instanceFromId($verify_id);
        if ($uv->checkAndExpire($code)) {
            $user_id = User::userIdFromEmail($uv->data);
            $user = User::instanceFromId($user_id);
            $user->manualEmailValidate($user_id);
            redirect(APP_URL . "register/phone_verify_needed/".$user_id);
        } else {
            redirect(APP_URL."/user_verify/email_form/".$verify_id);
        }
    }

    public function phoneVerifyNeeded($user_id) {
        PageHelper::setMetaTitle("Freestuff NZ - Register");
        PageHelper::setMetaDescription("Freestuff NZ - Register");

        BreadcrumbHelper::addBreadcrumbs('Register', APP_URL . 'register');
        BreadcrumbHelper::addBreadcrumbs('Phone Verification');

        PageHelper::setViews("views/register/phone_verify_required.php");

        include("templates/main_layout.php");
    }

    public function sendCodeViaSms() {
        $user_id = (int)paramFromRequest('user_id');
        $mobile_prefix = paramFromRequest('mobile_prefix');
        $mobile = $mobile_prefix . paramFromRequest('mobile');
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
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            echo json_encode(array("success" => true, "verify_id" => $uv->verify_id));
        }
    }

    public function sendCodeViaLandline() {
        $user_id = (int)paramFromRequest('user_id');
        $mobile_prefix = paramFromRequest('landline_prefix');
        $mobile = $mobile_prefix . paramFromRequest('landline');
        $mobile = SmsPi::cleanMobileNumber($mobile);

        User::isValidLandline($mobile, $user_id);

        if (!ErrHelper::hasErrors()) {
            $uv = UserVerify::create('register_landline', $user_id, $mobile);
            $uv->sendLandline();
        }
        if (ErrHelper::hasErrors()) {
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            echo json_encode(array("success" => true, "verify_id" => $uv->verify_id));
        }
    }

    public function phoneVerified($verify_id, $code) {
        if (!FloodControlHelper::allow('uv-attempt'.$_SERVER["REMOTE_ADDR"],50, 2614400 )) {
            ErrHelper::raise('Flood Control Engaged',203);
            return true;
        }
        $uv = UserVerify::instanceFromId($verify_id);
        if ($uv->checkAndExpire($code)) {
            $user = User::instanceFromId($uv->user_id);
            $user->setMobile($uv->data, true);
            $_SESSION["session_user_id"] = $uv->user_id;
            User::authenticate($uv->data, BACKDOOR);
            redirect(APP_URL . "register/completed");
        } else {
            redirect(APP_URL."/user_verify/mobile_form/".$verify_id);
        }
    }

    public function completed() {
        PageHelper::setMetaTitle("Freestuff NZ - Register");
        PageHelper::setMetaDescription("Freestuff NZ - Register");

        BreadcrumbHelper::addBreadcrumbs('Register', APP_URL . 'register');
        BreadcrumbHelper::addBreadcrumbs('Completed');

        PageHelper::setViews("views/register/completed.php");

        include("templates/main_layout.php");
    }
}

