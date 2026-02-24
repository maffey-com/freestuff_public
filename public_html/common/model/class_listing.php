<?
class Listing extends CRModel {
    public $temp_id;
    public $listing_id;
    public $user_id;
    public $title;
    public $description;

    public $listing_date;
    public $visits;
    public $last_updated;
    public $ip_address;
    public $has_image;
    public $user_firstname;
    public $removed_reason;
    public $removed_date;

    public $listing_type;
    public $expiry_reminded;
    public $original_listing_date;

    public $request_count;
    public $my_request_id;
    public $district_id;
    public $listing_status;
    public $image_data;
    public $pickup;

    public static $available_listing_types = array('free', 'wanted');

    public static $reserve_expiry_hours = 72;

    public static $all_statuses = array('available', 'reserved', 'gone', 'removed', 'expired');
    public bool $is_my_listing;

    public function __construct() {
        $this->listing_type = 'free';
    }

    public function buildFromPost() {
        foreach ($_POST as $k => $v) {
            $this->$k = $v;
        }
    }

    public function buildFromBackendPost() {
        $this->title = paramFromPost('title');
        $this->description = paramFromPost('description');
        $this->listing_type = paramFromPost('listing_type');
        $this->district_id = paramFromPost('district_id');
        $this->listing_status = paramFromPost('listing_status');
    }

    public function createTempListingId() {
        Listing::checkUserSuspended();
        $filename = FILES_DIR . "/temporary_listing_ids.txt";

        if (file_exists($filename)) {
            $file = file_get_contents($filename);
            $temp_id = $file + 1;
            file_put_contents($filename, $temp_id);
            $this->temp_id = $temp_id;

        } else {
            file_put_contents($filename, '1');
            $this->temp_id = 1;
        }
    }

    public static function currentListingCounter() {
        static $current_listings;

        if (is_null($current_listings)) {
            $sql = "SELECT COUNT(listing_id)
                    FROM listing
                    WHERE listing_status IN ('available', 'reserved')
                    AND listing_type = 'free'";
            $current_listings = (int)runQueryGetFirstValue($sql);
        }
        return $current_listings;
    }

    public function retrieveFromID($listing_id) {
        $listing_id = (int)$listing_id;

        $sql = "SELECT l.listing_id, l.user_id, l.title, l.description, l.listing_date, l.visits,
                l.last_updated, l.ip_address, l.has_image, l.expiry_reminded, l.original_listing_date,
                l.listing_type, l.request_count,l.district_id,l.user_firstname, l.listing_status
                FROM listing l
                WHERE l.listing_id = " . $listing_id;
        $row = runQueryGetFirstRow($sql);

        if ($row) {
            $this->listing_id = $row["listing_id"];
            $this->user_id = $row["user_id"];
            $this->title = $row["title"];
            $this->description = $row["description"];
            $this->listing_date = $row["listing_date"];
            $this->visits = $row["visits"];
            $this->last_updated = $row["last_updated"];
            $this->ip_address = $row["ip_address"];
            $this->has_image = ($row["has_image"] == 'y' ? true : false);
            $this->user_firstname = $row["user_firstname"];
            $this->listing_type = $row["listing_type"];
            $this->expiry_reminded = $row["expiry_reminded"];
            $this->original_listing_date = $row["original_listing_date"];
            $this->request_count = $row['request_count'];
            $this->district_id = $row['district_id'];
            $this->listing_status = $row['listing_status'];

            $this->is_my_listing = ($this->user_id == paramFromSession("session_user_id"));

            return TRUE;
        }
    }


    public function canIEditThisListing() {
        if (!isAdmin() && (paramFromSession("session_user_id") != $this->user_id)) {
            exit();
        }
    }

    public function validate() {
        if (empty($this->listing_type)) {
            raiseError("You must choose a listing type", "listing_type");
        }

        if (empty($this->title)) {
            raiseError("You must enter a title", "title");
        }

        if (empty($this->description)) {
            raiseError("You must enter a description", "description");
        }


        if (empty($this->district_id)) {
            raiseError("Please selected closest district for pickup", "district_id");
        } else {
            $district = new District();
            $district->retrieveFromID($this->district_id);
            if (!$district->district) {
                raiseError("Please selected closest district for pickup", "district_id");
            }
        }

        if (hasErrors()) {
            return false;
        }

        $this->title = strip_tags($this->title);
        $this->description = strip_tags($this->description);



        return true;
    }

    public function insertListing() {
        if ($this->validate()) {
            Listing::checkUserSuspended();


            $sql = "INSERT listing SET ";
            $sql .= " user_id = " . SESSION_USER_ID;
            $sql .= " ,listing_type = " . quoteSQL($this->listing_type);
            $sql .= " ,title = " . quoteSQL($this->title);
            $sql .= " ,description = " . quoteSQL($this->description);
            $sql .= " ,user_firstname = " . quoteSQL(paramFromSession('session_firstname'));
            $sql .= ", ip_address = " . quoteSQL(paramFromHash('REMOTE_ADDR', $_SERVER));
            $sql .= ", district_id = " . quoteSQL($this->district_id);
            $sql .= ", last_updated = NOW()";
            $sql .= ", original_listing_date = NOW()";
            $sql .= ", listing_date = NOW() ";
            if (runQuery($sql)) {
                $this->listing_id = lastInsertedId();
                return true;
            } else {
                return false;
            }
        }
    }

    public function updateFrontEnd() {
        $fh = new FileHelper('temporary_listing_image', "temp_" . $this->temp_id);
        $has_image = $fh->getFileNameFromTag("most_recent_upload");

        if ($this->validate()) {
            $sql = "UPDATE listing SET ";
            $sql .= " title = " . quoteSQL($this->title);
            $sql .= " ,description = " . quoteSQL($this->description);
            $sql .= " ,has_image = " . quoteSQL(($has_image ? "y" : "n"));
            $sql .= ", last_updated = NOW()";
            $sql .= " ,listing_type = " . quoteSQL($this->listing_type);
            $sql .= " ,district_id = " . quoteSQL($this->district_id);
            $sql .= " WHERE listing_id = " . quoteSQL($this->listing_id);
            return runQuery($sql);
        }
    }

    public function updateBackend() {
        if ($this->validate()) {
            $sql = "UPDATE listing SET ";
            $sql .= " last_updated = NOW()";
            $sql .= " ,title = " . quoteSQL($this->title);
            $sql .= " ,description = " . quoteSQL($this->description);
            $sql .= " ,district_id = " . quoteSQL($this->district_id);
            $sql .= " ,listing_type = " . quoteSQL($this->listing_type);
            $sql .= " ,listing_status = " . quoteSQL($this->listing_status);
            $sql .= " WHERE listing_id = " . quoteSQL($this->listing_id);
            return runQuery($sql);
        }
    }

    public static function getRegionsWithCount() {
        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type', 'free');


        $sql = "SELECT district_id
                FROM listing
                WHERE listing_status IN ('available', 'reserved')
                  and district_id is not null
                  and listing_type = 'free'";

        $district_ids = runQueryGetAllFirstValues($sql);
        $out = array_fill_keys(District::$regions,0);
        foreach ($district_ids as $district_id) {

            $district = District::resolve($district_id);
            if ($district && isset($out[$district->region])) {
                $out[$district->region]++;
            }
        }
return $out;

    }

    public static function report($listing_id, $user_id, $report_comment) {
        $listing_id = (int)$listing_id;
        $user_id = (int)$user_id;
        if (strlen($report_comment) < 5) {
            raiseError("Please specify what is wrong with this listing", "report_comment");
            return false;
        }
        $sql = "INSERT report SET ";
        $sql .= " user_id = " . $user_id;
        $sql .= ", report_date = now()";
        $sql .= ", report_comment = " . quoteSQL($report_comment);
        $sql .= ", ip_address = " . quoteSQL($_SERVER["REMOTE_ADDR"]);
        $sql .= ", listing_id = " . $listing_id;
        runQuery($sql);

        $sql = "SELECT COUNT(report_id)
                FROM report
                WHERE listing_id = " . $listing_id;
        $count_reports = (int)runQueryGetFirstValue($sql);

        if ($count_reports > 5) {
            $sql = "UPDATE listing SET
                    listing_status = 'removed'
        		  WHERE listing_id = " . $listing_id;
            runQuery($sql);
        }

        return TRUE;
    }

    public static function profileMark($listing_id,$platform = 'W') {
        $listing_id = (int)$listing_id;

        $sql = "INSERT listing_profile_mark SET";
        $sql .= " listing_id = " . quoteSQL($listing_id);
        if (!empty(SESSION_USER_ID)) {
            $sql .= " , user_id = " . quoteSQL(SESSION_USER_ID);
        }
        $sql .= " ,date = now()";
        $sql .= " ,platform = " . quoteSQL($platform);
        $sql .= " ,ip_address = " . quoteSQL($_SERVER["REMOTE_ADDR"]);
        runQuery($sql);

        $sql = "UPDATE listing SET 
                visits = visits+1 
                WHERE listing_id = " . quoteSQL($listing_id);
        runQuery($sql);
    }

    public function relist() {
        Listing::checkUserSuspended();

        $fh = new FileHelper("listing_images", $this->listing_id);

        if (isset($_SESSION["session_district"]) && is_object($_SESSION["session_district"])) {
            $this->district_id = $_SESSION["session_district"]->district_id;
        }
        $this->insertListing();
        $this->markAsAvailable();
        $fh->cloneFileHelper("listing_images", $this->listing_id);
        return $this->listing_id;
    }

    public function checkUserSuspended() {
        //incase user is suspended but still logged in
        $sql = "SELECT user_id 
                FROM user 
                WHERE user_id = " . SESSION_USER_ID;
        $user_id = runQueryGetFirstValue($sql);
        if (!$user_id) {
            MessageHelper::setSessionErrorMessage('User not found!');
            session_destroy();
            redirect(APP_URL . "login");
        }
    }


    public function remove($reason) {
        $sql = "UPDATE listing SET ";
        $sql .= " listing_status = 'removed' ";
        $sql .= ",  removed_date = now() ";
        $sql .= ", removed_reason =  " . quoteSQL($reason);
        $sql .= " WHERE listing_id = " . (int)$this->listing_id;
        if (runQuery($sql)) {
            $this->listing_status = 'removed';

            $dict = array(
                "__lister_firstname__" => $this->firstname,
                "__listing_title__" => $this->title,
                "__reason__" => $reason
            );

            $eh = new EmailHelper();
            $eh->setTemplate("Remove Listing", $dict);
            $eh->setTo($this->email);
            $eh->send();

            return TRUE;
        }
    }

    public function switchToWanted() {
        $sql = "UPDATE listing SET 
                listing_type = 'wanted' 
                WHERE listing_id = " . quoteSQL($this->listing_id);
        if (runQuery($sql)) {
            $this->listing_type = 'wanted';

            return TRUE;
        }
    }

    public function delete() {
        $this->listing_id = (int)$this->listing_id;
        $sql = "DELETE FROM listing
        		WHERE listing_id = " . $this->listing_id;
        if (!runQuery($sql)) {
            raiseError("Could not delete listing");
            return false;
        }
        $sql = "DELETE FROM listing_request
                    WHERE listing_id = " . $this->listing_id;
        runQuery($sql);
        return TRUE;
    }


    public static function getAllListingFromUserId($user_id, $type = NULL) {
        static $output = array();

        $user_id = (int)$user_id;

        if (!array_key_exists($user_id, $output)) {
            $sql = "SELECT l.*
					FROM listing l
					WHERE l.user_id = " . $user_id . "
					AND l.listing_status IN ('available', 'reserved')";
            $output[$user_id] = runQueryGetAll($sql);
        }

        return $output[$user_id];
    }

    public function updateRequestCount() {
        $sql = "SELECT COUNT(*) 
                FROM listing_request 
                WHERE listing_id = " . quoteSQL($this->listing_id);
        $this->request_count = (int)runQueryGetFirstValue($sql);

        $sql = "UPDATE listing SET
                request_count = " . quoteSQL($this->request_count) . "
                WHERE listing_id = " . quoteSQL($this->listing_id);
        runQuery($sql);
    }

    public function haveIRequestedThisItem() {
        if (is_null($this->my_request_id)) {

            $this->my_request_id = 0;
            if (!empty(SESSION_USER_ID)) {
                $sql = "SELECT request_id 
                        FROM listing_request 
                        WHERE listing_id = " . quoteSQL($this->listing_id) . "
                        AND user_id = " . quoteSQL(SESSION_USER_ID) . "
                        LIMIT 1";
                $this->my_request_id = runQueryGetFirstValue($sql) ;
                $this->my_request_id = $this->my_request_id?:0;
            }
        }

        return $this->my_request_id;
    }

    public function hasReachedMaxRequestLimit() {
        return $this->request_count >= REQUESTS_PER_ITEM_HARD_LIMIT;
    }

    public function canBeRequested() {
        return $this->listing_status == 'available';
    }

    public function isActive() {
        return in_array($this->listing_status, array('available', 'reserved'));
    }

    public function isReserved() {
        return $this->listing_status == 'reserved';
    }

    public function isWanted() {
        return $this->listing_type == 'wanted';
    }

    public function isMyListing() {
        return SESSION_USER_ID == $this->user_id;
    }

    public static function countActiveFreeListings() {
        $sql = "SELECT COUNT(listing_id) 
                FROM listing 
                WHERE listing_status IN ('available', 'reserved') 
                AND listing_type = 'free'";
        return runQueryGetFirstValue($sql);
    }

    public static function countActiveWantedListings() {
        $sql = "SELECT COUNT(listing_id) 
                FROM listing 
                WHERE listing_status = 'available' 
                AND listing_type = 'wanted'";
        return runQueryGetFirstValue($sql);
    }

    public static function recalculateRequestCount($lister_id, $requester_id) {
        $lister_id = (int)$lister_id;
        $requester_id = (int)$requester_id;

        $sql = "SELECT listing.listing_id
                FROM listing_request
                JOIN listing on listing.listing_id = listing_request.listing_id
                WHERE listing.listing_status IN ('available', 'reserved')
                AND listing.user_id = " . $lister_id . "
                AND listing_request.user_id = " . $requester_id;
        $listings = runQueryGetAllFirstValues($sql);

        foreach ($listings as $listing_id) {
            $listing = Listing::instanceFromId($listing_id);
            $listing->updateRequestCount();
        }
    }

    public function hasImage() {
        $sql = "update listing set has_image = 'y' where listing_id = " . quoteSQL($this->listing_id);
        runQuery($sql);

    }

    public function markAsAvailable() {
        return $this->updateListingStatus('available');
    }

    public function markAsReserved() {
        return $this->updateListingStatus('reserved');
    }

    public function markAsGone() {
        return $this->updateListingStatus('gone');
    }

    public function updateListingStatus($status) {
        $sql = "UPDATE listing SET ";
        $sql .= " listing_status = " . quoteSQL($status);
        $sql .= ", reserved_date = " . ($status == 'reserved' ? "NOW()" : "NULL");
        $sql .= " WHERE listing_id = " . quoteSQL($this->listing_id);
        if (runQuery($sql)) {
            $this->listing_status = $status;
            return true;
        }
    }

}
