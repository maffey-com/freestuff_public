<?
class ListingRequest extends CRModel {
    public $request_id;
    public $listing_id;
    public $request_timestamp;
    public $user_id;
    public $user_firstname;
    public $user_ip_address;
    public $district_id;

    /**
     * class constructor
     */
    public function __construct() {
        #$this->request_id = "new";

        $this->_primary_key = 'request_id';
        $this->_setHintNumeric('request_id', 'listing_id', 'user_id');

    }

    /**
     * retrieve values from $_POST and set it to the object
     */
    public function buildFromPost() {
        $this->_populateFromArray($_POST);

        $this->request_id = (int)$this->request_id;
        $this->listing_id = (int)$this->listing_id;
        $this->user_id = (int)$this->user_id;

    }

    public function buildCurrentUserDetails() {
        $this->user_id = (int)paramFromSession("session_user_id");
        $this->user_firstname = paramFromSession("session_firstname");
        $this->user_ip_address = paramFromHash("REMOTE_ADDR", $_SERVER);

        $tmp_district = paramFromSession("session_district");
        if (!empty($tmp_district)) {
            $this->district_id = $tmp_district->district_id;
        }
    }

    /**
     * retrieve database record and set it to the object
     * @param INT $request_id
     */
    public function retrieveFromID($request_id) {
        $request_id = (int)$request_id;

        $sql = "SELECT request_id, listing_id, request_timestamp, user_id, user_firstname,  user_ip_address, district_id
				FROM listing_request 
				WHERE request_id = " . quoteSQL($request_id);
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
            $this->_validateRequiredField('request_id', 'Request Id');
        }
        $this->_validateIntField('request_id', 'Request Id');

        //make sure is unique
        $sql = "SELECT COUNT(*) 
                FROM listing_request
                WHERE listing_id = " . quoteSQL($this->listing_id) . "
                AND user_id = " . quoteSQL($this->user_id);
        if (runQueryGetFirstValue($sql)) {
            raiseError("Request already placed");
        }

        return !hasErrors();
    }

    /**
     * Insert record to database
     */
    public function insert() {
        if ($this->validate('insert')) {
            $sql = "INSERT listing_request SET ";
            $sql .= $this->_sqlSETHelper('listing_id', 'user_id', 'user_firstname', 'district_id', 'user_ip_address');
            $sql .= ", request_timestamp = NOW()";
            if (runQuery($sql)) {
                $this->request_id = lastInsertedId();

                return TRUE;
            }
        }
    }


    /**
     * Delete record from db
     */
    public static function delete($request_id) {
        $request_id = (int)$request_id;

        $sql = "DELETE FROM listing_request 
				WHERE request_id = " . quoteSQL($request_id);
        return runQuery($sql);
    }

    public static function deleteAllFromUser($user_id) {
        $user_id = (int)$user_id;

        $sql = "DELETE FROM listing_request 
                WHERE user_id = " . quoteSQL($user_id);
        return runQuery($sql);
    }

    public static function getRequestsForListing($listing_id) {
        $listing_id = (int)$listing_id;

        $sql = "SELECT lr.*, 
                user.thumbs_up, user.thumbs_down, user.user_request_count, user.user_listing_count, d.district,
                max(message_id) message_id
                FROM listing_request lr
                JOIN user ON user.user_id = lr.user_id
                JOIN message lrm ON (lrm.request_id = lr.request_id)
                LEFT JOIN district d ON d.district_id = user.district_id
                WHERE lr.listing_id = " . quoteSQL($listing_id) . "
                GROUP BY lr.request_id";

        $requests = runQueryGetAll($sql);
        $message_ids = ArrayHelper::getColumn($requests,"message_id");
        if (sizeof($message_ids)) {
            //attach latest message to request
            $sql = "select request_id,date_created,message,sender_user_id ";
            $sql .= " from message";
            $sql .= " where message_id in (" . arrayToSQLIn($message_ids) . ")";
            $messages = runQueryGetAll($sql);
            $messages = ArrayHelper::setKey($messages,"request_id");
            foreach ($requests as &$resquest) {
                $request_id = $resquest["request_id"];
                $message = $messages[$request_id];
                $resquest["message"] = $message['message'];
                $resquest["message_send_or_receive"] = $message["sender_user_id"] == $resquest["user_id"] ? "received" : "sent";
                $resquest["message_timestamp"] = $message["date_created"];

            }
            $requests = ArrayHelper::sortByColumn($requests,"request_id",'ASC');
        }
        return $requests;
    }


    public static function getOtherPartyDetails($request_id) {
        $am_i_lister = ListingRequest::amILister($request_id);
        if ($am_i_lister) {
            $join_on = 'r.user_id';
        } else {
            $join_on = 'l.user_id';
        }

        $sql = "SELECT u.user_id, u.firstname, u.location, u.thumbs_up, u.thumbs_down
                FROM listing_request r
                JOIN listing l ON l.listing_id = r.listing_id
                JOIN user u ON u.user_id = ".$join_on." 
                WHERE r.request_id = " . quoteSQL($request_id);
        $result = runQueryGetFirstRow($sql);
        if ($am_i_lister) {
            $my_thumbs = Thumb::getThumbsGiven(array($result['user_id']));
            $result['my_thumbs'] = isset($my_thumbs[$result['user_id']])?$my_thumbs[$result['user_id']]:'x';
        } else {
            $result['my_thumbs'] = 'x';
        }
        return $result;
    }


    public static function amILister($request_id) {
        $lister = ListingRequest::amIInvolved($request_id);
        return ($lister == 'lister');
    }

    public static function amIInvolved($request_id) {
        $request_id = (int)$request_id;

        $sql = "SELECT r.user_id requester,
                l.user_id lister
                FROM listing_request r
                JOIN listing l ON l.listing_id = r.listing_id
                WHERE r.request_id = " . quoteSQL($request_id);
        $row = runQueryGetFirstRow($sql);
        if (SESSION_USER_ID == $row["requester"]) {
            return "requester";
        }

        if (SESSION_USER_ID == $row["lister"]) {
            return "lister";
        }

        return FALSE;
    }


    public static function getRequesterUserId($request_id) {
        $request_id = (int)$request_id;

        $sql = "SELECT user_id 
                FROM listing_request 
                WHERE request_id = " . $request_id;
        return (int)runQueryGetFirstValue($sql);
    }

    public static function currentRequestsCount() {
        $sql = "SELECT COUNT(r.request_id) 
                FROM listing_request r
                JOIN listing l ON r.listing_id = l.listing_id
                WHERE l.listing_status IN ('available', 'reserved')";
        return (int)runQueryGetFirstValue($sql);
    }


    public static function recentRequestsBetweenUsers($my_user_id, array $user_ids, $limit = 3) {
        $my_user_id = (int)$my_user_id;
        $user_ids = array_filter($user_ids);

        if (count($user_ids) == 0) {
            return [];
        }

        $sql = "SELECT l.listing_id,l.title, l.user_id lister_user_id, l.title listing_title,
                r.request_timestamp, l.listing_status, 
                IF(l.user_id = " . quoteSQL($my_user_id) . ", 'y', 'n') is_lister,
                IF(r.user_id = " . quoteSQL($my_user_id) . ", l.user_id, r.user_id) other_user_id
                FROM listing l 
                JOIN listing_request r on r.listing_id = l.listing_id
                WHERE (r.user_id = " . quoteSQL($my_user_id) . " AND l.user_id IN " . quoteIN($user_ids) . ") 
                OR 
                (r.user_id in " . quoteIN($user_ids) . " AND l.user_id = " . quoteSQL($my_user_id) . ") 
                ORDER BY request_id desc";
        $result = runQueryGetAll($sql);
        $out = array();
        foreach ($result as $row) {
            $other_user_id = $row['other_user_id'];
            if (!isset($out[$other_user_id])) {
                $out[$other_user_id] = array();
            }

            if (in_array($row['listing_status'], array('available', 'reserved')) || sizeof($out[$other_user_id]) < $limit) {
                $out[$other_user_id][] = $row;
            }
        }
        return $out;
    }

    public static function getAllUsersRequestedFromUserA($user_a, $user_ids = array()) {
        $sql = "SELECT r.user_id, COUNT(r.request_id) count_requests, 
                u_requester.user_listing_count, u_requester.user_request_count, u_requester.thumbs_up, u_requester.thumbs_down
                FROM listing_request r
                JOIN listing l ON (l.listing_id = r.listing_id AND l.listing_type <> 'wanted')
                JOIN user u_requester ON (u_requester.user_id = r.user_id)
                WHERE l.user_id = " . quoteSQL($user_a);
        $sql .= (count($user_ids) > 0) ? " AND r.user_id IN (" . arrayToSQLIn($user_ids) . ')' : '';
        $sql .= " GROUP BY r.user_id";
        $result = runQuery($sql);
        $output = array();
        while ($row = fetchSQL($result)) {
            $output[$row['user_id']] = $row;
        }

        return $output;
    }
}
