<?
//class designed to handle dates
//can handle unix timestamps,
//   --  NZ_DATE (dd/mm/yyyy),
//   -- SHORT_DISPLAY date eg 10 Feb 2010
//   -- LONG_DISPLAY date eg 10 February 2010
//  -- DB date

class DateHelper {
    public static $shortmonth_names = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    public static $longmonth_names = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

    public $input_format;
    public $input_date;

    public $year;
    public $month;
    public $day;
    public $hour;
    public $minute;
    public $second;

    public function __construct($in_date) {
        $this->input_date = $in_date;
        $this->_breakDateIntoComponents();
    }

    public static function load($in_date) {
        $date_in = trim($in_date);
        $date_in = str_ireplace('  ', ' ', $in_date);

        static $load_array;

        if (!is_array($load_array) || !array_key_exists($in_date, $load_array)) {
            $load_array[$in_date] = new DateHelper($in_date);
        }
        return $load_array[$in_date];
    }

    protected function _getInputFormat() {
        if (is_null($this->input_format)) {
            if (preg_match("|^[0-9]{7,12}$|", $this->input_date)) {
                $this->input_format = 'TIMESTAMP';

            } else {
                if (preg_match("|^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}|", $this->input_date)) {
                    $this->input_format = 'NZ_DATE';

                } else {
                    if (preg_match("|^[0-9]{1,2}\s[a-zA-Z]{3,3}\s[0-9]{2,4}|", $this->input_date)) {
                        $this->input_format = 'SHORT_DISPLAY';

                    } else {
                        if (preg_match("|^[0-9]{1,2}\s[a-zA-Z]{3,9}\s[0-9]{2,4}|", $this->input_date)) {
                            $this->input_format = 'LONG_DISPLAY';

                        } elseif (preg_match("|^[0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2}|", $this->input_date)) {
                            $this->input_format = 'DB';
                        } elseif (empty($this->input_date)) {
                            $this->input_format = 'EMPTY';

                        } else {
                            return raiseError('Date does not match any available format');
                            //die('Date does not match any available format');
                        }
                    }
                }
            }
        }
    }

    protected function _breakDateIntoComponents() {
        $this->_getInputFormat();

        $APM = '';

        switch ($this->input_format) {
            case 'TIMESTAMP':
                $this->year = date('Y', $this->input_date);
                $this->month = date('m', $this->input_date);
                $this->day = date('d', $this->input_date);
                $this->hour = date('H', $this->input_date);
                $this->minute = date('i', $this->input_date);
                $this->second = date('s', $this->input_date);
                $APM = '';
                break;

            case 'NZ_DATE':
                preg_match("|([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})\s?([0-9]{1,2})?\:?([0-9]{1,2})?[:. ]?([0-9]{1,2})?([ap]m)?|", $this->input_date, $matches);

                $this->year = $matches[3];
                $this->month = $matches[2];
                $this->day = $matches[1];
                $this->hour = (isset($matches[4]) ? $matches[4] : '00');
                $this->minute = (isset($matches[5]) ? $matches[5] : '00');
                $this->second = (isset($matches[6]) ? $matches[6] : '00');

                $APM = (isset($matches[7]) ? $matches[7] : '');
                break;

            case 'SHORT_DISPLAY':
                preg_match("|([0-9]{1,2})\s([a-zA-Z]{3,3})\s([0-9]{2,4})\s?([0-9]{1,2})?\:?([0-9]{1,2})?[:. ]?([0-9]{1,2})?([ap]m)?|", $this->input_date, $matches);

                $this->year = $matches[3];
                $this->month = array_search(ucfirst($matches[2]), self::$shortmonth_names) + 1;
                $this->day = $matches[1];
                $this->hour = (isset($matches[4]) ? $matches[4] : '00');
                $this->minute = (isset($matches[5]) ? $matches[5] : '00');
                $this->second = (isset($matches[6]) ? $matches[6] : '00');

                $APM = (isset($matches[7]) ? $matches[7] : '');
                break;

            case 'LONG_DISPLAY':
                preg_match("|([0-9]{1,2})\s([a-zA-Z]{3,9})\s([0-9]{2,4})\s?([0-9]{1,2})?\:?([0-9]{1,2})?[:. ]?([0-9]{1,2})?([ap]m)?|", $this->input_date, $matches);

                $this->year = $matches[3];
                $this->month = array_search(ucfirst($matches[2]), self::$longmonth_names) + 1;
                $this->day = $matches[1];
                $this->hour = (isset($matches[4]) ? $matches[4] : '00');
                $this->minute = (isset($matches[5]) ? $matches[5] : '00');
                $this->second = (isset($matches[6]) ? $matches[6] : '00');

                $APM = (isset($matches[7]) ? $matches[7] : '');
                break;

            case 'DB':
                preg_match("|([0-9]{4,4})\-([0-9]{2,2})\-([0-9]{2,2})\s?([0-9]{1,2})?\:?([0-9]{1,2})?[:. ]?([0-9]{1,2})?([ap]m)?|", $this->input_date, $matches);

                $this->year = $matches[1];
                $this->month = $matches[2];
                $this->day = $matches[3];
                $this->hour = (isset($matches[4]) ? $matches[4] : '00');
                $this->minute = (isset($matches[5]) ? $matches[5] : '00');
                $this->second = (isset($matches[6]) ? $matches[6] : '00');

                $APM = (isset($matches[7]) ? $matches[7] : '');
                break;
            case 'EMPTY':


                break;
            default:
                return raiseError('Error parsing date');
                break;
        }

        //convert to 24 hour
        if (!empty($APM)) {
            if ($APM == 'pm') {
                if ($this->hour < 12) {
                    $this->hour = $this->hour + 12;
                }

            } elseif ($APM == 'am') {
                if ($this->hour == 12) {
                    $this->hour = '00';
                }
            }
        }

        if (strlen($this->year) == 2) {
            if ($this->year > 50) {
                $this->year = '19' . $this->year;
            } else {
                $this->year = '20' . $this->year;
            }
        }

        //leading zeros on everything
        $this->year = str_pad($this->year, 4, '0', STR_PAD_LEFT);
        $this->month = str_pad($this->month, 2, '0', STR_PAD_LEFT);
        $this->day = str_pad($this->day, 2, '0', STR_PAD_LEFT);
        $this->hour = str_pad($this->hour, 2, '0', STR_PAD_LEFT);
        $this->minute = str_pad($this->minute, 2, '0', STR_PAD_LEFT);
        $this->second = str_pad($this->second, 2, '0', STR_PAD_LEFT);
    }

    public function validateInputDate() {
        if (hasErrors()) {
            return raiseError('Invalid Time  - has errors');

        } elseif ($this->input_format == "EMPTY") {
            return true;
        } elseif (!checkdate($this->month, $this->day, $this->year)) {
            return raiseError('Invalid Gogorian date');

        } elseif ($this->hour > 23 || $this->minute > 59 || $this->second > 59) {
            return raiseError('Invalid Time');

        } else {
            return true;
        }
    }

    public function convertToTimestamp() {
        if ($this->validateInputDate()) {
            return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
        }
    }

    public function convertToAgo() {
        if ($this->validateInputDate()) {
            $time = $this->convertToTimestamp();
            $now = time(); // current time
            $diff = $now - $time; // difference between the current and the provided dates

            if ($diff < 60) {
                // it happened now
                return 'now';
            } elseif ($diff < 3600) { // it happened X minutes ago
                return str_replace('{num}', ($out = round($diff / 60)), $out == 1 ? '{num} minute ago' : '{num} minutes ago');
            } elseif ($diff < 3600 * 24) {
                // it happened X hours ago
                return str_replace('{num}', ($out = round($diff / 3600)), $out == 1 ? '{num} hour ago' : '{num} hours ago');
            } elseif ($diff < 3600 * 24 * 2) {
                // it happened yesterday
                return str_replace('{num}', ($out = round($diff / 3600)), $out == 1 ? '{num} hour ago' : '{num} hours ago');
            } else {
                // falling back on a usual date format as it happened later than yesterday
                return date(date('Y', $time) == date('Y') ? 'j M' : 'j M, Y', $time);
            }
        }
    }


    /**
     *
     * Return date/&time in long/short NZ format
     * @param string $date_in
     * @param boolean $short
     * @param boolean $include_time
     * @param boolean $seconds
     * @param boolean $am_pm
     */
    public
    static function display($date_in, $short = FALSE, $include_time = FALSE, $seconds = FALSE, $am_pm = FALSE) {
        $date_in = trim($date_in);

        if (empty($date_in)) {
            return '';
        }

        $instance = self::load($date_in);

        if ($instance->validateInputDate()) {
            if ($instance->input_format == "EMPTY") {
                return "";
            }
            $time_stamp = $instance->convertToTimestamp();

            $format = ($short) ? 'j M Y' : 'j F Y';
            $rtn_value = date($format, $time_stamp);
            $rtn_value .= (!$include_time) ? '' : ' ' . self::displayTime($date_in, $seconds, $am_pm);
            return $rtn_value;
        }
    }

    /**
     *
     * Return time
     * @param string $date_in
     * @param boolean $seconds
     * @param boolean $am_pm
     */
    public
    static function displayTime($date_in, $seconds = false, $am_pm = false) {
        $instance = self::load($date_in);
        if ($instance->validateInputDate()) {
            $time_stamp = $instance->convertToTimestamp();

            $format = ($am_pm) ? 'h' : 'H';
            $format .= ':i';
            $format .= ($seconds) ? ':s' : '';
            $format .= ($am_pm) ? 'A' : '';

            $rtn_value = date($format, $time_stamp);
            return $rtn_value;
        }
    }

    /**
     *
     * formats a date to go into mysql database, by default, the date is wrapped in quotes
     * @param string $date_in
     * @param boolean $include_time
     * @param boolean $quotes
     */
    public
    static function db($date_in, $include_time = true, $quotes = true) {
        $instance = self::load($date_in);

        $rtn_value = "";

        if ($instance->validateInputDate()) {
            $rtn_value = $instance->year . '-' . $instance->month . '-' . $instance->day;
            $rtn_value .= (!$include_time) ? '' : ' ' . $instance->hour . ':' . $instance->minute . ':' . $instance->second;
        }

        return ($quotes) ? quoteSQL($rtn_value) : $rtn_value;
    }


    /**
     *
     * interval is in the form +/- with units eg + 10 DAYS, - 2 MONTHS
     * @param string $date_in
     * @param string $interval
     */
    public
    static function maths($date_in, $interval) {
        $instance = self::load($date_in);

        return strtotime("$interval", $instance->convertToTimestamp());
    }

    /**
     *
     * Calculator different between 2 days (day2 - day1
     * @param string $date_from
     * @param string $date_to
     * @param string SECOND|MINUTE|HOUR|DAY|MONTH|YEAR|PRETTY $unit
     * @param boolean $display_unit
     */
    public
    static function difference($date_1, $date_2 = NULL, $unit = 'SECOND', $display_unit = true) {
        $date_2 = is_null($date_2) ? time() : $date_2;

        $instance_1 = self::load($date_1);
        $instance_2 = self::load($date_2);

        $timestamp_1 = (int)$instance_1->convertToTimestamp();
        $timestamp_2 = (int)$instance_2->convertToTimestamp();

        $timestamp_difference = $timestamp_2 - $timestamp_1;
        $divider = 1;

        if ($unit == 'PRETTY') {
            return (self::_drillDownDifference($timestamp_difference));
        } else {
            $divider = self::_getUnitSecondDivider($unit);
        }

        if (!hasErrors()) {
            $difference = round($timestamp_difference / $divider, 2);
            $rtn = $difference;

            if ($display_unit) {
                $rtn .= ' ' . strtolower($unit);

                if (abs($difference) > 1) {
                    $rtn .= 's';
                }
            }
            return $rtn;
        }
    }

    protected
    static function _getUnitSecondDivider($unit) {
        switch ($unit) {
            case 'SECOND':
                $divider = 1;
                break;
            case 'MINUTE':
                $divider = 60;
                break;
            case 'HOUR':
                $divider = 60 * 60;
                break;
            case 'DAY':
                $divider = 60 * 60 * 24;
                break;
            case 'MONTH':
                $divider = 60 * 60 * 24 * 31;
                break;
            case 'YEAR':
                $divider = 60 * 60 * 24 * 365;
                break;
            default:
                return raiseError('Invalid unit');
                break;
        }
        return $divider;
    }

    protected
    static function _drillDownDifference($timestamp_difference, $unit = 'MONTH', &$rtn_value = '') {
        $value = floor($timestamp_difference / self::_getUnitSecondDivider($unit));
        $timestamp_difference = $timestamp_difference - (self::_getUnitSecondDivider($unit) * $value);

        if (abs($value) > 1) {
            $rtn_value .= ' ' . $value . ' ' . strtolower($unit);
            $rtn_value .= (abs($value) > 1) ? 's' : '';
        }

        if ($unit == 'SECOND') {
            return $rtn_value;
        } else {
            $tmp = array('YEAR', 'MONTH', 'DAY', 'HOUR', 'MINUTE', 'SECOND');
            $next_unit = $tmp[array_search($unit, $tmp) + 1];

            return self::_drillDownDifference($timestamp_difference, $next_unit, $rtn_value);
        }
    }

    public
    static function format($date, $format) {
        $tmp = new DateHelper($date);
        return date($format, $tmp->convertToTimestamp());
    }

    /**
     *
     * formats a date to go into unix timestamp
     * @param string $date_in
     */
    public
    static function timestamp($date_in) {
        $instance = self::load($date_in);
        return $instance->convertToTimestamp();
    }

    /**
     *
     * Validate a time, return 0 or 1
     * @param string $date_in
     */
    public
    static function validate($date_in) {
        $instance = self::load($date_in);
        return $instance->validateInputDate();
    }

    /*
     returns all days between two dates
    */
    public
    static function returnDayBetweenArray($date_1, $date_2) {
        $instance_1 = self::load($date_1);
        $instance_2 = self::load($date_2);

        $day_diff = floor(self::difference($date_1, $date_2, 'DAY', false));

        $rtn_value = array();

        for ($i = 0; $i <= $day_diff; $i++) {
            $formular = '+ ' . $i . ' DAYS';
            $tmp = self::maths($instance_1->input_date, $formular);
            $rtn_value[self::display($tmp)] = NULL;
        }

        return $rtn_value;
    }

    public static function ago($date_in) {
        $instance = self::load($date_in);
        return $instance->convertToAgo();

    }
}
