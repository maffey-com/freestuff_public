<?php

//data windows expects to have access to the following $_REQUEST parameters
//  $_REQUEST["dw_pg"] -> current page for data window
// $_REQUEST["dw_sc"] -> sort column , column to sort buy
// $_REQUEST["dw_so"] -> sort order, asc or desc, asc by default

//$sql must not include order or limit clauses
class DataWindowHelper {
    var $sql;
    var $rows_per_page;
    var $max_pages;
    var $sort_column;
    var $sort_order;
    var $data;
    var $total_number_of_results;
    var $current_page;
    var $dw_name;
    var $sort_column_name;
    var $sort_order_name;


    function __construct($dw_name, $sql = "", $default_sort_column = "", $default_sort_order = 'asc', $rows_per_page = 20, $max_pages = 20) {
        $dw_name = preg_replace('/[^A-Za-z0-9]/', '', $dw_name);
        $this->dw_name = str_ireplace(' ','_',$dw_name);

        $this->sql = $sql;
        $this->rows_per_page = $rows_per_page;
        $this->max_pages = $max_pages;
        $this->current_page = (int)paramFromRequest("dw_pg_" . $dw_name, 1);
        $this->current_page = empty($this->current_page) ? 1 : $this->current_page;

        $this->sort_column = paramFromRequest("dw_sc_" . $dw_name, $default_sort_column);
        $this->sort_column = empty($this->sort_column) ? $default_sort_column : $this->sort_column;

        $this->sort_order = paramFromRequest("dw_so_" . $dw_name, $default_sort_order);
        $this->sort_order = empty($this->sort_order) ? $default_sort_order : $this->sort_order;

        $_SESSION["dw_so_" . $dw_name] = $this->sort_order;
        $_SESSION["dw_sc_" . $dw_name] = $this->sort_column;
        $_SESSION["dw_pg_" . $dw_name] = $this->current_page;
        $this->data = array();
    }

    function run() {
        if (empty($this->sql)) {
            $_GLOBALS["error"] = "no SQL set";
            return false;
        }

        //ensure $sql starts with SQL_CALC_FOUND_ROWS
        if (!stristr($this->sql, "SQL_CALC_FOUND_ROWS")) {
            $this->sql = stristr($this->sql, "select ");
            $this->sql = "select SQL_CALC_FOUND_ROWS " . substr($this->sql, 7);
        }
        $sql = $this->sql;
        //order by
        if (!empty($this->sort_column)) {
            $sql .= " order by " . $this->sort_column . " " . $this->sort_order;
        }

        //limit page sizes
        $offset = ($this->current_page - 1) * $this->rows_per_page;
        $sql .= " LIMIT $offset," . $this->rows_per_page;

        $this->data = runQueryGetAll($sql);

        $this->total_number_of_results = runQueryGetFirstValue("select found_rows()");
    }

    public function sortableColumnHeading($db_column_name, $heading) {
        if ($this->sort_column == $db_column_name) {
            if ($this->sort_order == "desc") {
                $url = $this->_modifyCurrentURL(array("dw_sc_" . $this->dw_name => $db_column_name, "dw_so_" . $this->dw_name => "asc"));
                return "<a href='$url' class='sortable_desc'>$heading</a>";
            } else {
                $url = $this->_modifyCurrentURL(array("dw_sc_" . $this->dw_name => $db_column_name, "dw_so_" . $this->dw_name => "desc"));
                return "<a href='$url' class='sortable_asc'>$heading</a>";
            }
        } else {
            $url = $this->_modifyCurrentURL(array("dw_sc_" . $this->dw_name => $db_column_name, "dw_so_" . $this->dw_name => "asc"));
            return "<a href='$url' class='sortable'>$heading</a>";
        }
    }


    public function quickAndDirtyDisplay() {
        $data = $this->data;
        if (!sizeof($data)) {
            return false;
        }
        $keys = array_keys($data[0]);
        echo "<table class='listTable'>";
        echo "<tr>";
        foreach ($keys as $column) {
            echo "<th>" . $this->sortableColumnHeading($column, $column) . "</th>";
        }
        echo "</tr>";

        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";

        echo $this->displayPaging();

    }

    public function displayPaging() {
        $start_index = (($this->current_page - 1) * $this->rows_per_page) + 1;
        $end_index = (($this->current_page - 1) * $this->rows_per_page) + $this->rows_per_page;
        $end_index = ($end_index > $this->total_number_of_results) ? $this->total_number_of_results : $end_index;
        $start_index = ($start_index > $this->total_number_of_results) ? 0 : $start_index;

        #rows_per_page
        $output = '<div class="datatable-footer">';
        $output .= '<div class="dataTables_info" id="data-table_info">Showing ' . $start_index . ' to ' . $end_index . ' of ' . $this->total_number_of_results . ' entries</div>';
        $output .= '<div class="dataTables_paginate paging_full_numbers" id="data-table_paginate">';
        $output .= '<span>';
        $pages = $this->getPaging();
        $output .= '<a class="paginate_button '.($this->current_page === 1?'paginate_button_disabled':'').'" href="'.$pages['first'].'" aria-label="First">
                        <span>First</span>
                    </a>
                    <a class="paginate_button '.($this->current_page === 1?'paginate_button_disabled':'').'" href="'.$pages['prev'].'" aria-label="Previous">
                        <span>Previous</span>
                    </a>';
        foreach ($pages['links'] as $page => $link) {
            if ($this->current_page == $page) {
                $output .= '<a class="paginate_active" tabindex="0" href="#">' . $page . '</a>';
            } else {
                $output .= '<a class="paginate_button" tabindex="0" href="' . $link . '">' . $page . '</a>';
            }
        }
        $output .= '<a class="paginate_button '.($this->current_page == $pages['total_pages']?'paginate_button_disabled':'').'" href="'.$pages['next'].'" aria-label="Next">
                        <span>Next</span>
                    </a>
                    <a class="paginate_button '.($this->current_page == $pages['total_pages']?'paginate_button_disabled':'').'" href="'.$pages['last'].'" aria-label="Last">
                        <span>Last</span>
                    </a>';
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    public function getPaging() {
        //do paging linking, retuns an array
        $link = $this->_modifyCurrentURL(array("dw_pg_{$this->dw_name}" => ''));

        $total_pages = ceil($this->total_number_of_results / $this->rows_per_page);

        $start_page = round($this->current_page - ($this->max_pages / 2), 0);
        $end_page = round($this->current_page + ($this->max_pages / 2), 0);

        if ($this->current_page < ($this->max_pages / 2)) {
            $end_page += ceil(($this->max_pages / 2) - $this->current_page);
        }
        if ($total_pages - $this->current_page < ($this->max_pages / 2)) {
            $start_page -= ceil(($this->max_pages / 2) - ($total_pages - $this->current_page));
        }

        $end_page = ($end_page >= $total_pages ? $total_pages : $end_page);
        $start_page = ($start_page < 1 ? 1 : $start_page);

        $out = array();
        $delimiter = (strpos($link, '?') === FALSE) ? '?' : '&';
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $this->current_page) {
                $out[$i] = "";
            } else {

                $out[$i] = $link . $delimiter . "dw_pg_" . $this->dw_name . "=" . $i;
            }
        }

        $first_link = $link . $delimiter . "dw_pg_" . $this->dw_name . "=1";
        $prev_link = $this->current_page!==1?$link . $delimiter . "dw_pg_" . $this->dw_name . "=" . ($this->current_page-1):'';
        $next_link = $this->current_page!==$total_pages?$link . $delimiter . "dw_pg_" . $this->dw_name . "=" . ($this->current_page+1):'';
        $last_link = $link . $delimiter . "dw_pg_" . $this->dw_name . "=" . $total_pages;

        return array('links'=>$out, 'total_pages'=>$total_pages, 'next'=>$next_link, 'prev'=>$prev_link, 'first'=>$first_link, 'last'=>$last_link);
    }

    private function _modifyCurrentURL(array $parameters_to_include = array()) {
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

    public static function popupBoxLinks() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".sortable, .sortable_asc, .sortable_desc, .list_paging").click(function (e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    $("#popup_box_content").load(url);
                })
            })
        </script>
        <?
    }

    public static function ajaxPanelLinks($panel_name) {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".sortable, .sortable_asc, .sortable_desc, .list_paging").click(function (e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    $("<?=($panel_name)?>").load(url);
                })
            })
        </script>
        <?
    }
}
