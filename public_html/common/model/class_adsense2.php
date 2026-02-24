<?

define("ADSENSE_START_DATE", "2012-01-01");

class Adsense2 {
    var $token;
    protected $apiClient;
    var $earnings_per_day;

    var $last_updated;

    static $api_client_id = "x";
    static $api_client_secret = "y";
    static $redirect_uri = SITE_URL . "z";


    static $publisher_id = "1";

    function __construct() {
        $this->earnings_per_day = array();
    }

    /**
     * @param $params
     * @return void
     */

    public function authCodeToToken($code) {
        $params = array();
        $params['code'] = $code;
        $params['grant_type'] = "authorization_code";
        $this->token  = $this->_tokenApi($params);
    }

    public function refreshToken() {
        $params = array();
        $params['refresh_token'] = $this->token['refresh_token'];
        $params['grant_type'] = "refresh_token";
        $this->token['access_token'] = $this->_tokenApi($params)['access_token'];
    }

    private function _tokenApi($params) {
        $url = "https://oauth2.googleapis.com/token";
        $params['client_id'] = self::$api_client_id;
        $params['client_secret'] = self::$api_client_secret;
        $params['redirect_uri'] = self::$redirect_uri;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // Return response instead of printing
        curl_setopt($ch, CURLOPT_POST, true);              // Set method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);   // Set POST data
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // If dealing with https, this line can help in case of certificate issues (though not recommended for production)

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Request error:' . curl_error($ch);
        }

        curl_close($ch);

        if (curl_errno($ch)) {
            throw new exception('Request error:' . curl_error($ch));
        } else {
            $data = json_decode($response, true);

            if (isset($data['error'])) {
                throw new exception('Data error:' . $data['error']);
            }
            return $data;
        }

    }


    function __wakeup() {

    }

    /**
     * @return void
     * @throws exception
     */
    public function fetchEarnings() {
        $this->refreshToken();
        $queries = array("monthly" =>
            array(
                'dimensions' => 'MONTH',
                'metrics' => 'ESTIMATED_EARNINGS',
                'startDate.day' => '01',
                'startDate.month' => '01',
                'startDate.year' => date('Y', strtotime("-3 years")),
                'endDate.day' => '31',
                'endDate.month' => '12',
                'endDate.year' => date('Y')
            ),
            "daily" =>
                array(
                    'dimensions' => 'DATE',
                    'metrics' => 'ESTIMATED_EARNINGS',
                    'dateRange' => 'LAST_30_DAYS'
                )
        );

        $results = array();
        foreach ($queries as $range => $params) {
            $url = "https://adsense.googleapis.com/v2/accounts/" . self::$publisher_id . "/reports:generate?" . http_build_query($params);

            $ch = curl_init($url);

            $headers = [
                "Authorization: Bearer {$this->token['access_token']}",
                "Accept: application/json"
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new exception('Request error:' . curl_error($ch));
            } else {
                $data = json_decode($response, true);

                if (isset($data['error'])) {
                    throw new exception('Data error:' . $data['error']);
                }

                $results[$range] = $data['rows'];
            }

            curl_close($ch);
        }

        $this->last_updated = time();

        //update daily data in adsense object
        foreach ($results['daily'] as $row) {
            $date = $row['cells'][0]['value'];
            $dollars = $row['cells'][1]['value'];
            $this->earnings_per_day[$date] = $dollars;
        }
        $this->save();


        //Update monthly stats
        foreach ($results['monthly'] as $row) {
            $month = $row['cells'][0]['value'];
            $dollars = $row['cells'][1]['value'];
            $int_month = (int)StringHelper::removeNonNumeric($month);
            $sql = "insert stats_monthly set adsense_earnings = $dollars,  month = $int_month ON DUPLICATE KEY UPDATE adsense_earnings = $dollars";
            runQuery($sql);
        }
        return $results;;
    }

    public function latestMonths() {
        $sql = "select month,adsense_earnings from stats_monthly where month >= " . quoteSQL(date("Ym", strtotime(ADSENSE_START_DATE))) . " order by month desc";
        $month_data = runQueryGetHash($sql);
        $months = array();

        $months = array();
        foreach ($month_data as $date => $earnings) {
            $month_array = array();
            $year = substr($date, 0, 4);
            $month = substr($date, 4, 2);

            if ($year == date("Y") && $month == date("m")) {
                $days = date("d");
            } else {
                $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            }
            $month_array["dollars"] = $earnings;
            $month_array["no_of_days"] = $days;
            $month_array["per_day"] = number_format($earnings / $days, 2);
            $month_array["pretty_date"] = date("M Y", strtotime($year . "-" . $month . "-01"));
            $months[] = $month_array;
        }

        //pretty month names
        return $months;
    }

    public function latestDays() {
        $days = array();
        krsort($this->earnings_per_day);
        foreach ($this->earnings_per_day as $date => $dollars) {
            $days[] = $dollars;
        }


        return $this->earnings_per_day;
    }


    function save() {
        $data = serialize($this);
        $sql = "delete from adsense";
        runQuery($sql);
        $sql = "insert into adsense set serial = " . quoteSQL($data);
        runQuery($sql);
    }


    function hasCurrentData() {
        if (isset($this->last_updated) && (int)$this->last_updated + 86400 > time()) {
            return true;
        }
        return false;
    }

    public function hasToken() {
        if ($this->token && is_array($this->token)) {
            return true;
        }
        return false;
    }

    static function retrieve() {
        $sql = "select serial from adsense";
        $serial = runQueryGetFirstValue($sql);
        if ($serial) {
            $adsense = unserialize($serial);
        } else {
            $adsense = new Adsense2();
        }
        return $adsense;
    }

    static function adsenseAuthUrl() {
        return "https://accounts.google.com/o/oauth2/v2/auth?client_id=" . self::$api_client_id . '&redirect_uri=' . self::$redirect_uri . '&response_type=code&scope=https://www.googleapis.com/auth/adsense.readonly&access_type=offline&prompt=consent';
    }


}




