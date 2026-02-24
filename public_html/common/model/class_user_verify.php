<?php

/*
 * has a unique 8 char id mainly for use in emails
 * has 6 numerics code for use by SMS
 *
 * stores potential new value in data
 *
 */
if (isset($_SERVER['DEV'])) {
    define('LANDLINE_PHONE_TEST','6494482192');
} else {
    define('LANDLINE_PHONE_TEST',false);
}
class UserVerify extends CRModel {

    var $verify_id;
    var $six_digit_code;
    var $verify_type;
    var $user_id;
    var $date_created;
    var $date_checked;
    var $date_expired;
    var $data;

    public static $verify_types = array(
        "change_email" => "account/email_update_verified",
        "change_mobile" => "account/mobile_update_verified",


        "register_email" => "register/email_verified",
        "register_landline" => "register/phone_verified",
        "register_mobile" => "register/phone_verified",


        "forgotten_password" => "login/forgotten_password_reset_form"

    );

    public function floodControl() {
        if (!FloodControlHelper::allow('uv'.$_SERVER["REMOTE_ADDR"],100)) {
            ErrHelper::raise('Flood Control Engaged',203);
            writeLog('Flood Control Engaged');
            return true;
        }
        if (!FloodControlHelper::allow('uv'.$this->user_id,30)) {
            ErrHelper::raise('Flood Control Engaged',203);
            writeLog('Flood Control Engaged');
            return true;
        }

    }

    function __construct() {

    }

    static function create($type, $user_id, $data = false) {
        if (!isset(self::$verify_types[$type])) {
            ErrHelper::raise('No such verify type', 99);
        }
        $v = new UserVerify();
        $v->verify_type = $type;
        $v->data = $data;
        $v->user_id = $user_id;
        $v->verify_id = $v->generateUniqueID();
        $v->six_digit_code = StringHelper::randomNumeric(6);
        if ($v->insert()) {
            return $v;
        }
    }

    static function verifyForUseridType($user_id, $verify_type, $code) {
        $sql = "select * from user_verify ";
        $sql .= " where user_id = " . quoteSQL($user_id);
        $sql .= " and verify_type = " . quoteSQL($verify_type);
        $result = runQueryGetFirstRow($sql);
        if (!$result) {
            return false;
        }
    }

    function insert() {
        // Purge old verifies
        $this->clean();

        $sql = "SELECT six_digit_code FROM user_verify WHERE data=".quoteSQL($this->data)." AND verify_type = ".quoteSQL($this->verify_type) ." LIMIT 1";
        $old_code = runQueryGetFirstValue($sql);
        $this->six_digit_code = $old_code?$old_code:$this->six_digit_code;

        $sql = "INSERT user_verify SET " .
            $this->_sqlSetHelper("verify_id", "six_digit_code", "verify_type", "user_id", "data");
        $sql .= ",date_created = now()";

        return runQuery($sql);
    }


    function retrieveFromId($verify_id) {
        $verify_id = StringHelper::removeNonAlpha($verify_id);
        $sql = "select * from user_verify where verify_id = " . quoteSQL($verify_id);
        $row = runQueryGetFirstRow($sql);
        if ($row) {
            $this->_populateFromArray($row);
        } else {
            ErrHelper::raise('Invalid Verification', 99);
        }
    }

    function retrieveByUserIdAndType($user_id, $type) {

    }

    function generateUniqueID() {
        $unique = false;

        while (!$unique) {
            $id = StringHelper::randomString(8);
            $sql = 'select verify_id from user_verify where verify_id = ' . quoteSQL($id);
            $unique = !runQueryGetFirstValue($sql);
        }
        return $id;
    }



    function checkCode($code) {
        if ($this->six_digit_code == $code && !$this->date_expired) {
            if (!$this->date_checked) {
                $sql = "update user_verify set date_checked = now() where verify_id = " . quoteSQL($this->verify_id);
                runQuery($sql);
            }
            return true;
        } else {
            if ($this->date_expired) {
                ErrHelper::raise('Verification Expired', 99);
            } else {
                ErrHelper::raise('Invalid Code', 99);
            }

        }
        return false;
    }

    function checkAndExpire($code) {
        $check = $this->checkCode($code);
        if ($check) {
            $this->expire();
        }
        return $check;
    }

    function expire() {
        $sql = "update user_verify set date_expired = now() where verify_id = " . quoteSQL($this->verify_id);
        runQuery($sql);
    }

    public function chechHasExpired() {
        return $this->date_expired;
    }

    public function sendEmail() {
        if ($this->floodControl()) {
            return false;
        }
        if ($this->date_expired) {
            ErrHelper::raise("Verification expired",99);
            return false;
        }
        $user = User::instanceFromId($this->user_id);

        $link = SITE_URL . 'r/v/' . $this->verify_id . "/" . $this->six_digit_code;

        $dict = array(
            "__firstname__" => $user->firstname,
            "__code__" => $this->six_digit_code,
            "__link__" => $link,
        );

        $eh = new EmailHelper();
        $eh->setTemplate("User Verify", $dict);
        $eh->setReplyTo('no-reply@mailer.freestuff.co.nz');
        $eh->setFrom('no-reply@mailer.freestuff.co.nz',"Freestuff NZ");
        $eh->setTo($this->data);
        $eh->send();
    }

    public function sendSMS() {
        if ($this->floodControl()) {
            return false;
        }
        if ($this->date_expired) {
            ErrHelper::raise("Verification expired",99);
            return false;
        }
        SmsPi::sendMessage($this->data, $this->six_digit_code . " is your freestuff code");
    }

    static function clean() {
        $sql = "delete from user_verify where date_created < (NOW() - INTERVAL 7 DAY)";
        runQuery($sql);
    }

    public function sendLandline() {
        if ($this->floodControl()) {
            return false;
        }
        $phone_no = "64" . ltrim($this->data, '0');
        if (LANDLINE_PHONE_TEST) {
            $phone_no = LANDLINE_PHONE_TEST;
        }

        $onverify_url = 'http://www.onverify.com/call.php?userid=&apipass=&template_id=4718&number=' . $phone_no . '&pin=' . urlencode($this->six_digit_code);
/*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $onverify_url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
*/
        writeLog("Landline call to $phone_no: $this->six_digit_code");

    }

    static function validLandlineCallingTime() {
        $current_hour = date("G");
        if (($current_hour < 9) && ($current_hour > 21)) {
            ErrHelper::raise('Cannot dial numbers out site of 9am to 9pm', 99);
            return false;
        }
        return true;

    }

    static function fakeId() {
        return StringHelper::randomString(8);
    }
}
