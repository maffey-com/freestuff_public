<?
class SearchController extends _Controller {
    public function index() {
        self::loginRequiredAndRedirect();

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('saved_search');
        PageHelper::setViews("views/search/list_saved_searches.php");

        BreadcrumbHelper::addBreadcrumbs('My account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Saved Searches');

        PageHelper::setMinifyPageCssName('search');

        $saved_searchs = SavedSearch::mySavedSearches();
        include("templates/main_layout.php");
    }

    public function search() {
        $search_string = paramFromGet("q");
        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type','free');

        $sql = "SELECT *, 0 crow_flies_dist 
                FROM listing l 
                JOIN user u ON l.user_id = u.user_id 
                WHERE l.listing_status IN ('available','reserved')";
        $sql .= empty($search_string) ? "" : " AND (MATCH(title, description) AGAINST ( " . quoteSQL($search_string) . ") OR listing_id = " . quoteSQL(preg_replace("/[^0-9]/","",$search_string)) . ")";
        if ($listing_filter->listing_type != 'all') {
           // $sql .= " AND l.listing_type =" . quoteSQL($listing_filter->listing_type);
        }

        $listings = new DataWindowHelper("browse", $sql, "crow_flies_dist", "asc", 10);
        $listings->run();
        $paging = $listings->getPaging();

        PageHelper::setRssLink(APP_URL . "rss_feed?search_string=" . $search_string);

        TemplateHandler::setSearchText($search_string);
        PageHelper::setViews('views/search/banner.php', "views/search/search_results.php");

        BreadcrumbHelper::addBreadcrumbs('Search');
        BreadcrumbHelper::addBreadcrumbs($search_string);

        include("templates/main_layout.php");
    }

    public function save($search_string) {
        self::loginRequiredAndRedirect();

        $saved_search = new SavedSearch();
        $saved_search->search_string = $search_string;
        $saved_search->setMyDefaults();
        $saved_search->save();

        redirect(APP_URL . 'search');
    }

    public function edit($search_id) {
        self::loginRequiredAndRedirect();

        $search_id = (int)$search_id;

        $saved_search = SavedSearch::instanceFromId($search_id);
        if ($saved_search->isMine()) {
            TemplateHandler::setSelectedMainTab('my_account');
            TemplateHandler::setSelectedDashboardMenu('saved_search');
            PageHelper::setViews("views/search/edit_form.php");

            PageHelper::setMinifyPageCssName('search');

            BreadcrumbHelper::addBreadcrumbs('My account', APP_URL . 'my_freestuff');
            BreadcrumbHelper::addBreadcrumbs('Saved Searches', APP_URL . 'search');
            BreadcrumbHelper::addBreadcrumbs($saved_search->search_string);
            BreadcrumbHelper::addBreadcrumbs('Edit');

            include("templates/main_layout.php");

        } else {
            MessageHelper::setSessionErrorMessage("You do not have permission to edit this search record.");
            redirect(APP_URL . "search");
        }
    }

    public function update($search_id) {
        self::loginRequiredAndEchoJsonError();

        $search_id = (int)$search_id;

        $saved_search = SavedSearch::instanceFromId($search_id);
        if ($saved_search->isMine()) {
            $saved_search->buildFromPost();
            $saved_search->update();
        } else {
            raiseError("You do not have permission to edit this search record.");
        }

        if (hasErrors()) {
            echo json_encode(getErrors());
        } else {
            echo '1';
        }
        exit();
    }


    public function delete($search_id) {
        self::loginRequiredAndEchoErrorMessage();

        $search_id = (int)$search_id;

        $saved_search = SavedSearch::instanceFromId($search_id);
        if ($saved_search->isMine()) {
            $saved_search->expire();
        } else {
            raiseError("You do not have permission to delete this search record.");
        }

        if (hasErrors()) {
            MessageHelper::setSessionErrorMessage('Cannot delete Search');
        } else {
            MessageHelper::setSessionSuccessMessage('Search Deleted');
        }

        exit();
    }
}
