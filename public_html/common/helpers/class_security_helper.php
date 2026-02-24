<?
class SecurityHelper {
	#const DEFUALT_REDIRECT_URL = "login.php";

	public static function hasPermission($permission) {
		if (!isset($_SESSION["session_permissions"])) {
			return false;
		}
		if (in_array($permission, $_SESSION["session_permissions"])) {
			return true;
		}
		return false;
	}


	public static function loginViaCookie() {
	    User::checkRememberMe();
    }

	public static function isLoggedIn() {
	    $session_user_id = (int)paramFromSession('session_user_id');
		return !empty($session_user_id);
	}


    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

	public static function logout() {
		if (self::isLoggedIn()) {
			unset($_SESSION['session_user_id']);
		}

        session_unset();
        session_write_close();

		if (isset($_COOKIE['freestuff_remember_me'])) {
			list($user_id, $key) = unserialize(urldecode($_COOKIE['freestuff_remember_me']));

			$sql = "DELETE FROM user_remember_me 
                    WHERE user_id = " . quoteSQL($user_id) . " 
                    AND passkey = " .quoteSQL($key);
			runQuery($sql);
		}

        setcookie("freestuff_remember_me", '', time() - 10, "/", '','',false);

		$GLOBALS["success"] = "Successfully Logged Out";
	}

	public static function getCurrentPage() {
		# get current page url
		$self_page = $_SERVER['PHP_SELF'];

        $pos = strrpos($self_page, '/');
        return substr($self_page, $pos+1);
	}

}
