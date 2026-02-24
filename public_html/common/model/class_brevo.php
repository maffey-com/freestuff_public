<?php

class Brevo {
    static $api_key = '';
    static $api_url = 'https://api.sendinblue.com/v3/';

    static $ip_whitelist_ranges = array("185.107.232.1" => "185.107.232.254", "1.179.112.1" => "1.179.127.254");

    public static function sendUserToBrevo($user_id) {
        //curl funciton
        $url = self::$api_url . 'contacts';
        $user = new User();
        $user->retrieveFromId($user_id);
        if (!$user->email) {
            return false;
        }


        $data = array(
            'email' => $user->email,
            'listIds' => array(2),
            'updateEnabled' => true,
            'attributes' => array("FIRSTNAME" => $user->firstname, "REGION" => District::displayRegion($user->district_id), "LISTING_COUNT" => $user->user_listing_count)
        );

        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key:' . self::$api_key, 'Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($result === false) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            if ($httpcode == 204) {
                //did an update instead of an insert, need to fetch the existing brevo_id
                $brevo_id = self::setBrevoIdFromEmail($user);
                echo "----- $user->email UPDATED brevo id: $brevo_id\n";
            } else {
                $result = json_decode($result);
                if (isset($result->id)) {
                    $brevo_id = $result->id;
                    echo "$user->email NEW brevo id: $brevo_id\n";
                }
            }


            if (isset($brevo_id)) {
                $sql = "update user set brevo_push_date = now(), brevo_id = " . quoteSQL($brevo_id) . " where user_id = " . quoteSQL($user_id);
                runQuery($sql);
                return $brevo_id;
            }
        }
        //
    }

    public static function updateUser($user_id) {
        //curl funciton
        $url = self::$api_url . 'contacts';
        $user = new User();
        $user->retrieveFromId($user_id);
        if (!$user->email) {
            return false;
        }

        $url .= "/" . $user->brevo_id;


        $data = array(
            'attributes' => array("EMAIL" => $user->email, "FIRSTNAME" => $user->firstname, "REGION" => District::displayRegion($user->district_id), "LISTING_COUNT" => $user->user_listing_count)
        );


        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key:' . self::$api_key, 'Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $result = json_decode($result);
            $sql = "update user set brevo_push_date = now() where user_id = " . quoteSQL($user_id);
            runQuery($sql);
        }
        //
    }


    public static function pushUsersNeedingUpdate($limit = 20) {
        $users = self::getUsersNeedingUpdate('list', $limit);

        foreach ($users as $user_id) {
            self::updateUser($user_id);
        }
    }

    public static function pushUsersNeedingInsert($limit = 20) {
        $users = self::getUsersNeedingInsert('list', $limit);

        foreach ($users as $user_id) {
            self::sendUserToBrevo($user_id);
        }
    }

    public static function getUsersNeedingUpdate($mode = 'count',$limit = 0) {
        $columns_clause = $mode == 'count' ? "count(*)" : "user_id";
        $limit_clause = $mode != 'count' && $limit ? "limit $limit" : "";
        $sql = "select $columns_clause from user where brevo_id is not null and brevo_push_date is null $limit_clause";
        return $mode == 'count' ? runQueryGetFirstValue($sql) : runQueryGetAllFirstValues($sql);
    }

    public static function getUsersNeedingInsert($mode = 'count',$limit = 0) {
        $columns_clause = $mode == 'count' ? "count(*)" : "user_id";
        $limit_clause = $mode != 'count' && $limit ? "limit $limit" : "";
        $sql = "select $columns_clause from user where brevo_id is null and last_login > '2020-01-01' and (brevo_status not in ('unsubscribe','hard_bounce','spam') or brevo_status is null) and email_bounced_date is null and email_validated is not null $limit_clause";
        return $mode == 'count' ? runQueryGetFirstValue($sql) : runQueryGetAllFirstValues($sql);
    }


    public static function setBrevoIdFromEmail(User $user) {
        //fetch the brevo user and extract the brevo id
        $url = self::$api_url . 'contacts/' . $user->email;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key:' . self::$api_key, 'Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $result = json_decode($result);
            if (isset($result->id)) {

                $sql = "update user set brevo_id = " . quoteSQL($result->id) . " where user_id = " . quoteSQL($user->user_id);
                runQuery($sql);
                return $result->id;
            }
        }
        return false;
    }


    public static function isIpAllowed($ip_address) {
        foreach (self::$ip_whitelist_ranges as $start => $end) {
            if (ip2long($ip_address) >= ip2long($start) && ip2long($ip_address) <= ip2long($end)) {
                return true;
            }
        }
        return false;
    }

    public static function tTest() {
        //curl funciton
        $url = self::$api_url . 'smtp/email';
        $data = array(
            'sender' => array("name" => "Freestuff Team", "email" => "team@freestuff.co.nz"),
            'to' => array(array("email" => "chris@maffey.com","name" => "Chris Maffey")),
            "subject" => "This is a test",
            "htmlContent" => "<html><head></head><body><p>Hello,</p>This is my first transactional email sent from Brevo.</p></body></html>"
        );


        $data_string = json_encode($data);
        print_r($data_string);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key:' . self::$api_key, 'Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            print_r($result);
        }
        //
    }

}