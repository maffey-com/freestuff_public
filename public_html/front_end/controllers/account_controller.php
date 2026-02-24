<?
class AccountController extends _Controller {
    public function logout() {
        User::logout();
        redirect(APP_URL . 'home');
    }
    public function logoutAndLogin() {
        User::logout();
        redirect(APP_URL . 'login');
    }

    public function logoutViaAjax() {
        User::logout();

        $output = array();
        $output['success'] = hasErrors() ? FALSE : TRUE;

        echo json_encode($output);
        die();
    }

    public function updateName() {
        self::loginRequiredAndRedirect();

        $first_name      = paramFromPost('firstname');
        $user            = User::instanceFromId(SESSION_USER_ID);
        $old_user = clone($user);

        $user->firstname = $first_name;
        if ($user->validate("update_name")) {
            $user->updateFirstname();
            if ($old_user->firstname != $user->firstname) {
                UserHistory::insertHistory(SESSION_USER_ID, 'firstname', $old_user->firstname, $user->firstname);
            }
            if ($old_user->location != $user->location) {
                UserHistory::insertHistory(SESSION_USER_ID, 'location', $old_user->location, $user->location);
            }

            MessageHelper::setSessionSuccessMessage('Your account details have been updated.');
            redirect(SITE_URL . 'account/edit_name');

        } else {
            $this->editName();
        }
    }

    public function editName() {
        self::loginRequiredAndRedirect();

        $user = User::instanceFromId(SESSION_USER_ID);

        TemplateHandler::setSelectedMainTab('my_account');
        PageHelper::setViews("views/my_account/edit_name.php");

        PageHelper::setMetaTitle('Freestuff NZ - Edit account details');
        PageHelper::setMetaDescription('Freestuff NZ - Edit account details');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL.'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Name');

        include("templates/main_layout.php");
    }

    public function updateLocation() {
        self::loginRequiredAndRedirect();
        $user = User::instanceFromId(SESSION_USER_ID);

        $district = new District();
        $district->retrieveFromID(paramFromPost('district_id'));

        $old_user = clone($user);

        $user->buildFromPostFrontEnd();

        if ($district->district) {
            $user->updateLocation($district);
                UserHistory::insertHistory(SESSION_USER_ID, 'location', '', District::display2($district->district_id));

            MessageHelper::setSessionSuccessMessage('Your closest district has been updated.');
            echo json_encode(array("success" => TRUE));
        } else {
            ErrHelper::raise('Please select a district', 69, 'district_id');
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        }
    }

    public function editLocation() {
        self::loginRequiredAndRedirect();
        $user = User::instanceFromId(SESSION_USER_ID);

        TemplateHandler::setSelectedMainTab('my_account');
        PageHelper::setViews("views/my_account/edit_location.php");
        PageHelper::setMetaTitle('Freestuff NZ - Edit account details');
        PageHelper::setMetaDescription('Freestuff NZ - Edit account details');
        PageHelper::setMinifyPageCssName('change_location');
        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Closest District');
        include("templates/main_layout.php");
    }

    public function editEmail() {
        self::loginRequiredAndRedirect();
        $user = User::instanceFromId(SESSION_USER_ID);

        TemplateHandler::setSelectedMainTab('my_account');
        PageHelper::setViews("views/my_account/edit_email.php");
        PageHelper::setMetaTitle('Freestuff NZ - Change email address');
        PageHelper::setMetaDescription('Freestuff NZ - Change email address');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Email');

        include("templates/main_layout.php");
    }

    public function updateEmail() {
        self::loginRequiredAndEchoJsonError();
        $user = User::instanceFromId(SESSION_USER_ID);

        $email = paramFromPost('email');
        $user->email = $email;
        $user->_validateUserEmail();

        if (!ErrHelper::hasErrors()) {
            $uv = UserVerify::create('change_email', SESSION_USER_ID, $email);
            $uv->sendEmail();
        }
        if (ErrHelper::hasErrors()) {
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            echo json_encode(array("success" => TRUE, "verify_id" => $uv->verify_id));
        }
        exit();
    }

    public function emailUpdateVerified($verify_id, $code) {
        $uv = UserVerify::instanceFromId($verify_id);
        if ($uv->checkAndExpire($code)) {
            $user = User::instanceFromId($uv->user_id);
            $user->changeEmail($uv->data);
        }

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('edit_email');
        PageHelper::setViews("views/my_account/edit_email_success.php");
        PageHelper::setMetaTitle('Freestuff NZ - Change email address');
        PageHelper::setMetaDescription('Freestuff NZ - Change email address');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Email', APP_URL . 'account/edit_email');
        BreadcrumbHelper::addBreadcrumbs('Email Address Verified');

        include("templates/main_layout.php");
    }

    public function editMobile() {
        self::loginRequiredAndRedirect();
        $user = User::instanceFromId(SESSION_USER_ID);

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('edit_mobile');
        PageHelper::setViews("views/my_account/edit_mobile.php");
        PageHelper::setMetaTitle('Freestuff NZ - Change mobile number');
        PageHelper::setMetaDescription('Freestuff NZ - Change mobile number');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL .'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Mobile Number');

        include("templates/main_layout.php");
    }

    public function updateMobile() {
        self::loginRequiredAndRedirect();

        $mobile_prefix   = paramFromRequest('mobile_prefix');
        $mobile          = $mobile_prefix . paramFromRequest('mobile');
        $mobile          = SmsPi::cleanMobileNumber($mobile);

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
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            echo json_encode(array("success" => TRUE, "verify_id" => $uv->verify_id));
        }
    }

    public function mobileUpdateVerified($verify_id, $code) {
        self::loginRequiredAndRedirect();
        $uv = UserVerify::instanceFromId($verify_id);

        if ($uv->checkAndExpire($code)) {
            $user = User::instanceFromId($uv->user_id);
            $user->setMobile($uv->data, TRUE);
        }

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('edit_mobile');
        PageHelper::setViews("views/my_account/edit_mobile_success.php");
        PageHelper::setMetaTitle('Freestuff NZ - Change email address');
        PageHelper::setMetaDescription('Freestuff NZ - Change email address');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Mobile Number', APP_URL . 'account/edit_mobile');
        BreadcrumbHelper::addBreadcrumbs('Verify Mobile Number');

        include("templates/main_layout.php");
    }

    public function updatePassword() {
        $old_password       = paramFromRequest('old_password');
        $new_password       = paramFromRequest('new_password');
        $new_password_again = paramFromRequest('new_password_again');

        if (strlen($new_password) < 5) {
            ErrHelper::raise("Password must be at least 5 characters.", '69', 'new_password');
        }

        $user = new User();
        $user->changePassword($old_password, $new_password, $new_password_again);

        if (ErrHelper::hasErrors()) {
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        } else {
            MessageHelper::setSessionSuccessMessage("Password has been updated.");
            echo "1";
        }
    }

    public function editPassword() {
        self::loginRequiredAndRedirect();
        $user = User::instanceFromId(SESSION_USER_ID);

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('edit_password');
        PageHelper::setViews("views/my_account/edit_password.php");
        PageHelper::setMetaTitle('Freestuff NZ - Change password');
        PageHelper::setMetaDescription('Freestuff NZ - Change password');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Change Password');

        include("templates/main_layout.php");
    }

    public function delete() {
        self::loginRequiredAndRedirect();
        $user = User::instanceFromId(SESSION_USER_ID);

        TemplateHandler::setSelectedMainTab('my_account');
        PageHelper::setViews("views/my_account/suspend_account.php");
        PageHelper::setMetaTitle('Freestuff NZ - Delete Account');
        PageHelper::setMetaDescription('Freestuff NZ - Delete Account');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Delete Account');

        include("templates/main_layout.php");
    }

    public function deleteConfirm() {
        if (!SecurityHelper::isLoggedIn()) {
            ErrHelper::raise('Please log into Freestuff first.');
        }
        $user = User::instanceFromId(SESSION_USER_ID);
        $success = true;
        try {
            $user->deleteAccount();
        } catch (Exception $e) {
            $success = false;
            writeLog($e);
        }
        User::logout();
        if ($success) {
            MessageHelper::setSessionSuccessMessage('Your account has been deleted.');
        } else {
            MessageHelper::setSessionErrorMessage('There was a problem deleting your account. Please try again later.');
        }
        redirect(APP_URL . 'home');
    }

    public function deleteAllSavedSearches() {
        $_SESSION['wherewasi'] = '/account/delete_all_saved_searches';
        self::loginRequiredAndRedirect();
        if (SESSION_USER_ID && SavedSearch::deleteAllForUser(SESSION_USER_ID)) {
            MessageHelper::setSessionSuccessMessage("Your saved searches have been deleted. You will no longer receive notifications for them.");
        } else {
            MessageHelper::setSessionErrorMessage("Sorry, we couldn't unsubscribe you. Please try again.");
        }
        redirect(APP_URL . 'home');
    }

}
