<?

class SmsPi{

    /**
     * Check if a number matches with the Zew Zealand mobile number pattern
     *
     * Matches 9 - 12 digit mobile numbers begining with the following prefixes.
     *      020
     *      021
     *      022
     *      026
     *      027
     *      028
     *      029
     *
     * @param $number
     *
     * @return bool
     */
    static function isValidMobileNumber($number = '') {
        $pattern = "/^((?:0?)(?:20|21|22|26|27|28|29)\d{6,9})$/";
        $matches = array();
        preg_match_all($pattern,$number??'',$matches);

        if (!isset($matches[0][0])) {
            return false;
        }

        return $matches[0][0] == $number;
    }

    static function cleanMobileNumber($number) {
        if (substr($number,0,4) == '+640') {
            $number = substr($number,4);
        }
        if (substr($number,0,3) == '+64') {
            $number = '0'.substr($number,3);
        }
        return preg_replace('/[^\d]/', '', $number);
    }

    static function sendMessage($phone_no,$message) {
     /*   if (SMS_PHONE_TEST) {
            $phone_no = SMS_PHONE_TEST;
        }

        $phone_no = '+64' . substr($phone_no,1);
        $url =  SMSPI_URL .
            '?username='.rawurlencode(SMSPI_USERNAME).
            '&password='.rawurlencode(SMSPI_PASSWORD).
            '&number='.rawurlencode($phone_no).
            '&message='.rawurlencode($message);
        $json = file_get_contents($url);
     $result = (array)json_decode($json);
     */

        writeLog("SMS message to $phone_no: $message");

        $result =  array();
        $result['message_id'] = '123';
        $result['error'] = 'SMS sending is disabled in this environment';


        //log message
        $sql = " INSERT sms_outgoing SET ";
        $sql .= " date = now()";
        $sql .= " ,ip_address = " . quoteSQL($_SERVER["REMOTE_ADDR"]);
        $sql .= " ,phone_no = " . quoteSQL($phone_no);
        $sql .= " ,sms_global_msg_id = " . quoteSQL($result["message_id"]);
        $sql .= " ,sms_global_error = " . quoteSQL(paramFromHash("error",$result));
        runQuery($sql);

        if ($result["result"] != 'success') {
            ErrHelper::raise('SMS send error: ' . $result['error'],99);
        }

        return $result;
    }
}
