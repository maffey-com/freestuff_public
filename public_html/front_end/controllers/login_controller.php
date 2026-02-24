<?
class LoginController extends _Controller {
    public function __construct() {
        PageHelper::setMinifyPageCssName('login');
    }

    public function index() {
        self::redirectIfAlreadyLoggedIn();

        PageHelper::setMetaTitle("Freestuff NZ - Login");
        PageHelper::setMetaDescription("Freestuff NZ - Login");

        PageHelper::setViews("views/login/login_form.php");

        BreadcrumbHelper::addBreadcrumbs('Login');

        # set default $user object
        include("templates/main_layout.php");
    }

    public function processLogin() {
        self::redirectIfAlreadyLoggedIn();

        $username = paramFromPost('username');
        $password = paramFromPost('password');
        $remember_me = (paramFromPost('remember') == 'y');

        if (User::authenticate($username, $password, $remember_me)) {
            if (isset($_SESSION['wherewasi'])) {
                redirect($_SESSION['wherewasi']);
            } else {
                redirect(APP_URL . 'home');
            }
        } else {

            if(isset($_SESSION['inactive'])) {
                redirect(APP_URL . 'account/inactive');
            }

            if ($mobile_not_validated_error = ErrHelper::getErrorWithCode(ERR_USER_ACCOUNT_NOT_MOBILE_VALIDATED)) {
                redirect(APP_URL . 'register/phone_verify_needed/'.$mobile_not_validated_error->data["user_id"]);
            }

            if ($email_not_validated_error = ErrHelper::getErrorWithCode(ERR_USER_ACCOUNT_NOT_EMAIL_VALIDATED)) {

                $uv = UserVerify::create('register_email', $email_not_validated_error->data["user_id"],$email_not_validated_error->data["email"]);
                $uv->sendEmail();
                redirect(APP_URL . 'user_verify/email_form/'.$uv->verify_id);
            }

            MessageHelper::setSessionErrorMessage(ErrHelper::getAllMessages());
            redirect(APP_URL . 'login');

        }
    }

    public function forgottenPasswordEmail() {
        $email = paramFromRequest('email');
        $user_id = User::userIdFromEmail(paramFromRequest('email'));
        if ($user_id) {
            $uv = UserVerify::create('forgotten_password', $user_id,$email);
            $uv->sendEmail();
        } else {
            ErrHelper::raise('No such user', 99, 'email');
        }
        if (ErrHelper::hasErrors()) {
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            echo json_encode(array("success" => true, "verify_id" => $uv->verify_id));
        }
    }

    public function forgottenPasswordSMS() {
        $mobile = paramFromRequest('mobile_prefix').paramFromRequest('mobile');
        $user_id = User::userIdFromMobile($mobile);
        if ($user_id) {
            $uv = UserVerify::create('forgotten_password', $user_id,$mobile);
            $uv->sendSMS();
        } else {
            ErrHelper::raise('No such user', 99, 'mobile');
        }
        if (ErrHelper::hasErrors()) {
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            echo json_encode(array("success" => true, "verify_id" => $uv->verify_id));
        }
    }

    public function forgottenPassword() {
        self::redirectIfAlreadyLoggedIn();

        PageHelper::setMetaTitle("Freestuff NZ - Forgotten Password");
        PageHelper::setMetaDescription("Freestuff NZ - Forgotten Password");

        BreadcrumbHelper::addBreadcrumbs('Login', APP_URL . 'login');
        BreadcrumbHelper::addBreadcrumbs('Forgotten Password');

        PageHelper::setMinifyPageCssName('forgotten_password.css');
        PageHelper::addPageStylesheetFile('css/forgotten_password.css');

        PageHelper::setViews("views/login/forgotten_password.php");
        include("templates/main_layout.php");
    }


    public function forgottenPasswordResetForm($verify_id,$code) {
        $uv = UserVerify::instanceFromId($verify_id);
        if (!$uv->checkCode($code) || !$uv->verify_id) {
            redirect(APP_URL."/user_verify/email_form/".$verify_id);
        }

        PageHelper::setMetaTitle("Freestuff NZ - Reset Password");
        PageHelper::setMetaDescription("Freestuff NZ - Reset Password");

        BreadcrumbHelper::addBreadcrumbs('Login', APP_URL . 'login');
        BreadcrumbHelper::addBreadcrumbs('Reset Password');

        PageHelper::setViews("views/password_reset/reset_password.php");

        # set default $user object
        include("templates/main_layout.php");
    }

    public function forgottenPasswordProcessReset() {
        $new_password = paramFromPost('new_password');
        $confirm_password = paramFromPost('confirm_password');
        $verify_id = paramFromPost('verify_id');
        $code = paramFromPost('code');


        $uv = UserVerify::instanceFromId($verify_id);
        if (!$uv->checkCode($code) || !$uv->verify_id) {
            ErrHelper::raise("Password reset verification expired",99);
        }

        if (empty($new_password)) {
            ErrHelper::raise("You must enter a password", 99,"new_password");
        } elseif (empty($confirm_password)) {
            ErrHelper::raise("You must repeat your new password", 99,"confirm_password");
        } elseif ($new_password != $confirm_password) {
            ErrHelper::raise("New passwords do not match", 99,"new_password");
        } elseif (strlen($new_password) < 5) {
            ErrHelper::raise("Your new password must have at least 5 characters", 99,"new_password");
        }

        if (!ErrHelper::hasErrors()) {
            $user = User::instanceFromId($uv->user_id);

            if (empty($user->user_id)) {
                ErrHelper::raise("User record not found",69);
            } else {
                if (User::updatePassword($uv->user_id, $new_password, $confirm_password)) {
                    $uv->expire();

                    MessageHelper::setSessionSuccessMessage('Your password has been updated');
                    echo "1";
                    exit();
                }
            }
        }

        echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        exit();
    }
}

