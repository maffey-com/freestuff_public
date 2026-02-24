<?

class District extends CRModel {
    public $district_id;
    public $district;
    public $region;

    static $regions = array("Northland", "Auckland", "Waikato", "Bay of Plenty", "Gisborne", "Hawkes Bay", "Taranaki", "Manawatu-Wanganui", "Wellington",
        "Nelson-Tasman", "Marlborough", "West Coast", "Canterbury", "Otago", "Southland");

    /**
     * class constructor
     */
    public function __construct($district_id = false, $district = '', $region = '') {
        $this->district_id = $district_id;
        $this->district = $district;
        $this->region = $region;
        $this->_primary_key = 'district_id';

    }

    /**
     * retrieve values from $_POST and set it to the object
     */
    public function buildFromPost() {
        $this->_populateFromArray($_POST);
    }

    /**
     * retrieve database record and set it to the object
     * @param INT $district_id
     */
    public function retrieveFromID($district_id) {
        $district_id = (int)$district_id;

        $sql = "SELECT  district_id, district, region
				FROM district 
				WHERE district_id = " . quoteSQL($district_id);
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
            $this->_validateRequiredField('district_id', 'District Id');
        }
        $this->_validateRequiredField('district', 'District');
        $this->_validateRequiredField('region', 'Region');

        return !hasErrors();
    }

    /**
     * Insert record to database
     */
    public function insert() {
        if ($this->validate('insert')) {
            $sql = "INSERT district SET ";
            $sql .= $this->_sqlSETHelper('district', 'region');

            if (runQuery($sql)) {
                $this->district_id = lastInsertedId();
                return true;
            }
        }
    }

    /**
     * Update db record
     */
    public function update() {
        if ($this->validate()) {
            $sql = "UPDATE district SET ";
            $sql .= $this->_sqlSETHelper('district', 'region');
            $sql .= " WHERE district_id = " . quoteSQL($this->district_id);
            return runQuery($sql);
        }
    }

    /**
     * Delete record from db
     */
    public static function delete($district_id) {
        $district_id = (int)$district_id;

        $sql = "DELETE FROM district 
				WHERE district_id = " . quoteSQL($district_id);
        return runQuery($sql);
    }


    /**
     * Look up the name of a record, based on the id
     */

    public static function resolve($district_id) {
        $nested = self::getAllNested();
        foreach ($nested as $region_name => $districts) {
            foreach ($districts as $i_district_id => $district_name) {
                if ($district_id == $i_district_id) {
                    return new District($district_id, $district_name, $region_name);
                }
            }
        }
        return false;
    }

    public static function resolveRegionName($district_id) {
        $district = self::resolve($district_id);
        if ($district) {
            return $district ? $district->region : '';
        }
    }

    public static function districtIdsForRegion($region_name) {
        $nested = self::getAllNested();
        if (isset($nested[$region_name])) {
            return array_keys($nested[$region_name]);
        }
        return array();
    }

    public static function display($district_id) {
        $district = self::resolve($district_id);
        if ($district) {
            return $district->region . ' - ' . $district->district;
        }
    }

    public static function display2($district_id) {
        $district = self::resolve($district_id);
        if ($district) {
            return $district ? $district->district . ', ' . $district->region : '';
        }
    }

    public static function displayRegion($district_id) {
        $sql = "SELECT region FROM district WHERE district_id = " . quoteSQL($district_id);
        return runQueryGetFirstValue($sql);
    }

    public static function displayShort($district_id) {
        $district = self::resolve($district_id);
        if ($district) {
            return  $district->district;
        }
    }


    public static function getAllNested() {
        //uses session as cache for speed
        if (!isset($_SESSION['district_cache'])) {
            $sql = "select * from district order by district";
            $result = runQuery($sql);
            $regions = array_fill_keys(self::$regions, array());

            while ($row = fetchSQL($result)) {
                $regions[$row['region']][$row['district_id']] = $row['district'];
            }
            $_SESSION['district_cache'] = $regions;
        }
        return $_SESSION['district_cache'];
    }
}

