<?

class Message extends CRModel {
    public $message_id;
    public $conversation_key;
    public $sender_user_id;
    public $receiver_user_id;
    public $message;
    public $ip_address;
    public $date_created;
    public $date_notified;
    public $date_viewed;

    public $email_message_id;
    public $request_id;

    /**
     * class constructor
     */
    public function __construct() {
        $this->_primary_key = 'message_id';
        $this->_setHintNumeric('message_id', 'sender_user_id', 'receiver_user_id', 'request_id');
    }

    /**
     * retrieve values from $_POST and set it to the object
     */
    public function buildFromPost() {
        $this->_populateFromArray($_POST);
    }

    public function buildFromArray($input) {
        $this->_populateFromArray($input);
    }

    /**
     * retrieve database record and set it to the object
     * @param INT $message_id
     */
    public function retrieveFromID($message_id) {
        $message_id = (int)$message_id;

        $sql = "SELECT *
				FROM message 
				WHERE message_id = " . quoteSQL($message_id);
        $row = runQueryGetFirstRow($sql);
        if ($row) {
            $this->_populateFromArray($row);
        }
    }

    /**
     * Validate value
     * @param STRING $type
     */
    public function validate($type = NULL) {
        if ($type != 'insert') {
            $this->_validateRequiredField('message_id', 'Message Id');
        }

        $this->_validateIntField('message_id', 'Message Id');
        $this->_validateRequiredField('sender_user_id', 'Sender User Id');
        $this->_validateRequiredField('receiver_user_id', 'Receiver User Id');

        return !hasErrors();
    }

    /**
     * Insert record to database
     */
    public function insert() {
        if ($this->validate('insert')) {
            $ip_address = paramFromHash('REMOTE_ADDR', $_SERVER);

            $sql = "INSERT message SET ";
            $sql .= $this->_sqlSETHelper('receiver_user_id', 'sender_user_id', 'message', 'request_id', 'conversation_key');
            $sql .= ", date_created = now()";
            $sql .= ", ip_address = " . quoteSQL($ip_address);
            if (runQuery($sql)) {
                $this->message_id = lastInsertedId();
                self::updateLatestMessageInConversationKey($this->conversation_key);
                return true;
            }
        }
    }

    public static function getAllConversationsForUserId($my_user_id, FilterHelper $filter = NULL) {
        $filter = is_null($filter) ? new FilterHelper() : $filter;

        # all my conversation
        $sql_my_conversation = "SELECT MAX(m.message_id) max_message_id, COUNT(m.message_id) count_messages
                                FROM message m
                                JOIN user u_sender ON (u_sender.user_id = m.sender_user_id)
                                JOIN user u_receiver ON (u_receiver.user_id = m.receiver_user_id)
                                WHERE (m.sender_user_id = " . quoteSQL($my_user_id) . " OR m.receiver_user_id = " . quoteSQL($my_user_id) . ")";
        if ($filter->q) {
            $sql_my_conversation .= " AND (
                                            m.message LIKE " . quoteSQL('%' . $filter->q . '%') . "
                                            OR 
                                            u_sender.firstname LIKE " . quoteSQL('%' . $filter->q . '%') . "
                                            OR 
                                            u_receiver.firstname LIKE " . quoteSQL('%' . $filter->q . '%') . "
                                        )";
        }

        if($filter->listing_id){
            $sql_my_conversation .= " AND (m.conversation_key IN (SELECT DISTINCT m.conversation_key 
                                        FROM message m 
                                        JOIN listing_request r ON (r.request_id = m.request_id)
                                        WHERE r.listing_id = " . quoteSQL($filter->listing_id) . "
                                        ))";
        }

        $sql_my_conversation .= " GROUP BY conversation_key";
        $sql = "SELECT tmp_message.*,
                u_other.firstname other_firstname, u_other.district_id other_district_id,
                IF(tmp_message.sender_user_id = " . quoteSQL($my_user_id) . ", 'y', 'n') is_sender
                FROM (
                        SELECT tmp_conversation.*,
                        m.*, 
                        IF(m.sender_user_id = " . quoteSQL($my_user_id) . ", m.receiver_user_id, m.sender_user_id) other_user_id
                        FROM (" . $sql_my_conversation . ") tmp_conversation
                        JOIN message m ON (m.message_id = tmp_conversation.max_message_id)
                    ) tmp_message
                JOIN user u_other ON (u_other.user_id = tmp_message.other_user_id)";
        return $sql;
    }

    /**
     * @param Listing|null $listing - we can make this message about a specific listing, or not.
     */
    public function notify() {
        if ($this->request_id) {
            $request = new ListingRequest();
            $request->retrieveFromID($this->request_id);

            $listing = new Listing();
            $listing->retrieveFromID($request->listing_id);
        }

        $recents = ListingRequest::recentRequestsBetweenUsers($this->sender_user_id, array($this->receiver_user_id));
        if (isset($recents[$this->receiver_user_id])) {
            $titles = ArrayHelper::getColumn($recents[$this->receiver_user_id], 'title');
        } else {
            $titles = array();
        }

        $u_sender = new User();
        $u_sender->retrieveFromID($this->sender_user_id);

        $u_receiver = new User();
        $u_receiver->retrieveFromID($this->receiver_user_id);

        $reply_to_email = EmailHelper::generateConversationReplyTo($u_sender->user_id, $u_receiver->user_id);

        $subject = 'New message from ' . ucfirst($u_sender->firstname);
        if (isset($listing)) {
            $subject .= " - " . $listing->title;
        }

        $email_body = "You have received a new message from " . ucfirst($u_sender->firstname) . ':' . PHP_EOL . $this->message;

        $re = "Your recent interactions with " . ucfirst($u_sender->firstname) . " include: " . PHP_EOL . implode(PHP_EOL, $titles) . PHP_EOL . PHP_EOL;

        $email_body = str_ireplace("<br/>", PHP_EOL, $email_body);
        $email_body = strip_tags($email_body);

        $dict = array(
            "__firstname__" => ucfirst($u_receiver->firstname),
            "__message__" => $email_body,
            "__reply_url__" => SITE_URL . "message/conversation/" . $u_sender->user_id,
            "__re__" => $re
        );

        $eh = new EmailHelper();
        $eh->setTemplate("New Message", $dict);
        $eh->setTo($u_receiver->email);
        $eh->setReplyTo($reply_to_email);
        $eh->setSubject($subject);
        $eh->send();

        FirebaseHelper::sendNotification($u_receiver, 'New message from ' . $u_sender->firstname, $this->message, ['open_message' => 'true', 'other_user_id' => $u_sender->user_id, 'other_user_name' => $u_sender->firstname]);

        $sql = "UPDATE message SET date_notified = now() WHERE message_id = " . quoteSQL($this->message_id);
        runQuery($sql);
    }

    public static function buildConversationKey($user_id_1, $user_id_2) {
        $user_id_1 = (int)$user_id_1;
        $user_id_2 = (int)$user_id_2;

        return min($user_id_1, $user_id_2) . '-' . max($user_id_1, $user_id_2);
    }

    public static function checkAccessToConversationKey($conversation_key, $my_user_id) {
        if (empty($conversation_key)) {
            return FALSE;
        }

        $user_ids = explode("-", $conversation_key);

        if (count($user_ids) != 2) {
            return false;
        }

        if (!in_array($my_user_id, $user_ids)) {
            return FALSE;
        }

        $other_user_id = ($my_user_id == $user_ids[0]) ? $user_ids[1] : $user_ids[0];

        return (count(self::getAllTwoWayRequestsWithUserB($my_user_id, $other_user_id)) > 0);
    }

    public static function getAllTwoWayRequestsWithUserB($my_user_id, $user_id_b) {
        static $output = array();

        $output_key = $my_user_id . '-' . $user_id_b;

        if (!array_key_exists($output_key, $output)) {
            $sql = "SELECT 
                    IF(l.user_id = " . quoteSQL($my_user_id) . ", 'y', 'n') is_lister,
                    l.*
                    FROM listing_request r
                    JOIN listing l ON (l.listing_id = r.listing_id)
                    WHERE (r.user_id = " . quoteSQL($my_user_id) . " AND l.user_id = " . quoteSQL($user_id_b) . ")
                        OR (r.user_id = " . quoteSQL($user_id_b) . " AND l.user_id = " . quoteSQL($my_user_id) . ')
                    ORDER BY r.request_id DESC';
            $output[$output_key] = runQueryGetAll($sql);
        }

        return $output[$output_key];
    }


    public static function setLatestInThread($user_id_1, $user_id_2) {
        return self::updateLatestMessageInConversationKey(self::buildConversationKey($user_id_1, $user_id_2));
    }

    public static function updateLatestMessageInConversationKey($conversation_key) {
        $sql = "SELECT MAX(message_id) 
                FROM message
                WHERE conversation_key = " . quoteSQL($conversation_key);
        $max_message_id = runQueryGetFirstValue($sql);

        $sql = "UPDATE message SET 
                is_latest = IF(message_id = " . quoteSQL($max_message_id) . ", 'y', 'n')
                WHERE conversation_key = " . quoteSQL($conversation_key);
        return runQuery($sql);
    }


    public static function getAllFromConversationKey($conversation_key, $my_user_id, $show_unread_only = FALSE, $last_fetched_message_id = 0) {
        static $output = array();

        $output_key = $conversation_key . "-" . $my_user_id . '-' . ((int)$show_unread_only);

        if (!array_key_exists($output_key, $output)) {
            $sql = "SELECT tmp.*,
                    u_other.firstname other_firstname
                    FROM (
                        SELECT m.*,
                        IF(r.user_id = " . quoteSQL($my_user_id) . ", 'n', 'y') is_lister,
                        IF(m.sender_user_id = " . quoteSQL($my_user_id) . ", m.receiver_user_id, m.sender_user_id) other_user_id, 
                        IF(m.sender_user_id = " . quoteSQL($my_user_id) . ", 'y', 'n') is_sender, 
                        l.listing_id, l.title listing_title, 
                        IF(l.listing_type = 'free', 'y', 'n') is_free
                        FROM message m
                        LEFT JOIN listing_request r ON (r.request_id = m.request_id)
                        LEFT JOIN listing l ON (l.listing_id = r.listing_id)
                        WHERE conversation_key = " . quoteSQL($conversation_key) . "
                        AND m.message_id > " . quoteSQL($last_fetched_message_id, FALSE);
            $sql .= $show_unread_only ? ' AND (date_viewed IS NULL OR sender_user_id = ' . quoteSQL($my_user_id) . ')' : '';
            $sql .= ") tmp
                        JOIN user u_other ON (u_other.user_id = tmp.other_user_id)
                        ORDER BY date_created";
            $result = runQuery($sql);

            $tmp_output = array();
            while ($row = fetchSQL($result)) {
                $_row = $row;

                if ($row["sender_user_id"] == $my_user_id) {
                    $_row["d"] = "sent";
                } else {
                    $_row["d"] = "received";
                }

                $_row["timestamp"] = DateHelper::display($row["date_created"], true, true);
                $_row["timeAgo"] = DateHelper::ago($row["date_created"]);
                $_row["listing_seo"] = seoFriendlyURLs($row['listing_id'], "listing", FALSE, $row['listing_title']);

                $tmp_output[] = $_row;
            }

            $output[$output_key] = $tmp_output;
        }

        return $output[$output_key];
    }

    public static function updateReceiverViewedDateForConversationKey($conversation_key, $receiver_user_id) {
        $sql = "UPDATE message SET 
                date_viewed = NOW()
                WHERE date_viewed IS NULL
                AND conversation_key = " . quoteSQL($conversation_key) . "
                AND receiver_user_id = " . quoteSQL($receiver_user_id);
        runQuery($sql);
    }

    public static function getAllUnreadConversationKeys($user_id) {
        $sql = "SELECT DISTINCT m.conversation_key
		        FROM message m
		        WHERE m.date_viewed IS NULL
		        AND m.receiver_user_id = " . quoteSQL($user_id);
        return runQueryGetAllFirstValues($sql);
    }

    public static function getUnreadFromConversationKeys($conversation_keys, $user_id) {
        $sql = 'select DISTINCT conversation_key from message where conversation_key in (' . arrayToSQLIn($conversation_keys) . ')';
        $sql .= ' and receiver_user_id = ' . quoteSQL($user_id);
        $sql .= ' and date_viewed is null';
        return runQueryGetAllFirstValues($sql);
    }

    public static function send($conversation_key) {
        $user_ids = explode("-", $conversation_key);
        $other_user_id = ($user_ids[0] == SESSION_USER_ID) ? $user_ids[1] : $user_ids[0];

        $_message = new Message();
        $_message->buildFromPost();
        $_message->conversation_key = $conversation_key;
        $_message->sender_user_id = SESSION_USER_ID;
        $_message->receiver_user_id = $other_user_id;
        $_message->insert();

        $u_other = new User();
        $u_other->retrieveFromID($other_user_id);

        $u_me = new User();
        $u_me->retrieveFromID(SESSION_USER_ID);

        $sql = "SELECT COUNT(message_id) count_notified_unread_messages
                    FROM message
                    WHERE conversation_key = " . quoteSQL($conversation_key) . "
                    AND email_message_id IS NOT NULL
                    AND date_notified IS NOT NULL
                    AND receiver_user_id = " . quoteSQL($_message->receiver_user_id) . "
                    AND date_notified > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                    AND date_viewed IS NULL";
        $count_notified_unread_message = (int)runQueryGetFirstValue($sql);

        $send_email = FALSE;
        $dict = array();
        if (empty($count_notified_unread_message)) {
            $subject = 'Message from ' . ucfirst($u_me->firstname);
            $subject .= empty($u_me->district_id) ? ' of ' . District::displayRegion($u_me->district_id) : " of " . District::displayShort($u_me->district_id);

            $reply_to_email = EmailHelper::generateConversationReplyTo($u_me->user_id, $u_other->user_id);

            $send_email = true;

            $email_body = "You have received a new message" .
                PHP_EOL . PHP_EOL .
                ucfirst($u_me->firstname) . " says:" .
                PHP_EOL .
                $_message->message .
                PHP_EOL . PHP_EOL;
            $email_body = str_ireplace("<br/>", PHP_EOL, $email_body);
            $email_body = str_ireplace("<br />", PHP_EOL, $email_body);
            $email_body = str_ireplace("<br>", PHP_EOL, $email_body);
            $email_body = strip_tags($email_body);

            $dict = array(
                "__firstname__" => ucfirst($u_other->firstname),
                "__message__" => $email_body,
                "__action_block__" => "Click here to reply:" . PHP_EOL . SITE_URL . 'message/conversation/' . $u_me->user_id
            );
        }

        if ($send_email && count($dict)) {
            $eh = new EmailHelper();
            $eh->setTemplate("Request New Message", $dict);
            $eh->setTo($u_other->email);
            $eh->setReplyTo($reply_to_email);
            $eh->setSubject($subject);
            $message_id = $eh->send(true);

            $sql = "UPDATE message SET date_notified = NOW(), email_message_id = " . quoteSQL($message_id) . " WHERE message_id = " . quoteSQL($_message->message_id);
            runQuery($sql);

        } else {
            $sql = "UPDATE message SET date_viewed = NOW() WHERE message_id = " . quoteSQL($_message->message_id);
            runQuery($sql);
        }

        FirebaseHelper::sendNotification($u_other, 'New message from ' . $u_me->firstname, $_message->message, ['open_message' => 'true', 'other_user_id' => $u_me->user_id, 'other_user_name' => $u_me->firstname]);
    }


    /**
     * Delete all messages where user is the sender or receiver
     *
     * @param $user_id
     * @return bool|mysqli_result
     */
    public static function deleteAllWhereUserInvolved($user_id) {
        $sql = "DELETE FROM message 
				WHERE sender_user_id = " . quoteSQL($user_id);
        $sql .= " OR receiver_user_id = " . quoteSQL($user_id);
        return runQuery($sql);
    }
}
