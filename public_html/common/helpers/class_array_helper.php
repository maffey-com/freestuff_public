<?

/*
 A bunch of static functions that take a string, do something to it and return it
*/

class ArrayHelper {

    public static function printNice($array) {
        echo "<div style='color: black; background: white'><pre>";
        print_r($array);
        echo "</pre></div>";
    }

    public static function getColumn($array, $col_name) {
        $out = array();
        foreach ($array as $row) {
            $out[] = $row[$col_name];
        }
        return $out;
    }

    public static function getHash($array, $key_column, $value_column) {
        $out = array();
        foreach ($array as $row) {
            $out[$row[$key_column]] = $row[$value_column];
        }
        return $out;
    }

    public static function setKey($array, $key_column) {
        $out = array();
        foreach ($array as $row) {
            $out[$row[$key_column]] = $row;
        }
        return $out;
    }

    public static function nestByColumn($array, $key_column) {
        $out_array = array();
        foreach ($array as $row) {
            $nest_key = $row[$key_column];
            if (!isset($out_array[$nest_key])) {
                $out_array[$nest_key] = array();
            }
            $out_array[$nest_key][] = $row;
        }
        return $out_array;
    }

    public static function sortByColumn($array, $field, $order = 'ASC', $preserve_key = true) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $field) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case 'ASC':
                    asort($sortable_array);
                    break;
                case 'DESC':
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                if ($preserve_key) {
                    $new_array[$k] = $array[$k];
                } else {
                    $new_array[] = $array[$k];
                }
            }
        }

        return $new_array;
    }

    function arrayToCsv(array &$fields, $delimiter = ',', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false) {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ($fields as $field) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ($encloseAll || $field == 'NULL' || preg_match("/(?:{$delimiter_esc}|{$enclosure_esc}|\s)/", $field)) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            } else {
                $output[] = $field;
            }
        }

        return implode($delimiter, $output);
    }


    /*adds an item to the begining of an associative array*/
    static function arrayUnshiftAssoc(&$arr, $key, $val) {
        $arr = array_reverse($arr, true);
        $arr[$key] = $val;
        return array_reverse($arr, true);
    }

}

