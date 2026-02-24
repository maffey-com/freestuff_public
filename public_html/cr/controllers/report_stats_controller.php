<?php
class ReportStatsController extends _Controller {

    public function index() {
        $today = "'" . date("Y-m-d") . "'";
        $limit = "'" . date("Y-m-d",strtotime("-20 days")) . "'";

        $sql = "SELECT COUNT(profile_mark_id) c, sum(if(platform = 'W',1,0)) web, sum(if(platform = 'A',1,0)) mobile,
                DATE_FORMAT(date, '%Y-%m-%d') date 
                FROM listing_profile_mark 
                WHERE date > $limit 
                GROUP BY date_format(date, '%Y-%m-%d') 
                ORDER BY date_format(date, '%Y-%m-%d') DESC";
        $listing_views = runQueryGetAll($sql);



        $sql = "SELECT COUNT(listing_id) c, 
                SUM(IF(listing_type = 'free', 1, 0)) free,  
                SUM(IF(listing_type = 'wanted', 1, 0)) wanted,  
                DATE_FORMAT(listing_date, '%Y-%m-%d') date 
                FROM listing 
                WHERE listing_date > $limit 
                GROUP BY DATE_FORMAT(listing_date, '%Y-%m-%d') 
                ORDER BY DATE_FORMAT(listing_date, '%Y-%m-%d') DESC";
        $new_listings = runQueryGetAll($sql);

        $sql = "SELECT COUNT(user_id) c,  
                DATE_FORMAT(created_on,'%Y-%m-%d') date 
                FROM user 
                WHERE created_on > $limit 
                GROUP BY DATE_FORMAT(created_on, '%Y-%m-%d') 
                ORDER BY DATE_FORMAT(created_on, '%Y-%m-%d') DESC";
        $new_users = runQueryGetAll($sql);

        $sql = "SELECT COUNT(user_id) c,  
                DATE_FORMAT(mobile_validated,'%Y-%m-%d') date 
                FROM user 
                WHERE mobile_validated > $limit 
                GROUP BY DATE_FORMAT(mobile_validated, '%Y-%m-%d') 
                ORDER BY DATE_FORMAT(mobile_validated, '%Y-%m-%d') ";
        $mobile_validated = runQueryGetAll($sql);

        $sql = "SELECT COUNT(user_id) c,  
                DATE_FORMAT(email_validated,'%Y-%m-%d') date 
                FROM user 
                WHERE email_validated > $limit 
                GROUP BY DATE_FORMAT(email_validated, '%Y-%m-%d') 
                ORDER BY DATE_FORMAT(email_validated, '%Y-%m-%d') ";
        $email_validated = runQueryGetAll($sql);

        $sql = "SELECT count(*) 
                FROM user 
                WHERE mobile_validated IS NOT NULL";
        $no_of_users = runQueryGetFirstValue($sql);

        $no_current_listings = Listing::currentListingCounter();

        $report = array();
        foreach ($listing_views as $row) {
            $report[$row["date"]]["listing_views"] = $row["c"];
            $report[$row["date"]]["mobile_views"] = $row["mobile"];
            $report[$row["date"]]["web_views"] = $row["web"];
        }


        foreach ($new_listings as $row) {
            $report[$row["date"]]["new_listings"]['free'] = $row["free"];
            $report[$row["date"]]["new_listings"]['wanted'] = $row["wanted"];
            $report[$row["date"]]["new_listings"]['total'] = $row["wanted"] + $row["free"];
        }

        foreach ($new_users as $row) {
            $report[$row["date"]]["new_users"] = $row["c"];
        }

        foreach ($mobile_validated as $row) {
            $report[$row["date"]]["mobile_validated"] = $row["c"];
        }

        foreach ($email_validated as $row) {
            $report[$row["date"]]["email_validated"] = $row["c"];
        }


        krsort($report);

        BreadcrumbHelper::addBreadcrumbs("Reports");
        BreadcrumbHelper::addBreadcrumbs("Stats");

        TemplateHandler::setSideMenu("Reports", "Stats");
        TemplateHandler::setPageTitle("Stats");
        TemplateHandler::setMainView("views/report_stats/home.php");

        include("templates/standard.php");
    }

    public function monthly() {
        $sql = "SELECT * 
                FROM stats_monthly 
                ORDER BY month DESC";
        $result = runQueryGetAll($sql);

        $month_stats = array();
        foreach ($result as $row) {
            $month_stats[$row["month"]] = $row;
        }

        $start_month = "201101";

        BreadcrumbHelper::addBreadcrumbs("Reports");
        BreadcrumbHelper::addBreadcrumbs("Stats (monthly)");

        TemplateHandler::setSideMenu("Reports", "Stats (monthly)");
        TemplateHandler::setPageTitle("Stats (monthly)");
        TemplateHandler::setMainView("views/report_stats/monthly.php");

        $display_month = date('Ym');

        include("templates/standard.php");
    }

    public function buildMonth($month) {
        $begining_of_month_timestamp = strtotime(substr($month, 0, 4) . "-" . substr($month, 4, 2) . "-01");
        $begining_of_month = date("Y-m-d G:i", $begining_of_month_timestamp);
        $end_of_month = date("Y-m-d G:i", strtotime("+ 1 month", $begining_of_month_timestamp));

        $sql = "SELECT COUNT(profile_mark_id) 
                FROM listing_profile_mark 
                WHERE date >= '$begining_of_month' 
                AND date < '$end_of_month'";
        $listing_views = runQueryGetFirstValue($sql);

        $sql = "SELECT COUNT(*) 
                FROM category_profile_mark 
                WHERE date >= '$begining_of_month' 
                AND date < '$end_of_month'";
        $category_views = runQueryGetFirstValue($sql);

        $sql = "SELECT SUM(IF(listing_type = 'free', 1, 0)) free,  
                SUM(IF(listing_type = 'wanted', 1, 0)) wanted 
                FROM listing 
                WHERE listing_date >= '$begining_of_month' 
                AND listing_date < '$end_of_month'";
        $new_listings = runQueryGetFirstRow($sql);

        $sql = "SELECT COUNT(user_id) 
                FROM user 
                WHERE created_on >= '$begining_of_month' 
                AND created_on < '$end_of_month'";
        $new_users = runQueryGetFirstValue($sql);

        $sql = "SELECT COUNT(user_id) 
                FROM user 
                WHERE mobile_validated >= '$begining_of_month' 
                AND created_on < '$end_of_month'";
        $mobile_validations = runQueryGetFirstValue($sql);

        $sql = "SELECT COUNT(request_id) 
                FROM listing_request 
                WHERE request_timestamp >= '$begining_of_month' 
                AND request_timestamp < '$end_of_month'";
        $contacts = runQueryGetFirstValue($sql);

        $sql = "INSERT INTO stats_monthly SET
                date_updated = NOW(),
                month = " . quoteSQL($month);
        $sql .= ", listing_views = " . quoteSQL($listing_views);
        $sql .= ", category_views = " . quoteSQL($category_views);
        $sql .= ", new_free_listings = " . quoteSQL($new_listings["free"],false);
        $sql .= ", new_wanted_listings = " . quoteSQL($new_listings["wanted"],false);
        $sql .= ", new_users = " . quoteSQL($new_users);
        $sql .= ", mobile_validations = " . quoteSQL($mobile_validations);
        $sql .= ", contacts = " . quoteSQL($contacts);
        $sql .= " ON DUPLICATE KEY UPDATE
                date_updated = NOW(),
                listing_views = " . quoteSQL($listing_views) .
                ", category_views = " . quoteSQL($category_views) .
                ", new_free_listings = " . quoteSQL($new_listings["free"],false) .
                ", new_wanted_listings = " . quoteSQL($new_listings["wanted"],false) .
                ", new_users = " . quoteSQL($new_users) .
                ", mobile_validations = " . quoteSQL($mobile_validations) .
                ", contacts = " . quoteSQL($contacts);

        runQuery($sql);

        MessageHelper::setSessionSuccessMessage('Monthly stats have been re-calculated for ' . $month);
        redirect(APP_URL . 'report_stats/monthly');
    }

    public function truncate() {
        $sql = "delete from listing_profile_mark where date < DATE_SUB(NOW(), INTERVAL 12 MONTH)";
        runQuery($sql);

        $sql = "delete from category_profile_mark where date < DATE_SUB(NOW(), INTERVAL 12 MONTH)";
        runQuery($sql);

        MessageHelper::setSessionSuccessMessage('Monthly stats have been truncated');
        redirect(APP_URL . 'report_stats/monthly');
    }
}

