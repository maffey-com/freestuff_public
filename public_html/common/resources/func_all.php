<?php
//framework
function paramFromHash($field, $hash, $default = NULL) {
    if (isset($hash[$field])) {
        if (is_string($hash[$field])) {
            return trim($hash[$field]);
        } else {
            return $hash[$field];
        }
    } else {
        return $default;
    }
}

function paramFromGlobal($field, $default = '') {
    global ${$field};

    $value = (empty(${$field})) ? $default : ${$field};

    return is_string($value) ? trim($value) : $value;
}

function paramFromGet($field, $default = NULL) {
    $input = isset($_GET) ? $_GET : array();

    return paramFromHash($field, $input, $default);
}

function paramFromPost($field, $default = NULL) {
    $input = isset($_POST) ? $_POST : array();

    return paramFromHash($field, $input, $default);
}

function paramFromRequest($field, $default = NULL) {
    $input = isset($_REQUEST) ? $_REQUEST : array();

    return paramFromHash($field, $input, $default);
}

function paramFromSession($field, $default = NULL) {
    $input = isset($_SESSION) ? $_SESSION : array();

    return paramFromHash($field, $input, $default);
}

function paramFromCookie($field, $default = NULL) {
    $input = isset($_COOKIE) ? $_COOKIE : array();

    return paramFromHash($field, $input, $default);
}


// page action
function pa($default = "list") {
    if (isset($_REQUEST["pa"])) {
        return $_REQUEST["pa"];
    } else {
        return $default;
    }
}

function clean($in) {
    $out = strip_tags($in);
    $out = htmlentities($out);

    return $out;
}

function h($in, $type = "") {
    if ($type != "raw") {
        $in = clean($in);
    }
    echo $in;
}

/**
 *
 * Retrieve variable from either $_REQUEST or $_SESSION
 * $context is used to avoid crashing of session variable name in multiple places
 * Examples:  sticky('place_id', '', 'place.list'), sticky('place_id', '', 'place.edit')
 *
 * @param string $variable
 * @param string [$default]
 * @param string [$context]
 */
function sticky($variable, $default = '', $context = NULL) {
    if (is_null($context)) {
        $session_name = $variable;
    } else {
        $session_name = $context . '.' . $variable;
    }

    if (isset($_REQUEST[$variable])) {
        $value = paramFromRequest($variable);
        $_SESSION[$session_name] = $value;

        return $value;
    }

    $value = paramFromSession($session_name);
    if (!empty($value)) {
        return $value;
    }

    return $default;
}

function page() {
    return substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1);
}

//database helpers
function quoteSQL($foo, $set_null = TRUE) {
    global $DB_connect;
    if (empty($foo) && $set_null) {
        return 'NULL';
    } else {
        return "'" . mysqli_escape_string($DB_connect, $foo) . "'";
    }
}


function quoteIN($array) {
    $bar = '';
    foreach ($array as $value) {
        if ($bar != '') {
            $bar .= ', ';
        }
        $bar .= quoteSQL($value, false);
    }
    return "($bar)";
}


function fetchSQL($result) {
    if ($result) {
        return mysqli_fetch_assoc($result);
    }
}

function countSQL($result) {
    if ($result) {
        return (int)mysqli_num_rows($result);
    } else {
        return 0;
    }
}

function arrayToSQLIn($array) {
    $bar = "";
    foreach ($array as $value) {
        if ($bar != "") {
            $bar .= ",";
        }
        $bar .= quoteSQL($value, FALSE);
    }

    return $bar;
}

function runQuery($sql) {
    global $DB_connect;
    $command = strtolower(preg_replace('/[^A-Za-z]/', '', substr($sql, 0, stripos($sql, " "))));
    $sessionUsername = isset($_SESSION["session_username"]) ? $_SESSION["session_username"] : '';

    if ($command == "delete" || $command == "insert" || $command == "update") {
        writeLog(paramFromHash('REMOTE_ADDR', $_SERVER, 'LOCAL') . " - " . $_SERVER['PHP_SELF'] . " - " . $sessionUsername . " - " . $sql, "sql.log");
    }
    $result = mysqli_query($DB_connect, $sql);

    if ($result) {
        return $result;
    } else {
        writeLog($_SERVER['REMOTE_ADDR'] . " - " . $_SERVER['PHP_SELF'] . " - " . $sessionUsername . " - " . $sql . ' - ' . mysqli_error($DB_connect), "sql_error.log");
        if (isset($_SERVER["DEV"])) {
            echo '<span style="color:green">' . $sql . '</span><br/><span style="color:red">' . mysqli_error($DB_connect) . "</span>";
        }
        raiseError("Lookup failed. ");

        return FALSE;
    }
}

function runQueryGetAll($sql) {
    $result = runQuery($sql);
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }

    return $arr;
}

function runQueryGetStructured($sql) {
    //$args = func_get_args();
    $result = runQuery($sql);
    $breaks = array_slice(func_get_args(), 1);
    $last_item = $breaks[sizeof($breaks) - 1];
    if (is_string($last_item)) {
        $last_item_primary_key = array_pop($breaks);
    }
    $required_columns = array();
    foreach ($breaks as $break) {
        foreach ($break as $col_name) {
            $required_columns[] = $col_name;
        }
    }
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $ptr = &$arr;
        foreach ($breaks as $break) {
            $primary_key = $break[0];

            if (!isset($ptr[$row[$primary_key]])) {
                foreach ($row as $key => $value) {
                    if (in_array($key, $break)) {
                        $ptr[$row[$primary_key]][$key] = $value;
                    }
                }
                $ptr[$row[$primary_key]]["nested_items"] = array();
            }
            $ptr = &$ptr[$row[$primary_key]]["nested_items"];
        }
        //remaining columns
        $remainder_of_row = array();
        foreach ($row as $key => $value) {
            if (!in_array($key, $required_columns)) {
                $remainder_of_row[$key] = $value;
            }
        }
        if (isset($last_item_primary_key)) {
            if (!empty($remainder_of_row[$last_item_primary_key])) {
                $ptr[$remainder_of_row[$last_item_primary_key]] = $remainder_of_row;
            }
        } else {
            $ptr[] = $remainder_of_row;
        }
    }

    return $arr;
}

function runQueryGetFirstRow($sql) {
    $result = runQuery($sql);
    if ($result) {
        return mysqli_fetch_assoc($result);
    }

    return FALSE;
}

function runQueryGetFirstValue($sql) {
    $result = runQuery($sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result, MYSQLI_NUM);

        return $row[0];
    }

    return FALSE;
}

function runQueryGetAllFirstValues($sql) {
    $result = runQuery($sql);
    $arr = array();
    while ($row = mysqli_fetch_array($result)) {
        $arr[] = $row[0];
    }

    return $arr;
}

function runQueryGetHash($sql) {
    $result = runQuery($sql);
    $arr = array();
    while ($row = mysqli_fetch_array($result)) {
        $arr[$row[0]] = $row[1];
    }

    return $arr;
}

function lastInsertedId() {
    global $DB_connect;

    return (int)$DB_connect->insert_id;
}

//gui widgets
function option($value, $name, $selected = FALSE) {
    $out = "<option value=\"" . str_replace("\"", "", $value) . "\"";
    if (str_replace("\"", "", $value) == str_replace("\"", "", $selected)) {
        $out .= " selected='selected'";
    }
    $out .= ">" . htmlentities($name) . "</option>";

    echo $out;
}

function radioValue($value, $selectedValue) {
    if ($value == $selectedValue) {
        return "value='$value' checked='checked' ";
    } else {
        return "value='$value'";
    }
}

//post helpers
/*function checkboxArray($prefix) {
    $bar = array();
    foreach ($_POST as $key => $value) {
        if (substr($key, 0, strlen($prefix)) == $prefix) {
            $bar[] = substr($key, strlen($prefix));
        }
    }

    return $bar;
}*/

//misc helpers
function zeroIfBlank($foo) {
    if ($foo == "") {
        $foo = 0;
    }

    return $foo;
}

function nullIfBlank($foo) {
    if ($foo == "") {
        $foo = "null";
    }

    return $foo;
}

//session
function isAdmin() {
    if (isset($_SESSION['session_email']) && in_array($_SESSION['session_email'], User::ADMIN_EMAILS)) {
        return TRUE;
    }
    return FALSE;
}


function alt($a, $b) {
    global $alt;

    if ($alt == $a) {
        $alt = $b;
    } else {
        $alt = $a;
    }

    return $alt;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function yn($foo) {
    if (!$foo) {
        return "n";
    }
    if ($foo == "y" || $foo == "Y" || $foo == 1 || $foo == TRUE) {
        return "y";
    } else {
        return "n";
    }
}

function redirect($foo) {
    header("location: " . $foo);
    exit();
}

function seoFriendlyURLs($content_id, $type = "d", $url = '', $title = '') {
    $url = trim($url ?? '');
    $title = trim($title??'');

    #get the url from the database if none is supplied
    if (empty($url)) {
        $url = preg_replace("/[^A-Za-z0-9_]/", "-", $title);
    } else {
        $url = preg_replace("/[^A-Za-z0-9_]/", "-", $url);
    }

    $url = preg_replace("/(\-){2,}/", "-", $url);
    $url = substr($url, 0, 60);

    if (substr($url, -1) == '-') {
        $url = substr($url, 0, -1);
    }

    return strtolower($url . "-" . $type . $content_id);
}

function printArray($array) {
    echo '<div style="color: black; background: white"><pre>' . print_r($array) . '</pre></div>';
}

function writeLog($foo, $location = 'default.log') {
    if (is_array($foo) || is_object($foo)) {
        $foo = print_r($foo, TRUE);
    }

    $f = fopen(LOG_DIR . "/" . $location, 'a+');

    fputs($f, date('Y m d:H i s') . ' - ' . $foo . "\n");
    fclose($f);
}

function raiseError($error_text, $fieldname = "") {
    if (!isset($GLOBALS["errors"])) {
        $GLOBALS["errors"] = array();
    }

    $GLOBALS["errors"][$fieldname] = $error_text;
}

function hasErrors() {
    if (isset($GLOBALS["errors"])) {
        return $GLOBALS["errors"];
    }

    return FALSE;
}

function getErrors() {
    if (isset($GLOBALS["errors"])) {
        return $GLOBALS["errors"];
    }

    return array();
}

function fieldHasError($fieldname) {
    if (!isset($GLOBALS["errors"])) {
        return FALSE;
    }

    if (isset($GLOBALS["errors"][$fieldname])) {
        return $GLOBALS["errors"][$fieldname];
    } else {
        return FALSE;
    }
}

/**
 * Checks if the PHP script was called from the command line.
 * [jg] used in system tasks to make sure that they only run from the commandline and not a browser.
 *
 * @return bool
 */
function is_cli() {
    return (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));
}

function debugPrint($array) {
    ArrayHelper::printNice($array);
}

function debugPrintAndDie($array) {
    ArrayHelper::printNice($array);
    die();
}
