<?php
class ViewController extends _Controller {
    public function index() {
        $listing_id = (int)paramFromGet('listing_id');
        $supplied_url = paramFromGet('supplied_url');

        $listing = Listing::instanceFromId($listing_id);

        if (empty($listing->listing_id) || (SESSION_USER_ID && !UserBlocked::canSeeListing(SESSION_USER_ID, $listing->user_id))) {
            $this->do404();
        }

        $tmp_title = preg_replace("/,([^\s])/", ", $1", $listing->title??'');
        $full_required_url = seoFriendlyURLs($listing_id, "listing", false, $tmp_title);

        $required_url = preg_replace("~\-listing\d*(?!.*\-listing\d*)~","",$full_required_url);

        if ($required_url != $supplied_url) {
            if (!str_starts_with($full_required_url, "/")) {
                $full_required_url = "/" . $full_required_url;
            }
            redirect($full_required_url);
        }

        // Redirects to home if listing is not found or user is blocked.


        if ($listing->user_id != SESSION_USER_ID) {
            Listing::profileMark($listing_id,'W');
        }


        PageHelper::setMetaTitle($listing->title);
        PageHelper::setMetaDescription($listing->title);

        PageHelper::setMinifyPageCssName('view');
        PageHelper::addPageStylesheetFile('css/listing_view.css');

        if (empty($listing->district_id)) {
            BreadcrumbHelper::addBreadcrumbs('Listings');
        } else {
            BreadcrumbHelper::addBreadcrumbs('Listings in ' . District::resolveRegionName($listing->district_id), APP_URL . 'browse/by-region/' . District::resolveRegionName($listing->district_id));
        }
        BreadcrumbHelper::addBreadcrumbs($listing->title);

        TemplateHandler::setSuppressIslandAds();
        TemplateHandler::setBrowseCategoryName(District::resolveRegionName($listing->district_id));
        PageHelper::setViews('views/view/banner.php', "views/view/item_description.php");

        if ($listing->isMyListing()) {
            PageHelper::setViews("views/view/list_requesters.php");
            PageHelper::addPageJavascriptAfterPageLoaded('views/message/list_conversations.js');
        }

        if ($listing->haveIRequestedThisItem()) {
            PageHelper::setViews("views/view/my_request.php");
            PageHelper::addPageJavascriptAfterPageLoaded('views/message/list_conversations.js');
        }


        include("templates/main_layout.php");
    }

}
