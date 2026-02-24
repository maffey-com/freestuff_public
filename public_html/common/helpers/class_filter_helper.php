<?
//class designed to help with filtering records
class FilterHelper {
    private $filter_list = [];

    /**
     * Hover up everything prefixed with 'filter_'
     *
     * @param bool $sticky_handle
     */
    public function __construct($sticky_handle = FALSE) {
        $filter_in_request = FALSE;
        foreach ($_REQUEST as $key => $value) {
            if (substr($key, 0, 7) == "filter_") {
                $name              = substr($key, 7);
                $this->filter_list[$name] = $value;
                $filter_in_request = TRUE;
            }
        }

        // Do we have a session handle?
        if ($sticky_handle) {
            // Sure do, lets deal with it
            if ($filter_in_request) {
                // We also have a filter value in the $_REQUEST data, we better save the current filter to the session.
                $_SESSION["filter_sticky_handle_".$sticky_handle] = $this->filter_list;
            } else {
                // No filters found in the $_REQUEST, lets pull out the previously saved filter data (if there is any).
                if ($session_filter = paramFromSession("filter_sticky_handle_".$sticky_handle)) {
                    // Convert the saved filter options as propoerties on this FilterHelper instance.
                    foreach ($session_filter as $filter_name => $filter_value) {
                        $this->filter_list[$filter_name] = $filter_value;
                    }
                }
            }
        }
    }

    public function __get($name) {
        return $this->filter_list[$name] ?? false;
    }

    public function __set($name, $value) : void {
        $this->filter_list[$name] = $value;
    }

    public function setDefault($filter_name, $filter_value) {
        // Is this filter option already in our list?
        if (!isset($this->filter_list[$filter_name])) {
            // No, lets set it to the default value.
            $this->filter_list[$filter_name] = $filter_value;
        }
    }

    public function modifyCurrentURL(array $parameters_to_include = array()) {
        //creates a link, relative to the current page, replacing
        $out = paramFromHash('REQUEST_URI', $_SERVER);

        $delimiter = '';
        $append_query_string = '';

        if (strpos($out, "?")) {
            $out = substr($out, 0, strpos($out, "?"));
        }

        foreach ($_GET as $key => $value) {
            if (array_key_exists($key, $parameters_to_include)) {
                if (!empty($parameters_to_include[$key])) {
                    $append_query_string .= $delimiter . $key . "=" . $parameters_to_include[$key];
                    $delimiter = '&';
                }

                unset($parameters_to_include[$key]);
            } else {
                $append_query_string .= $delimiter . $key . "=" . $value;
                $delimiter = '&';
            }
        }

        foreach ($parameters_to_include as $key => $value) {
            if (!empty($value)) {
                $append_query_string .= $delimiter . $key . "=" . $value;
                $delimiter = '&';
            }
        }

        if (!empty($append_query_string)) {
            $out .= '?' . $append_query_string;
        }

        return $out;
    }
}
