<?php

class UserBlocked  {
    public static $blocked_users;
    public static $blocked_me;

    public static function blockUser($blocker_user_id, $blocked_user_id, $hide_messages = 'n') {
        $sql = "INSERT INTO user_blocked SET
                blocker_user_id = " . quoteSQL($blocker_user_id) . ",
                blocked_user_id = " . quoteSQL($blocked_user_id) . ",
                hide_messages = " . quoteSQL($hide_messages);
        return runQuery($sql);
    }

    public static function unblockUser($blocker_user_id, $blocked_user_id) {
        $sql = "DELETE FROM user_blocked
                WHERE blocker_user_id = " . quoteSQL($blocker_user_id) . "
                AND blocked_user_id = " . quoteSQL($blocked_user_id);
        return runQuery($sql);
    }


    public static function getBlockedForUser($blocker_user_id) {
        if (isset(self::$blocked_users)) {
            return self::$blocked_users;
        }
        $sql = "SELECT blocked_user_id, hide_messages
                FROM user_blocked
                WHERE blocker_user_id = " . quoteSQL($blocker_user_id);
        $res =  runQueryGetAll($sql);
        $blocked_users = array();
        foreach ($res as $row) {
            $blocked_users[$row['blocked_user_id']] = $row['hide_messages'];
        }
        self::$blocked_users = $blocked_users;
        return $blocked_users;
    }

    public static function isUserBlocked($blocker_user_id, $blocked_user_id) {
        $blocked_users = self::getBlockedForUser($blocker_user_id);
        return isset($blocked_users[$blocked_user_id]);
    }

    public static function isUserMessagesBlocked($blocker_user_id, $blocked_user_id) {
        $blocked_users = self::getBlockedForUser($blocker_user_id);
        return isset($blocked_users[$blocked_user_id]) && $blocked_users[$blocked_user_id] == 'y';
    }

    public static function getUsersBlockedMe($current_user_id) {
        if (isset(self::$blocked_me)) {
            return self::$blocked_me;
        }
        $sql = "SELECT blocker_user_id, hide_messages
                FROM user_blocked
                WHERE blocked_user_id = " . quoteSQL($current_user_id);
        $res =  runQueryGetAll($sql);
        $blocked_users = array();
        foreach ($res as $row) {
            $blocked_users[$row['blocker_user_id']] = $row['hide_messages'];
        }
        self::$blocked_me = $blocked_users;
        return $blocked_users;
    }
    public static function hasUserBlockedMe($current_user_id, $other_user_id) {
        $blocked_users = self::getUsersBlockedMe($current_user_id);
        return isset($blocked_users[$other_user_id]);
    }

    public static function canSeeListing($current_user_id, $other_user_id) {
        return !UserBlocked::isUserBlocked($current_user_id, $other_user_id) && !UserBlocked::hasUserBlockedMe($current_user_id, $other_user_id);
    }
}