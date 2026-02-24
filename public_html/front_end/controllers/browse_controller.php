<?php
class BrowseController extends _Controller  {

    public function index($region_name) {
        $region_name = trim($region_name);

        if (!$region_name) {
            redirect("/");
        }

        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type','free');

        $ip = paramFromHash('REMOTE_ADDR', $_SERVER);

        $district_ids = District::districtIdsForRegion($region_name);
        if (!sizeof($district_ids)) {
            redirect("/");
        }

        $sql = "SELECT l.*,u.firstname 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('available','reserved') 
                AND l.district_id in " . quoteIN($district_ids);
        if ($listing_filter->listing_type != 'all') {
        //    $sql .= " AND listing_type =" . quoteSQL($listing_filter->listing_type);
        }

        $listings = new DataWindowHelper("browse", $sql, "listing_date", "desc", 20);
        $listings->run();
        $paging = $listings->getPaging();

        //profile mark category
        $sql = "INSERT category_profile_mark SET 
                category = " . quoteSQL($region_name) . ", 
                user_id = " . quoteSQL(SESSION_USER_ID) . ", 
                date = NOW(), 
                ip_address = " . quoteSQL($ip);
        runQuery($sql);

        PageHelper::setMetaTitle('Browse ' . $region_name);
        PageHelper::setMetaDescription('Browse ' . $region_name);
        PageHelper::setRssLink(APP_URL . 'rss_feed?category=' . $region_name);

        TemplateHandler::setBrowseCategoryName($region_name);
        PageHelper::setViews('views/search/banner.php', "views/search/search_results.php");

        BreadcrumbHelper::addBreadcrumbs("Browse Listings");
        BreadcrumbHelper::addBreadcrumbs($region_name);

        include("templates/main_layout.php");
    }

    public function byRegion($region) {
        $this->index($region);
    }

    public function save($region) {
        self::loginRequiredAndRedirect();

        SavedSearch::getRegionNotifications($region);

        redirect(APP_URL . 'search');
    }

    public function toggleWanted() {
        $show_wanted = paramFromGet('show_wanted');

        if (empty($show_wanted)) {
            $_SESSION["listing_filter"] = array('free');
        } else {
            $_SESSION["listing_filter"] = explode(',', $show_wanted);
        }
        exit();
    }
}
