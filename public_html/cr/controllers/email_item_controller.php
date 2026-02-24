<?php
class EmailItemController extends _Controller {

    private static function _getBaseSQL() {
        $sql = "SELECT l.*,
                DATEDIFF(NOW(),listing_date) dd
                FROM listing l
                WHERE listing_date >= date_sub(CURDATE(), INTERVAL 208 DAY) 
                AND has_image = 'y' 
                AND listing_type = 'free' 
                AND listing_status IN ('available', 'reserved') ";

        return $sql;
    }

    public function index() {
       $sql = self::_getBaseSQL() . "
            ORDER BY listing_date DESC, visits DESC";
		$items = runQueryGetAll($sql);

        BreadcrumbHelper::addBreadcrumbs("Email items");

        TemplateHandler::setSideMenu("Listings", "Email items");
        TemplateHandler::setPageTitle("Email items");
        TemplateHandler::setMainView("views/email_item/list.php");

        include("templates/standard.php");
    }

    public function getHtml() {
        $listing_ids = paramFromPost('listing_ids', array(0));

        $sql = self::_getBaseSQL() . " 
                AND listing_id IN (" . arrayToSQLIn($listing_ids) . ") 
                ORDER BY l.visits asc";
        $items = runQueryGetAll($sql);

        require_once('views/email_item/html.php');
    }
}

