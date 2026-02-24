<?php

class Thumb {

    public static function myAvailableCredit() {

    }

    public static function updateThumb($user_id,$requester_user_id,$up_down) {

        $sql = "SELECT thumb_id 
                FROM thumb
                WHERE lister_id = " . quoteSQL($user_id) . "
                AND requester_id = " . quoteSQL($requester_user_id);
        $thumb_id = (int)runQueryGetFirstValue($sql);

        if ($thumb_id) {
            $sql = "UPDATE thumb SET";
            $sql .= " up_down = " . quoteSQL($up_down);
            $sql .= ", thumb_date = NOW()";
            $sql .= ", thumb_ip = " . quoteSQL($_SERVER["REMOTE_ADDR"]);
            $sql .= " WHERE thumb_id = " . quoteSQL($thumb_id);
            runQuery($sql);

        } else {
            $sql = "INSERT thumb SET";
            $sql .= " up_down = " . quoteSQL($up_down);
            $sql .= ", thumb_date = NOW()";
            $sql .= ", thumb_ip = " . quoteSQL($_SERVER["REMOTE_ADDR"]);
            $sql .= ", requester_id = " . quoteSQL($requester_user_id);
            $sql .= ", lister_id = " . quoteSQL($user_id);
            runQuery($sql);

        }

        self::totalUserThumbs($requester_user_id);

    }

    public static function totalUserThumbs($user_id) {
        $sql = "SELECT up_down, COUNT(thumb_id)
                FROM thumb
                WHERE requester_id = " . quoteSQL($user_id);
        $sql .= " GROUP BY up_down";
        $hash = runQueryGetHash($sql);

        $sql = "UPDATE user SET";
        $sql .= " thumbs_up = " . paramFromHash('u', $hash, 0);
        $sql .= " , thumbs_down = " . paramFromHash('d', $hash, 0);
        $sql .= " WHERE user_id = " . quoteSQL($user_id);
        runQuery($sql);
    }

    /**
     * @return void
     * bulk process all thumbs that have not been credited yet
     */
    public static function applyRequestCredit() {
        $sql = "select requester_id,up_down,thumb_id
from thumb 
where thumb_date > DATE_SUB(now(),INTERVAL 2 day)
and credited_date is null";
        $thumbs = runQueryGetAll($sql);

        foreach ($thumbs as $thumb) {
            $requester_user_id = $thumb['requester_id'];
            $up_down = $thumb['up_down'];
            $thumb_id = $thumb['thumb_id'];

            $sql = "update user set request_credit = greatest(0,request_credit ".($up_down == 'd' ? '-2' : '+2').") where user_id = " . quoteSQL($requester_user_id);
            //writeLog($sql);
            runQuery($sql);

            $sql = "update thumb set credited_date = now() where thumb_id = " . quoteSQL($thumb_id);
            runQuery($sql);

            $user = new User();
            $user->retrieveFromID($requester_user_id);
            if ($up_down == 'd') {
                $user_naughty = new UserNaughty($user->email, $user->mobile, 'Thumb Down', 5, 'Thumb Down');
                $user_naughty->insert();
            }
            //send an email template to the user
        }
    }

    public static function myRequestCredit($user_id) {
        return runQueryGetFirstValue("select request_credit from user where user_id = " . quoteSQL($user_id));
    }

    /**
     * @return void
     * bulk process give request credits for all users once a month
     */
    public static function refreshCredit() {
        $sql = "update `user` 
set request_credit = GREATEST(".MAX_REQUESTS_PER_MONTH.",request_credit),
request_credit_refresh_date =CURRENT_DATE() 
where (request_credit_refresh_date < DATE_SUB(now(),INTERVAL 28 DAY) or request_credit_refresh_date is null)";
        runQuery($sql);
    }


    public static function spendCredit($user_id) {
        $sql = "update user set request_credit = greatest(request_credit - 1,0) where user_id = " . quoteSQL($user_id);
        runQuery($sql);
    }

    public static function getThumbsGiven($requester_ids) {
        if (!sizeof($requester_ids)) {
            return array();
        }
        $sql = "select requester_id,up_down from thumb";
        $sql .= " where requester_id in (" . arrayToSQLIn($requester_ids) . ")";
        $sql .= " and lister_id = " . quoteSQL(SESSION_USER_ID);

        return runQueryGetHash($sql);
    }


    public static function refreshDueInDays($user_id) {
        return runQueryGetFirstValue("select DATEDIFF(date_add(request_credit_refresh_date,interval 28 day),now()) from user where user_id =  " . quoteSQL($user_id));
    }

    public static function refreshDueStatement(User $user) {
        if ($user->request_credit >= MAX_REQUESTS_PER_MONTH) {
            return "";
        }
        $days = self::refreshDueInDays($user->user_id);
        $statement = "You will get topped up to  " . MAX_REQUESTS_PER_MONTH . " request credits ";
        if ($days > 0) {
            return "$statement in " . StringHelper::singularOfPlural($days, "day", "days");
        } else {
            return "$statement tonight";
        }
    }

}