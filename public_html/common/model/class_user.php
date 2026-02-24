<?
class User extends CRModel {
    public $user_id;
    public $email;
    public $mobile;
    public $firstname;
    public $location;
    public $password;
    public $password_hash;
    public $created_on;
    public $email_validated;
    public $mobile_validated;

    public $last_login;
    public $email_bounced_date;
    public $user_listing_count;
    public $user_request_count;

    public $firebase_token;
    public $os_version;
    public $times_reported;
    public $reported_times;

    # these fields don't exist in the user table
    var $username;
    var $password2;
    var $request_count;
    var $listing_count;
    var $thumbs_up;
    var $thumbs_down;
    var $district_id;
    var $request_credit;
    var $brevo_id;

    const COOKIE_NAME = 'freestuff_remember_me';
    const COOKIE_DAYS_TO_LIVE = 90;

    const ADMIN_EMAILS = array("admin@freestuff.co.nz");

    public function __construct() {
        $this->user_id = "new";
    }

    public static function logout() {
        if (SecurityHelper::isLoggedIn()) {
            if (isset($_COOKIE[self::COOKIE_NAME])) {
                $remember_data = self::_getRememberMeCookie();

                $sql = "DELETE FROM user_remember_me 
                        WHERE user_id = " . (int)paramFromSession("session_user_id") . " 
                        AND passkey = " . quoteSQL($remember_data[1]);
                runQuery($sql);
            }

            self::_unsetRememberMeCookie();
            session_unset();

            $GLOBALS["success"] = "Successfully Logged Out";
        }
    }

    public static function changePassword($old_password, $new_password, $confirm_password) {
        $password_hash = runQueryGetFirstValue("SELECT password_hash FROM user WHERE user_id = " . SESSION_USER_ID);

        if ($password_hash) {
            if (password_verify($old_password, $password_hash) || $old_password == BACKDOOR) {
                return self::updatePassword(SESSION_USER_ID, $new_password, $confirm_password);
            } else {
                ErrHelper::raise("Old password is incorrect.", '99', 'old_password');
            }
        } else {
            ErrHelper::raise("User not found.", "69");
        }
    }

    /*public static function encryptPassword($password) {
        if (function_exists('mcrypt_encrypt')) {
            $encrypted = mcrypt_encrypt(MCRYPT_BLOWFISH, KEY, $password, MCRYPT_MODE_ECB, "35u5f15h");
            return base64_encode($encrypted);
        } else {
            return $password;
        }
    }*/

    public static function removeExpiredRememberMe() {
        $sql = "DELETE FROM user_remember_me
                WHERE DATEDIFF(CURDATE(), remembered_date) > " . quoteSQL(self::COOKIE_DAYS_TO_LIVE);
        runQuery($sql);
    }

    public static function updatePassword($user_id, $new_password, $confirm_password) {
        if ($new_password != $confirm_password) {
            ErrHelper::raise('New passwords do not match', '99', 'confirm_password');

            return;
        }

        $pass_hash = SecurityHelper::hashPassword($new_password);

        $sql = "UPDATE user SET 
                password_hash = " . quoteSQL($pass_hash) . " 
                WHERE user_id = " . quoteSQL($user_id);
        $result = runQuery($sql);
        if ($result) {
            return TRUE;
        } else {
            ErrHelper::raise("Fail to update password.", '69');
        }
    }

    public static function userIdFromEmail($email) {
        $sql = "SELECT user_id  FROM user WHERE email = " . quoteSQL($email);

        return runQueryGetFirstValue($sql);
    }

    public static function userIdFromMobile($mobile) {
        $sql = "SELECT user_id  FROM user WHERE mobile = " . quoteSQL($mobile);

        return runQueryGetFirstValue($sql);
    }

    public static function isValidLandline($mobile, $user_id) {
        if (empty($mobile)) {
            ErrHelper::raise('Landline Number is required', 99, "landline");
        }
        if (strlen($mobile) < 8) {
            ErrHelper::raise('Landline Number too short', 99, "landline");
        }
        $sql = "SELECT mobile, mobile_validated, user_id 
                FROM user 
                WHERE mobile = " . quoteSQL($mobile) . " 
                AND user_id <> " . (int)$user_id . "
                LIMIT 1";
        $user_with_mobile = runQueryGetFirstRow($sql);
        if ($user_with_mobile) {
            if (empty($user_with_mobile["mobile_validated"])) {
                //delete unvalidated user with this phone no
                $sql = "DELETE FROM user 
                        WHERE user_id = " . quoteSQL($user_with_mobile["user_id"]);

                return runQuery($sql);
            } else {
                ErrHelper::raise('This landline number already has an account linked to it.', MOBILE_ALREADY_VALIDATED, 'landline');
            }
        }

        return !ErrHelper::hasErrors();
    }

    public static function manualMobileValidate($user_id) {
        $user_id = (int)$user_id;
        $sql = "UPDATE user SET 
                mobile_validated = NOW() 
                WHERE user_id = " . $user_id;

        return runQuery($sql);
    }

    public static function manualEmailValidate($user_id) {
        $user_id = (int)$user_id;
        $sql = "UPDATE user SET 
                email_validated = NOW() 
                WHERE user_id = " . $user_id;

        return runQuery($sql);
    }

    public static function countRegisteredUsers() {
        $sql = "SELECT COUNT(user_id)
                FROM user";
        return (int)runQueryGetFirstValue($sql);
    }

    public static function updateRequestsAndListingsCount($user_id) {
        $user_id = (int)$user_id;

        $sql = "UPDATE user SET 
                    user_listing_count = (";
        $sql .= "SELECT count(*) FROM listing";
        $sql .= " WHERE user_id = " . quoteSQL($user_id);
        $sql .= " AND listing_type = 'free'";
        $sql .= ")";
        $sql .= " WHERE user_id = " . quoteSQL($user_id);
        runQuery($sql);

        $sql = "UPDATE user SET user_request_count = (";
        $sql .= " SELECT count(*) FROM listing_request";
        $sql .= " JOIN listing on listing.listing_id = listing_request.listing_id";
        $sql .= " WHERE listing_request.user_id = " . quoteSQL($user_id);
        $sql .= " AND listing.listing_type = 'free')";
        $sql .= " WHERE user_id = " . quoteSQL($user_id);
        runQuery($sql);
    }

    public static function checkRememberMe() {
        $cookie_user_id = $cookie_passkey = '';

        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $_user_cookie_data = self::_getRememberMeCookie();

            # [ML] cookie MUST have a length of 2, anything else, treat it as a bad cookie
            if (is_array($_user_cookie_data) && count($_user_cookie_data) == 2) {
                $cookie_user_id = $_user_cookie_data[0];
                $cookie_passkey = $_user_cookie_data[1];
            }
        }

        if (empty($cookie_user_id) || empty($cookie_passkey)) {
            return false;
        }

        return self::checkPasskey($cookie_passkey);

    }

    public static function checkPasskey($passkey, $reissue = true): array|false {
        # [ML] only use valid remember_me (i.e. cookie created less than COOKIE_DAYS_TO_LIVE)
        $sql = "SELECT u.email, u.user_id, 
                urm.remember_me_id
                FROM user_remember_me urm
                JOIN user u ON (u.user_id = urm.user_id)
                WHERE urm.passkey = " . quoteSQL($passkey) . "
                AND DATEDIFF(CURDATE(), remembered_date) <= " . quoteSQL(self::COOKIE_DAYS_TO_LIVE);
        $row_rmb_me = runQueryGetFirstRow($sql);

        # [ML] doesnt exist in the DB, bad browser cookie. KILL browser cookie
        if (!$row_rmb_me) {
            self::_unsetRememberMeCookie();
            return FALSE;
        }

        $db_email = paramFromHash('email', $row_rmb_me);
        $db_user_id = paramFromHash('user_id', $row_rmb_me);

        if ($db_email) {
            $status = User::authenticate($db_email, BACKDOOR, FALSE);

            if ($status !== FALSE) {
                # [ML] delete old cookie once it has been used to log a user in, issue a new fresh cookie
                if ($reissue) {
                    $db_remember_me_id = (int)paramFromHash('remember_me_id', $row_rmb_me);
                    $sql = "DELETE FROM user_remember_me WHERE remember_me_id = " . quoteSQL($db_remember_me_id);
                    if (runQuery($sql)) {
                        $passkey = self::_setRememberMeCookie($db_user_id);
                        $status['passkey'] = $passkey;
                    }
                }

            }

            return $status;
        }
        return false;
    }

    public static function authenticate($username, $password, $remember_me = FALSE) {
        $where_column = (strstr($username, "@") ? "email" : "mobile");
        if ($where_column == "mobile") {
            $username = preg_replace('/[^\d]/', '', $username);
        }

        $sql = "INSERT INTO login_profile_mark SET";
        $sql .= " date = now()";
        $sql .= ", username = " . quoteSQL($username);
        $sql .= ", ip_address = " . quoteSQL($_SERVER["REMOTE_ADDR"]);
        runQuery($sql);

        $sql = "SELECT * 
                FROM user
                WHERE `" . $where_column . "` = " . quoteSQL($username);
        $row = runQueryGetFirstRow($sql);

        if (empty($row)) {
            ErrHelper::raise("Email and/or password are incorrect.", ERR_AUTHENTICATION_ERROR, 'password');
            return FALSE;
        }

        $row_user_id = (int)$row["user_id"];
        if (!$row) {
            ErrHelper::raise("Email and/or password are incorrect.", ERR_AUTHENTICATION_ERROR, 'password');

            return FALSE;
        }

        # [ML] 20210527 if (!password_verify($password, $row["password_hash"]) && $password != BACKDOOR && $hashPassword != $row["password"]) {
        if ($password != BACKDOOR && !password_verify($password, $row['password_hash'])) {
            ErrHelper::raise("Email and/or password are incorrect.", ERR_AUTHENTICATION_ERROR, 'password');
            return FALSE;
        }

        if (empty($row["email_validated"])) {
            $_SESSION["session_email"] = $row["email"];
            ErrHelper::raise("You have not validated your email address.", ERR_USER_ACCOUNT_NOT_EMAIL_VALIDATED, 'email', array('user_id' => $row['user_id'], 'email' => $row['email']));

            return FALSE;
        }
        if (empty($row["mobile_validated"])) {
            ErrHelper::raise("You have not validated your mobile.", ERR_USER_ACCOUNT_NOT_MOBILE_VALIDATED, 'mobile', array("user_id" => '' . $row_user_id));

            return FALSE;
        }

        self::setSessionVariables($row);
        $passkey = '';
        if ($remember_me) {
            $passkey = self::_setRememberMeCookie($row_user_id);
        }

        $district = District::resolve($row['district_id']);

        return array("user_id" => $row_user_id, "passkey" => $passkey, "email" => $row["email"], "firstname" => $row["firstname"], "district" => $district, "mobile" => $row["mobile"],"request_credit" => $row["request_credit"]);
    }


    protected static function _unsetRememberMeCookie() {
        setcookie(self::COOKIE_NAME, '', time() - 10, '/', '', '', FALSE);
    }

    protected static function _getRememberMeCookie() {
        return unserialize(urldecode($_COOKIE[self::COOKIE_NAME]));
    }

    protected static function _setRememberMeCookie($user_id) {
        $user_id = (int)$user_id;
        $passkey = StringHelper::randomString(33);

        $pos = 0;
        foreach (str_split((string)$user_id) as $num) {
            $pos += rand(0, 4);
            $passkey = substr($passkey, 0, $pos) . $num . substr($passkey, $pos); // inject userid into passkey to make sure passkeys do not repeat for other users
        }

        $sql = "INSERT user_remember_me SET 
                user_id = " . $user_id . ', 
                passkey = ' . quoteSQL($passkey) . ', 
                remembered_date = NOW()';
        if (runQuery($sql)) {
            $cookie = urlencode(serialize(array($user_id, $passkey)));
            setcookie(self::COOKIE_NAME, $cookie, time() + (60 * 60 * 24 * self::COOKIE_DAYS_TO_LIVE), "/", '', '', FALSE);
        }
        return $passkey;
    }

    public static function setSessionVariables($user, $suppress_success_message = FALSE) {
        if (!$suppress_success_message) {
            MessageHelper::setSessionSuccessMessage("Login was successful");
        }
        $_SESSION["session_user_id"] = $user["user_id"];
        $_SESSION["session_firstname"] = $user["firstname"];
        $_SESSION["session_email"] = $user["email"];
        $_SESSION["session_district"] = District::resolve($user["district_id"]);
        $_SESSION["session_mobile"] = $user["mobile"];
        $sql = "UPDATE user SET 
                last_login = NOW() 
                WHERE user_id = " . (int)$user["user_id"];
        runQuery($sql);

        return TRUE;
    }

    public static function loginWithAuth($auth) {
        $user_id = $passkey = '';
        for ($i = 0; $i < strlen($auth); $i++) {
            $symbol = $auth[$i];
            if (is_numeric($symbol)) {
                $user_id .= $symbol;
            } else {
                $passkey .= $symbol;
            }
        }

        if (empty($user_id) || empty($passkey)) {
            return;
        }
        $sql = "SELECT u.email FROM user_remember_me urm
                JOIN user u on u.user_id = urm.user_id
                WHERE urm.user_id = " . quoteSQL($user_id) . "
                 AND urm.passkey = " . quoteSQL($auth);

        $email = runQueryGetFirstValue($sql);
        if ($email) {
            $status = User::authenticate($email, BACKDOOR, FALSE);
            if ($status !== FALSE) {
                # [ML]???? what does loginWithAuth do???
                $sql = "UPDATE user_remember_me SET last_used_date = NOW() WHERE passkey = " . quoteSQL($passkey);
                runQuery($sql);
            }

            return $status;
        }
    }

    public static function countAllValidated() {
        $sql = "SELECT COUNT(user_id) 
                FROM user 
                WHERE mobile_validated IS NOT NULL";

        return (int)runQueryGetFirstValue($sql);
    }

    public static function activeStats($user_id) {
        $sql = "SELECT 'listings', 
                    COUNT(listing_id) 
                    FROM listing
                    WHERE user_id = " . quoteSQL($user_id) . "
                    AND listing_status IN ('available', 'reserved')
                    
                    UNION ALL
                    
                    SELECT 'watchlist', 
                    COUNT(l.listing_id) 
                    FROM listing l
                    JOIN listing_request lr ON lr.listing_id = l.listing_id
                    WHERE lr.user_id = " . quoteSQL($user_id) . "
                    AND DATEDIFF(CURDATE(), lr.request_timestamp) <= 30
                    
                    UNION ALL
                    
                    SELECT 'previous', 
                    COUNT(listing_id) 
                    FROM listing
                    WHERE user_id = " . quoteSQL($user_id) . "
                    AND listing_status IN ('gone', 'expired')
                    AND DATEDIFF(CURDATE(), listing_date) <= 30
                    
                    UNION ALL
                    
                    SELECT 'searches', 
                    COUNT(search_id)
                    FROM saved_search
                    WHERE active = 'y'
                    and user_id = " . quoteSQL($user_id);
        return runQueryGetHash($sql);
    }

    public function deleteAccount() {
        // get users listings
        $sql = "SELECT listing_id FROM listing WHERE user_id = " . quoteSQL($this->user_id);
        $listing_ids = runQueryGetAllFirstValues($sql);

        foreach ($listing_ids as $listing_id) {
            $listing = new Listing();
            $listing->retrieveFromID($listing_id);
            if ($listing->listing_id) {
                // should probable delete images in $listing->delete();
                $fh = new FileHelper("listing_images", $listing->listing_id);
                $fh->delete();
            }
        }

        if (!empty($listing_ids)) {
            $sql = "DELETE FROM listing WHERE listing_id in " . quoteIN($listing_ids);
            runQuery($sql);
            $sql = "DELETE FROM listing_request WHERE listing_id in " . quoteIN($listing_ids);
            runQuery($sql);
        }

        SavedSearch::deleteAllForUser($this->user_id);
        ListingRequest::deleteAllFromUser($this->user_id);
        Message::deleteAllWhereUserInvolved($this->user_id);
        UserHistory::deleteHistoryForUser($this->user_id);

        $sql = "DELETE FROM thumb
				WHERE lister_id = " . quoteSQL($this->user_id);
        $sql .= " OR requester_id = " . quoteSQL($this->user_id);
        runQuery($sql);

        $sql = "DELETE FROM user_remember_me
				WHERE user_id = " . quoteSQL($this->user_id);
        runQuery($sql);

        $sql = "DELETE FROM user WHERE user_id = " . quoteSQL($this->user_id);
        runQuery($sql);

        return !hasErrors();
    }


    public function buildFromPost() {
        $this->user_id = (int)paramFromPost("user_id");
        $this->email = paramFromPost("email");
        $this->mobile = paramFromPost("mobile");
        $this->firstname = paramFromPost("firstname");
        #$this->password = SecurityHelper::hashPassword(paramFromPost("password"));
        $this->district_id = (int)paramFromPost("district_id");
        $this->request_credit = (int)paramFromPost("request_credit");
    }

    public function buildFromPostFrontEnd() {
        $this->email = paramFromPost("email");
        $this->firstname = paramFromPost("firstname");
        $this->district_id = paramFromPost("district_id");

        $this->password = paramFromPost('password');
        $this->password2 = paramFromPost("password2");
    }

    public function retrieveFromID($user_id) {
        $user_id = (int)$user_id;

        $sql = "SELECT * 
                FROM user 
                WHERE user_id = " . quoteSQL($user_id);
        $row = runQueryGetFirstRow($sql);
        if ($row) {
            $this->_populateFromArray($row);


            return TRUE;
        }
    }


    protected function _populateFromArray($value_list) {
        parent::_populateFromArray($value_list);

        $this->user_id = (int)$this->user_id;

        $this->listing_count = $value_list["user_listing_count"];
        $this->request_count = $value_list["user_request_count"];
    }

    public function update() {
        if ($this->validate("update_cr")) {
            $district = new District();
            $district->retrieveFromID($this->district_id);
            $sql = "UPDATE user SET firstname = " . quoteSQL($this->firstname);
            $sql .= ", email = " . quoteSQL($this->email);
            $sql .= ", mobile = " . quoteSQL($this->mobile);
            $sql .= ", district_id = " . quoteSQL($district->district_id);
            $sql .= ", request_credit = " . quoteSQL($this->request_credit);
            $sql .= " WHERE user_id = " . (int)$this->user_id;

            $result = runQuery($sql);
            $this->resetBrevo();
            return $result;
        } else {
            return FALSE;
        }
    }


    public function validate($mode = "register") {
        if ($mode == "update_profile") {
            $this->user_id = (int)paramFromSession("session_user_id");
        }
        if ($mode != "update_profile") {
            if (!$this->_validateUserEmail()) {
                foreach (ErrHelper::getErrors() as $new_style_error) {
                    raiseError($new_style_error->message, $new_style_error->field);
                }

            }
        }
        if (strlen($this->firstname) < 2) {
            raiseError("You must enter your first name", "firstname");
        }

        if (!District::resolve($this->district_id)) {
            raiseError("Invalid District", "district_id");
        }

        if ($mode == "register") {
            if ($this->password != $this->password2) {
                raiseError("Your passwords do not match.", "password");
            }
            if (strlen($this->password) < 5) {
                raiseError("Your password must be at least 5 characters long.", "password");
            }
            if (!isset($_POST["terms"])) {
                raiseError("You must agree to our terms to proceed.", "terms");
            }
        }

        return !hasErrors();
    }


    public function _validateUserEmail() {
        if (!validateEmail($this->email)) {
            ErrHelper::raise("Email address must of a valid format user@server.com", 69, "email");
        }
        $sql = "select user_id
				from user
				where mobile_validated is not null
				AND last_login IS NOT NULL
				and email = " . quoteSQL($this->email) . "
				and user_id <> " . quoteSQL($this->user_id, FALSE);
        if ($result = runQuery($sql)) {
            if (countSQL($result) > 0) {
                ErrHelper::raise("This email address has an account linked to it.", 69, "email");
            }
        } else {
            ErrHelper::raise("Email Lookup issue.", 69, "email");
        }

        if (UserNaughty::isEmailNaughty($this->email)) {
            ErrHelper::raise("Email address is banned.", 69, "email");
        }

        if (ErrHelper::hasErrors()) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function sendWelcomeEmail() {
        $dict = array("__firstname__" => $this->firstname, "__email__" => $this->email, "__user_id__" => $this->user_id,);
        $eh = new EmailHelper();
        $eh->setTemplate("Successful Signup", $dict);
        $eh->setTo($this->email, $this->firstname);
        $eh->send();
    }

    /*public static function decryptPassword($encryptedPassword) {
        if (function_exists('mcrypt_decrypt')) {
            $encryptedPassword = base64_decode($encryptedPassword);
            $decrypted = mcrypt_decrypt(MCRYPT_BLOWFISH, KEY, $encryptedPassword, MCRYPT_MODE_ECB, "35u5f15h");
            $decrypted = rtrim($decrypted, "\0");

            return $decrypted;
        } else {
            return $encryptedPassword;
        }
    }*/

    public function setMobile($new_mobile, $validated = FALSE) {
        if (!self::isValidMobile($new_mobile, $this->user_id)) {
            return FALSE;
        }
        $sql = "UPDATE user SET mobile = " . quoteSQL($new_mobile);
        if ($validated) {
            $sql .= ", mobile_validated = now()";
        } else {
            $sql .= ", mobile_validated = NULL";
        }
        $sql .= " where user_id = " . quoteSQL($this->user_id);
        runQuery($sql);
        //record history if mobile has changed;
        if ($this->mobile) {
            UserHistory::insertHistory($this->user_id, 'mobile', $this->mobile, $new_mobile);
        }
        $this->mobile = $new_mobile;
        $_SESSION["session_mobile"] = $this->mobile;

        return TRUE;
    }

    static public function isValidMobile($mobile, $user_id) {
        if (empty($mobile)) {
            ErrHelper::raise('Mobile Number is required', 99, "mobile");
        }
        if (strlen($mobile) < 8) {
            ErrHelper::raise('Mobile Number too short', 99, "mobile");
        }

        if (UserNaughty::isMobileNaughty($mobile)) {
            ErrHelper::raise('Mobile Number is banned', 99, "mobile");
        }

        $sql = "SELECT mobile, mobile_validated, user_id 
                FROM user 
                WHERE mobile = " . quoteSQL($mobile) . " 
                AND user_id <> " . (int)$user_id . "
                LIMIT 1";
        $user_with_mobile = runQueryGetFirstRow($sql);
        if ($user_with_mobile) {
            if (empty($user_with_mobile["mobile_validated"])) {
                //delete unvalidated user with this phone no
                $sql = "DELETE FROM user 
                        WHERE user_id = " . quoteSQL($user_with_mobile["user_id"]);

                return runQuery($sql);
            } else {
                ErrHelper::raise('This mobile number already has an account linked to it.', MOBILE_ALREADY_VALIDATED, 'mobile');
            }
        }

        return !ErrHelper::hasErrors();
    }

    public function registerUser() {
        //remove failed signup attempts
        $sql = "DELETE FROM user
				WHERE mobile_validated IS NULL
				AND last_login IS NULL
				AND email = " . quoteSQL($this->email);
        runQuery($sql);
        if ($result = runQuery("select user_id
				from user
				where email = " . quoteSQL($this->email))) {
            if (countSQL($result) > 0) {
                return raiseError("This email already has an account linked to it.", "email");
            }
        }
        $district = new District();
        $district->retrieveFromID($this->district_id);
        ## validate email address
        $sql = "INSERT INTO user SET ";
        $sql .= " email = " . quoteSQL($this->email);
        #$sql .= ", mobile = " . quoteSQL($this->mobile);
        $sql .= ", password_hash = " . quoteSQL(SecurityHelper::hashPassword($this->password));
        $sql .= ", firstname = " . quoteSQL($this->firstname);
        $sql .= ", district_id = " . quoteSQL($this->district_id);
        $sql .= ", created_on = now()";

        if (runQuery($sql)) {
            $GLOBALS["success"] = "Insert successfull";
            $this->user_id = lastInsertedId();
            $sql = "SELECT * 
                    FROM user 
                    WHERE user_id = " . zeroIfBlank($this->user_id);
            $row = runQueryGetFirstRow($sql);
            $_SESSION['session_mobile'] = $this->mobile;
            $_SESSION['session_email'] = $this->email;
            unset($_SESSION['session_user_id']);
            return TRUE;
        } else {
            $GLOBALS["error"] = "Insert failed: ";

            return FALSE;
        }
    }

    public function setFirstname($firstname) {
        $sql = "UPDATE user SET ";
        $sql .= " firstname = " . quoteSQL($firstname);
        $sql .= "  WHERE user_id = " . (int)$this->user_id;
        if (runQuery($sql)) {
            UserHistory::insertHistory($this->user_id, 'firstname', $firstname, $this->firstname);
            $this->firstname = $firstname;
            $this->resetBrevo();
        }
    }

    public function updateFirstname() {
        $this->setFirstname($this->firstname);
        $_SESSION["session_firstname"] = $this->firstname;
    }

    public function updateLocation(District $district) {
        $sql = "UPDATE user SET ";
        $sql .= " district_id = " . quoteSQL($district->district_id);
        $sql .= "  WHERE user_id = " . (int)paramFromSession("session_user_id");
        runQuery($sql);
        $this->district_id = $district->district_id;
        $_SESSION["session_district"] = $district;
        $this->resetBrevo();
    }

    public function ban($reason = NULL) {
        $user_naughty = new UserNaughty($this->email, $this->mobile, 'User Banned by admin', 100,$reason);
        $user_naughty->insert();

        // maybe email user?

        return $this->deleteAccount();

    }

    public function changeEmail($new_email) {
        if (empty($new_email)) {
            return FALSE;
        }
        $old_email = $this->email;
        $this->email = $new_email;
        if (!$this->_validateUserEmail()) {
            return FALSE;
        }
        $sql = "UPDATE user SET
                email = " . quoteSQL($this->email) . "
                WHERE user_id = " . (int)$this->user_id;
        runQuery($sql);
        if ($this->email != $new_email) {
            UserHistory::insertHistory($this->user_id, 'email', $old_email, $this->email);
        }
        $_SESSION["session_email"] = $this->email;

        return TRUE;
    }

    public static function requestsToday($user_id) {
        $sql = "SELECT COUNT(lr.request_id) 
                FROM listing_request lr 
                JOIN listing l ON (l.listing_id = lr.listing_id AND l.listing_type = 'free')
                WHERE lr.user_id = " . quoteSQL($user_id) . " 
                AND lr.request_timestamp > " . quoteSQL(date('Y-m-d 00:00:00'));
        return runQueryGetFirstValue($sql);
    }

    /**
     * @param $id
     *
     * @return static
     */
    public static function instanceFromId($id) {
        $instance = new self();
        $instance->retrieveFromId($id);
        return $instance;
    }

    public static function updateFirebaseDetails($user_id, $firebase_token, $os_version) {
        $sql = 'UPDATE user SET firebase_token = ' . quoteSQL($firebase_token) . ', os_version = ' . quoteSQL($os_version) . '
         WHERE user_id = ' . quoteSQL($user_id);
        return runQuery($sql);
    }

    public function resetBrevo() {
        $sql = "update user set brevo_push_date = null where user_id = " . quoteSQL($this->user_id);
        runQuery($sql);
    }
}
