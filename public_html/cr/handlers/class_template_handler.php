<?php

class TemplateHandler {
    static protected $_template;
    static protected $_page_title;
    static protected $_page_description;
    static protected $_side_main_menu;
    static protected $_side_sub_menu;

    static protected $_table_caption_text = '';
    static protected $_table_dropdown_options = array();

    static protected $_main_view_paths = array();

    /** private constructor not instanciated */
    protected function __construct() {
    }

    public static function setMainView($main_view_path) {
        self::setViews($main_view_path);
    }

    /**
     * Expects multiple arguments
     **/
    public static function setViews() {
        self::$_main_view_paths = array_merge(self::$_main_view_paths, func_get_args());
    }

    public static function getViews() {
        return self::$_main_view_paths;
    }

    public static function setPageTitle($title, $description = '') {
        self::$_page_title = trim($title);
        self::$_page_description = trim($description);
    }

    public static function getPageTitle() {
        return self::$_page_title;
    }

    public static function getPageDescription() {
        return self::$_page_description;
    }


    public static function echoTopMenuStyle() {
        return (in_array(self::$_page_action, func_get_args())) ? 'selected' : '';
    }

    public static function addTableCaptionDropDownOption($label, $link, $icon_class = 'icon-plus', $additional_attributes = '') {
        self::$_table_dropdown_options[] = array($label, $link, $icon_class, $additional_attributes);
    }

    public static function setTableCaptionText($text) {
        self::$_table_caption_text = trim($text);
    }

    public static function echoTableCaption() {
        ob_start();
        ?>
        <div class="navbar">
            <div class="navbar-inner">
                <?
                if (!empty(self::$_table_caption_text)) {
                    ?>
                    <h6><?= (self::$_table_caption_text) ?></h6>
                    <?
                }
                if (count(self::$_table_dropdown_options) > 0) {
                    ?>
                    <div class="nav pull-right">
                        <a data-toggle="dropdown" class="dropdown-toggle navbar-icon" href="#"><i class="icon-cogs"></i></a>
                        <ul class="dropdown-menu pull-right">
                            <?
                            foreach (self::$_table_dropdown_options as $row_option) {
                                $tmp_label = paramFromHash(0, $row_option);
                                $tmp_url = paramFromHash(1, $row_option);
                                $tmp_link_attributes = paramFromHash(3, $row_option);
                                echo '<li><a ' . $tmp_link_attributes . ' href="' . $tmp_url . '"><i class="' . $row_option[2] . '"></i>' . $tmp_label . '</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <?
                }
                ?>
            </div>
        </div>
        <?
        $output = ob_get_contents();
        ob_end_clean();
        echo $output;
    }

    public static function setSideMenu($main_menu, $sub_menu = '') {
        self::$_side_main_menu = $main_menu;
        self::$_side_sub_menu = $sub_menu;
    }

    public static function echoMenu() {
        $sidebar_menu = array();
        $sidebar_menu[] = array('label' => 'Dashboard', 'url' => APP_URL . 'dashboard', 'icon' => 'icon-home');
        $sidebar_menu[] = array('label' => 'Listings', 'url' => '#', 'icon' => 'icon-sitemap', 'submenu' => array(array('label' => 'Current listings', 'url' => APP_URL . 'listing/current_list'), array('label' => 'All listings', 'url' => APP_URL . 'listing'), array('label' => 'Email items', 'url' => APP_URL . 'email_item'), array('label' => 'Report', 'url' => APP_URL . 'report'),));
        $sidebar_menu[] = array('label' => 'Reports', 'url' => '#', 'icon' => 'icon-file', 'submenu' => array(array('label' => 'Stats', 'url' => APP_URL . 'report_stats'), array('label' => 'Stats (monthly)', 'url' => APP_URL . 'report_stats/monthly'), array('label' => 'Contacts', 'url' => APP_URL . 'report_contact'), array('label' => 'Requests', 'url' => APP_URL . 'report_request'),));
        $sidebar_menu[] = array('label' => 'Account', 'url' => '#', 'icon' => 'icon-user', 'submenu' => array(array('label' => 'Change password', 'url' => APP_URL . 'user/change_password'),));
        $sidebar_menu[] = array('label' => 'Admin', 'url' => '#', 'icon' => 'icon-list', 'submenu' => array(
            array('label' => 'Users', 'url' => APP_URL . 'user'),
            array('label' => 'Email templates', 'url' => APP_URL . 'email_template'),
            array('label' => 'Email tracker', 'url' => APP_URL . 'email_tracker'),
            array('label' => 'MailChimp', 'url' => APP_URL . 'mail_chimp'),
            array('label' => 'District', 'url' => APP_URL . 'district'),
        ));
        $sidebar_menu[] = array('label' => 'Logout', 'url' => APP_URL . "auth/logout", 'icon' => 'icon-remove');

        foreach ($sidebar_menu as $row_menu) {
            $sub_menus = paramFromHash('submenu', $row_menu, array());
            $count = (int)count($sub_menus);
            $icon = paramFromHash('icon', $row_menu, 'icon-th');
            $url = $row_menu['url'];
            $main_label = $row_menu['label'];
            $has_submenu = ($count > 0);
            $li_style = ($main_label == self::$_side_main_menu) ? 'active' : '';
            $a_id = ($main_label == self::$_side_main_menu) ? 'current' : '';
            if ($has_submenu) {
                echo '<li class="' . $li_style . '"><a href="#" title="" class="expand" id="' . $a_id . '"><i class="' . $icon . '"></i>' . $main_label . '<strong>' . $count . '</strong></a>';
                echo '<ul>';
                foreach ($sub_menus as $row_submenu) {
                    $row_sub_label = $row_submenu['label'];
                    $row_sub_url = $row_submenu['url'];
                    $sub_li_style = ($row_sub_label == self::$_side_sub_menu) ? 'current' : '';
                    echo '<li><a href="' . $row_sub_url . '" title="' . $row_sub_label . '" class="' . $sub_li_style . '">' . $row_sub_label . '</a></li>';
                }
                echo '</ul>';
                echo '</li>';
            } else {
                echo '<li class="' . $li_style . '"><a href="' . $url . '" title="' . $main_label . '"><i class="' . $icon . '"></i>' . $main_label . '</a></li>';
            }
        }
    }

    public static function echoSaveAndResetButton($label = 'Submit', $show_reset = TRUE, $align_right = TRUE) {
        $style = ($align_right) ? 'align-right' : '';
        $output = '<div class="form-actions ' . $style . '"><button class="btn btn-info" type="submit">' . $label . '</button>&nbsp;';
        if ($show_reset) {
            $output .= '<button class="btn" type="reset">Cancel</button>';
        }
        $output .= '</div>';
        echo $output;
    }
}
