<?php
    class UserHistory extends CRModel {

        protected $user_history_id;
        public $user_id;
        public $date;
        public $field;
        public $old_value;
        public $new_value;

        public function __construct() {

        }

        /**
         * Add a new event into the user history table.
         *
         * @param int    $user_id
         * @param string $field
         * @param mixed  $old_value
         * @param mixed  $new_value
         *
         * @return bool
         */
        public static function insertHistory($user_id = 0, $field = '', $old_value = '', $new_value = '') {
            if ((int)$user_id == 0) {
                return false;
            }
            $sql = "INSERT INTO user_history SET ";
            $sql .= "user_id = ".quoteSQL($user_id);
            $sql .= ",field = ".quoteSQL($field);
            $sql .= ",old_value = ".quoteSQL($old_value);
            $sql .= ",new_value = ".quoteSQL($new_value);

            return !!runQuery($sql);
        }

        public static function deleteHistoryForUser($user_id = 0) {
            if ((int)$user_id == 0) {
                return false;
            }
            $sql = "DELETE FROM user_history WHERE user_id = ".quoteSQL($user_id);
            return runQuery($sql);
        }

    }