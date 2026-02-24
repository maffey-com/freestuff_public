<?php
class ReportRequestController extends _Controller {

    public function index() {
        $filter = new FilterHelper('report_request');

        $filter_listing_id = (int)$filter->listing_id;
        $filter_hours = $filter->hours;
        $filter_hours = empty($filter_listing_id) ? 10000 : 0;

        // Get listings
        $listing_ids = array();
        $sql = "SELECT l.listing_id, l.title, l.user_id, l.description, l.listing_date, 
                u.email, 
                COUNT(DISTINCT(lr.request_id)) as requests
                FROM listing l 
                JOIN listing_request lr ON lr.listing_id = l.listing_id 
                JOIN user u ON u.user_id = l.user_id ";
        if (empty($filter_listing_id)) {
            $sql .= " WHERE DATE_ADD(NOW(), INTERVAL - " . $filter_hours . " HOUR) < l . listing_date OR DATE_ADD(NOW(), INTERVAL - " . $filter_hours . " HOUR) < lr . request_timestamp";
        } else {
            $sql .= " WHERE l.listing_id = " . quoteSQL($filter_listing_id);
        }
        $sql .= " GROUP BY l.listing_id";

        $dw_listing = new DataWindowHelper("listings", $sql, "listing_date", "DESC");
        $dw_listing->run();

        BreadcrumbHelper::addBreadcrumbs("Reports");
        BreadcrumbHelper::addBreadcrumbs("Requests");

        TemplateHandler::setSideMenu("Reports", "Requests");
        TemplateHandler::setPageTitle("Requests");
        TemplateHandler::setMainView("views/report_request/list.php");

        include("templates/standard.php");
    }

    /** to allow user to go back from browsers */
    public function filterList() {
        $filter = new FilterHelper('report_request');

        redirect(APP_URL . 'report_request');
    }

    public function view($listing_id) {
        $listing_id = (int)$listing_id;

        $request_ids = array(0);
        $sql = "SELECT lr.request_id, lr.request_timestamp, lr.listing_id, lr.user_id, 
                u.email 
                FROM listing_request lr 
                LEFT JOIN user u ON u.user_id = lr.user_id 
                WHERE lr.listing_id = " . $listing_id;
        $requests = runQueryGetAll($sql);
        foreach ($requests as $request_list) {
            $request_id = $request_list['request_id'];

            $request_ids[] = $request_id;
        }

        // Get messages
        $sql = "SELECT m.*, 
                u.email 
                FROM message m 
                LEFT JOIN user u on u.user_id = m.sender_user_id 
                WHERE m.request_id IN (" . arrayToSQLIn($request_ids) . ")";
        $messages = runQueryGetStructured($sql, array("request_id"), array("message_id", "message", "timestamp", "sender_user_id", "email"));

        $listing = new Listing();
        $listing->retrieveFromID($listing_id);

        $lister = User::instanceFromId($listing->user_id);

        BreadcrumbHelper::addBreadcrumbs("Reports");
        BreadcrumbHelper::addBreadcrumbs("Requests", APP_URL . 'report_request');
        BreadcrumbHelper::addBreadcrumbs("View");

        TemplateHandler::setSideMenu("Reports", "Requests");
        TemplateHandler::setPageTitle("Requests view");
        TemplateHandler::setMainView("views/report_request/view.php");

        include("templates/standard.php");
    }
}

