<?php
/**
 * Created by PhpStorm.
 * User: maggie
 * Date: 10/10/2016
 * Time: 5:06 PM
 */
class TemplateHandler {
    static protected $_search_text = '';
    static protected $_browse_category_name = '';

    static protected $_selected_main_tab = '';
    static protected $_selected_dashboard_menu = '';

	static protected $_main_view_paths = array();
    static protected $_suppress_island_ads = FALSE;

    protected function __construct() {}

    public static function getInstance() {
        static $output;

        if (is_null($output)) {
            $output = new self();
        }

        return $output;
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

	public static function  getViews() {
		return self::$_main_view_paths;
	}

    public static function setSelectedDashboardMenu($tab) {
        self::$_selected_dashboard_menu = $tab;
    }

    public static function getSelectedDashboardMenu() {
        return self::$_selected_dashboard_menu;
    }

    public static function setSelectedMainTab($tab) {
        self::$_selected_main_tab = $tab;
    }

    public static function getSelectedMainTab() {
        return self::$_selected_main_tab;
    }

    public static function setSuppressIslandAds() {
        self::$_suppress_island_ads = TRUE;
    }

    public static function getSuppressIslandAds() {
        return self::$_suppress_island_ads;
    }

    public static function setSearchText($text) {
        self::$_search_text = trim($text);
    }

    public static function getSearchText() {
        return self::$_search_text;
    }

    public static function setBrowseCategoryName($text) {
        self::$_browse_category_name = trim($text ?? '');
    }

    public static function getBrowseCategoryName() {
        return self::$_browse_category_name;
    }

    public static function echoSubTitle($page_title) {
        echo '<h4>' . $page_title . '</h4>';
    }

    public static function echoPageTitle($page_title, $blurb = '') {
        ?>
        <div id="" class="row fs-page-header">
            <div class="col">
                <h1><?=($page_title)?></h1>

                <?
                if (!empty($blurb)) {
                    ?>
                    <p><?=($blurb)?></p>
                    <?
                }
                ?>
            </div>
        </div>
        <?
    }

}