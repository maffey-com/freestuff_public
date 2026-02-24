<?php
ini_set('display_errors', 1);

class WebhookController extends _Controller {
    public function index() {

    }

    /** to allow user to go back from browsers */
    public function brevo() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (Brevo::isIpAllowed($ip)) {
            $data = file_get_contents('php://input');
            $object = json_decode($data);
            $sql = "update user set brevo_status = " . quoteSQL($object->event) . " where email = " . quoteSQL($object->email);
            writeLog($sql, "brevo.log");
            runQuery($sql);

            writeLog($data, "brevo.log");
        } else {
            writeLog("ip not allowed: " . $ip, "brevo.log");
        }

    }

}
