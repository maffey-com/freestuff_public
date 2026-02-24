<?php
class UserController extends _Controller {

    public function index() {
        $filter = new FilterHelper('users');

        $sql = "SELECT * 
        		FROM user";
	    if ($filter->name) {
	    	$tmp_search = quoteSQL("%" . $filter->name . "%");

			$sql .= " 
					WHERE (
                        firstname LIKE " . $tmp_search . " 
                        OR email LIKE " . $tmp_search . "
                        OR user_id = " . (int)$filter->name . " 
                        OR mobile LIKE " . $tmp_search . "
					)";
		}

		$dw_user = new DataWindowHelper("user", $sql, "firstname", "asc");
        $dw_user->run();

        BreadcrumbHelper::addBreadcrumbs("Users", APP_URL . 'user');

        TemplateHandler::setSideMenu("Admin", "Users");
        TemplateHandler::setPageTitle("Users");
        TemplateHandler::setMainView("views/user/list.php");

        include("templates/standard.php");
    }

    public function filterList() {
        $filter = new FilterHelper('users');
        redirect(APP_URL . 'user');
    }

    public function edit($user_id) {
        BreadcrumbHelper::addBreadcrumbs("Users", APP_URL . 'user');
        BreadcrumbHelper::addBreadcrumbs("Edit");

        TemplateHandler::setSideMenu("Admin", "Users");
        TemplateHandler::setPageTitle("Users", "Edit an existing user");
        TemplateHandler::setMainView("views/user/edit.php");

        $user = User::instanceFromId($user_id);

        include("templates/standard.php");
    }

    public function save($user_id) {
        $user = User::instanceFromId($user_id);
        $user->buildFromPost();

        if (empty($user->user_id)) {
            raiseError("You cannot create user this way.");

        } elseif ($user->update()) {
            echo "1";
            MessageHelper::setSessionSuccessMessage("User account has been updated");
            die();
        }

        echo json_encode(getErrors());
        die();
    }

    public function delete($user_id) {
        $user = User::instanceFromId($user_id);
        if ($user->user_id && $user->deleteAccount()) {
            MessageHelper::setSessionSuccessMessage("User has been deleted.");

		} else {
			MessageHelper::setSessionErrorMessage("Fail to delete user.");
		}

        redirect(APP_URL . 'user');
    }

    public function unban($user_id) {
        $user = User::instanceFromId($user_id);
		if ($user->unban()) {
            MessageHelper::setSessionSuccessMessage("User has been unbanned.");

        } else {
            MessageHelper::setSessionErrorMessage("Fail to unban user.");
        }

        redirect(APP_URL . 'user');
    }

    public function ban($user_id, $redirect = TRUE) {
        $user = User::instanceFromId($user_id);
		if ($user->ban(paramFromRequest('reason'))) {
            MessageHelper::setSessionSuccessMessage("User has been banned.");

        } else {
            MessageHelper::setSessionErrorMessage("Fail to ban user.");
        }

        if ($redirect) {
            redirect(APP_URL . 'user');
        }
    }

    public function banReporter($user_id) {
        $this->ban($user_id, FALSE);

        redirect(APP_URL . 'report');
    }

    public function validateEmail($user_id) {
        if (User::manualEmailValidate($user_id)) {
            MessageHelper::setSessionSuccessMessage("Email has been validated.");
        } else {
            MessageHelper::setSessionErrorMessage("Fail to validate email.");
        }

        redirect(APP_URL . 'user/edit/' . $user_id);
    }

    public function validateMobileNumber($user_id) {
		if (User::manualMobileValidate($user_id)) {
            MessageHelper::setSessionSuccessMessage("Mobile number has been validated.");

        } else {
            MessageHelper::setSessionErrorMessage("Fail to validate mobile number.");
        }

        redirect(APP_URL . 'user/edit/' . $user_id);
    }

    public function changeOtherPassword($user_id) {
        $user_id = (int)$user_id;

        $user = new User();
        $user->retrieveFromID($user_id);

        /*BreadcrumbHelper::addBreadcrumbs("Change user password");

        TemplateHandler::setSideMenu("Admin", "Change user password");
        TemplateHandler::setPageTitle("Admin", "Change user password");
        TemplateHandler::setMainView("views/user/change_member_password.php");
        */

        include("views/user/change_other_password.php");
    }

    public function saveOtherPassword($user_id) {
        $user_id = (int)$user_id;

        $user = new User();
        $user->retrieveFromID($user_id);
        $user->user_id = (int)$user->user_id;

        $new_password = paramFromPost('password');

        if (empty($user->user_id)) {
            ErrHelper::raise("User not found.", "69");

        } elseif (User::updatePassword($user_id, $new_password, $new_password)) {
            MessageHelper::setSessionSuccessMessage("Password has been updated for User ID: " . $user->user_id);
            redirect(APP_URL .'user');
        }

        $this->index();
    }

    public function changePassword() {
        BreadcrumbHelper::addBreadcrumbs("Change password");

        TemplateHandler::setSideMenu("Admin", "Change password");
        TemplateHandler::setPageTitle("Admin", "Change password");
        TemplateHandler::setMainView("views/user/change_password.php");

        include("templates/standard.php");
    }

    public function savePassword() {
        if (User::changePassword(paramFromPost('old_password'), paramFromPost('password'), paramFromPost('confirm_password'))) {
            MessageHelper::setSessionSuccessMessage("Password has been updated.");
            echo '1';
            die();
        }

        echo json_encode(getErrors());
        die();
    }

    public function history($user_id) {
        $user = User::instanceFromId($user_id);

        $sql = "SELECT l.title item, l.listing_date date, '' additionalInfo, 'Listed' type, l.listing_id
				FROM listing l
				WHERE l.user_id = " . $user_id . "
				UNION
				SELECT l.title item, r.report_date date, r.status additionalInfo, 'Reported' type, l.listing_id
				FROM report r
				JOIN listing l ON l.listing_id = r.listing_id
				WHERE r.user_id = " . $user_id . "
				UNION
				SELECT l.title item, t.thumb_date date, t.up_down additionalInfo, 'Thumb' type, l.listing_id
				FROM thumb t
				JOIN listing_request r ON t.request_id = r.request_id
				JOIN listing l ON l.listing_id = r.listing_id
				WHERE t.requester_id = " . $user_id . "
				AND t.up_down in ('u','d')
				UNION
				SELECT l.title item, lr.request_timestamp date, '' additionalInfo, 'Requested' type, l.listing_id
				FROM listing_request lr
				JOIN listing l ON l.listing_id = lr.listing_id
				WHERE lr.user_id = " . $user_id;

        # avoid paging on popup
		$dw_user = new DataWindowHelper("user", $sql, "date", 'DESC', 1000);
		$dw_user->run();

		$sql = "SELECT COUNT(DISTINCT(r.report_id)) FROM report r
                JOIN listing l ON l.user_id = ".quoteSQL($user_id)."  AND l.listing_id = r.listing_id";
		$times_reported = runQueryGetFirstValue($sql);

		$sql = "SELECT COUNT(DISTINCT(r.report_id)) FROM report r
                WHERE user_id = ".quoteSQL($user_id);
        $reported_times = runQueryGetFirstValue($sql);

		require_once('views/user/history.php');
    }

    public function brevo() {
        Brevo::sendUserToBrevo(69);
    }

}

