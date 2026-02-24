<?
class SavedSearch extends CRModel {
    public $search_id;
    public $created_date;
    public $user_id;
    public $search_string;
    public $listing_type;
    public $regions;

    /**
     * class constructor
     */
    public function __construct() {
        $this->_primary_key = 'search_id';
        $this->_setHintNumeric('search_id', 'user_id');
    }

    /**
     * retrieve values from $_POST and set it to the object
     */
    public function buildFromPost() {
        $this->_populateFromArray($_POST);

        $this->search_id = (int)$this->search_id;
        $this->user_id = (int)$this->user_id;
    }

    public function setMyDefaults() {
        $this->user_id = (int)paramFromSession("session_user_id");
        $this->regions = array(paramFromSession("session_district")->region);

        if (isset($_SESSION["listing_filter"])) {
            $this->listing_type = paramFromSession("listing_filter");
        }

        if (!$this->listing_type || !is_array($this->listing_type)) {
            $this->listing_type = array('free');
        }
    }

    public static function getRegionNotifications($region) {
        $regions = array($region);
        $saved_search = new SavedSearch();
        $sql = "SELECT * FROM saved_search WHERE user_id = ".quoteSQL(SESSION_USER_ID). " AND search_string IS NULL";
        $region_search = runQueryGetFirstRow($sql);
        if ($region_search) {
            $regions = explode(',', $region_search['regions']);
            $regions[] = $region;
            $saved_search->retrieveFromID($region_search['search_id']);
        } else {

            $saved_search->setMyDefaults();
        }

        $saved_search->search_string = '';
        $saved_search->regions = array_unique($regions);
        $saved_search->save();
    }

    /**
     * retrieve database record and set it to the object
     * @param INT $search_id
     */
    public function retrieveFromID($search_id) {
        $search_id = (int)$search_id;

        $sql = "SELECT search_id, created_date, user_id, search_string, listing_type, regions
				FROM saved_search 
				WHERE search_id = " . quoteSQL($search_id);
        $row = runQueryGetFirstRow($sql);
        if ($row) {
            $this->_populateFromArray($row);
            $this->listing_type = explode(",", $this->listing_type);
            $this->regions = explode(",", $this->regions);
        }
    }

    /**
     * Validate value
     * @param STRING $type
     */
    public function validate($type = NULL) {
        if ($type != 'insert') {
            $this->_validateRequiredField('search_id', 'Search Id');
        } else {
            $this->_validateRequiredField('user_id', 'User');
        }
        $this->_validateIntField('search_id', 'Search Id');



        //check unique
        $sql = "SELECT search_string 
                FROM saved_search
                WHERE user_id = " . quoteSQL($this->user_id);
        $sql .= " AND search_string = " . quoteSQL($this->search_string);
        $sql .= " AND search_id <> " . quoteSQL($this->search_id);
        $dupe = runQueryGetFirstValue($sql);

        if ($dupe) {
            raiseError("Duplicated String");
        }

        $this->search_string = strtolower($this->search_string ?? '');

        if (!sizeof($this->regions)) {
            raiseError("At least one region required", "regions");
        }

        if (!sizeof($this->listing_type)) {
            raiseError("Please choose either free, wanted or both", "listing_type");
        }

        return !hasErrors();
    }

    /**
     * Insert record to database
     */
    public function insert() {
        if ($this->validate('insert')) {
            $sql = "INSERT saved_search SET ";
            $sql .= $this->_sqlSETHelper( 'user_id', 'search_string');
            $sql .= ",regions = " . quoteSQL(implode(",", $this->regions));
            $sql .= ",listing_type = " . quoteSQL(implode(",", $this->listing_type));
            $sql .= ", created_date = NOW()";
            if (runQuery($sql)) {
                $this->search_id = lastInsertedId();
                return true;
            }
        }
    }

    /**
     * Update db record
     */
    public function update() {
        if ($this->validate()) {
            $sql = "UPDATE saved_search SET ";
            $sql .= $this->_sqlSETHelper( 'search_string');
            $sql .= ",regions = " . quoteSQL(implode(",", $this->regions));
            $sql .= ",listing_type = " . quoteSQL(implode(",", $this->listing_type));
            $sql .= " WHERE search_id = " . quoteSQL($this->search_id);
            return runQuery($sql);
        }
    }

    /**
     *
     * Delete record from db
     * @param int $search_id
     * @return bool|mysqli_result
     */
    public static function delete($search_id) {
        $search_id = (int)$search_id;

        $sql = "DELETE FROM saved_search 
				WHERE search_id = " . quoteSQL($search_id);
        return runQuery($sql);
    }

    public static function deleteAllForUser($user_id) {
        $sql = "DELETE FROM saved_search 
				WHERE user_id = " . quoteSQL($user_id);
        return runQuery($sql);
    }

    public function expire() {
        return self::delete($this->search_id);
    }

    public static function mySavedSearches() {
        $sql = "SELECT search_id, search_string, regions, listing_type 
                FROM saved_search
                WHERE user_id = " . (int)paramFromSession("session_user_id");
        return runQueryGetAll($sql);
    }

    public function isMine() {
        return ($this->user_id == paramFromSession("session_user_id"));
    }
}
