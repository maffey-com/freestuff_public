<?php
class MyFreestuffController extends _Controller {
    public function index() {
        self::loginRequiredAndRedirect();

        /*
         * Current listings
         */
        $sql = "SELECT * 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('available', 'reserved')
                AND u.user_id = " . SESSION_USER_ID . " 
                ORDER BY listing_date DESC;";//" LIMIT 4";
        $latest_listings = runQueryGetAll($sql);

        $sql = "SELECT COUNT(l.listing_id) 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('available', 'reserved')
                AND u.user_id = " . SESSION_USER_ID;
        $latest_count = runQueryGetFirstValue($sql);


		$sql = "SELECT l.* 
                FROM listing l
                JOIN listing_request r on l.listing_id = r.listing_id
                WHERE r.user_id = " . SESSION_USER_ID . "
                AND DATEDIFF(CURDATE(), r.request_timestamp) <= 30
                ORDER BY r.request_timestamp DESC LIMIT 4";
        $requested_listings = runQueryGetAll($sql);

        $sql = "SELECT COUNT(listing_id) 
                FROM listing 
                WHERE listing_id IN (
                                    SELECT DISTINCT listing_id 
                                    FROM listing_request 
                                    WHERE user_id = " . SESSION_USER_ID . ")";
        $requested_count = runQueryGetFirstValue($sql);

        /*
         * Previous listings
         */
        $sql = "SELECT * 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('gone', 'expired') 
                AND u.user_id = " . SESSION_USER_ID . " 
                AND DATEDIFF(CURDATE(), listing_date) <= 30
                ORDER BY listing_date DESC LIMIT 4";
        $previous_listings = runQueryGetAll($sql);

        $sql = "SELECT COUNT(l.listing_id) 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('gone', 'expired') 
                AND u.user_id = " . SESSION_USER_ID;
        $previous_count = runQueryGetFirstValue($sql);

        $user = User::instanceFromId(SESSION_USER_ID);
        $count_saved_searaches = count(SavedSearch::mySavedSearches());

        $blocked_users = UserBlocked::getBlockedForUser(SESSION_USER_ID);
        
        #$alerts = Message::countNewReadConversations(SESSION_USER_ID);

        BreadcrumbHelper::addBreadcrumbs("My Account");

        PageHelper::setMinifyPageCssName('my_freestuff');
        PageHelper::addPageStylesheetFile('css/dashboard.css');
        PageHelper::setViews("views/my_freestuff/dashboard.php");

        TemplateHandler::setSelectedMainTab('my_account');

        include("templates/main_layout.php");
    }

    public function watchlist() {
        $this->listings('watchlist');
    }

    public function listings($which) {
        $which = $which ? $which: 'current';
        self::loginRequiredAndRedirect();

        $filter = new FilterHelper('my_freestuff.' . $which);
        $filter->setDefault('age', 30);

        $filter_age = (int)$filter->age;
        $filter_age = in_array($filter_age, array(0, 30, 60, 90)) ? $filter_age : 30;

        switch ($which) {
            case 'current':
                $sql = "SELECT * 
                        FROM listing l 
                        JOIN user u on l.user_id = u.user_id 
                        WHERE l.listing_status IN ('available', 'reserved')
                        AND u.user_id = " . SESSION_USER_ID;

                $breadcrumb_label = 'Current listings';

                $title = "My Live Freestuff Listings";
                break;

            case 'watchlist':
                $sql = "SELECT * 
                        FROM listing 
                        WHERE listing_id IN (
                            SELECT DISTINCT listing_id 
                            FROM listing_request 
                            WHERE user_id = " . SESSION_USER_ID;
                            $sql .= empty($filter_age) ? '' : " AND DATEDIFF(CURDATE(), request_timestamp) <= " . (int)$filter_age;
                            $sql .= "
                            )";

                $breadcrumb_label = 'Requested Items';

                $title = "Freestuff I enquired about";
                break;

            case 'previous':
                $sql = "SELECT l.* 
                        FROM listing l 
                        JOIN user u on l.user_id = u.user_id 
                        WHERE l.listing_status IN ('gone', 'expired') 
                        AND u.user_id = " . SESSION_USER_ID;
                $sql .= empty($filter_age) ? '' : " AND DATEDIFF(CURDATE(), listing_date) <= " . (int)$filter_age;

                $breadcrumb_label = 'Previous Listings';
                $title = "My Previous Freestuff Listings";
                break;
        }

        $listings = new DataWindowHelper("browse", $sql, "listing_date", "desc", 12);
        $listings->run();
        $paging = $listings->getPaging();

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs($breadcrumb_label);

        if (($which != 'current') &&  (!empty($filter_age))) {
            BreadcrumbHelper::addBreadcrumbs('Last ' . $filter_age . ' days');
        }

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('listings_'.$which);
        PageHelper::setViews("views/my_freestuff/listings.php");
        PageHelper::addJsVar('current_url', 'my_freestuff/listings/' . $which);


        include("templates/main_layout.php");
    }
}
