<?php
class EmailTrackerController extends _Controller {

    public function index() {
        $filter = new FilterHelper('email_tracker');

        $sql = "SELECT * 
                FROM email_tracker
                WHERE date_sent > DATE_SUB(NOW(), INTERVAL 24 MONTH)";

        if($filter->template){
            $sql .= " AND template_name  = " . quoteSQL($filter->template);
        }

        if ($filter->to_address) {
            $sql .= " AND to_address like " . quoteSQL('%' . $filter->to_address . '%');
        }
		$dw_email = new DataWindowHelper("emails", $sql, "date_sent", "DESC");
        $dw_email->run();

        BreadcrumbHelper::addBreadcrumbs("Email tracker", APP_URL . 'email_tracker');

        TemplateHandler::setSideMenu("Admin", "Email tracker");
        TemplateHandler::setPageTitle("Email tracker");
        TemplateHandler::setMainView("views/email_tracker/list.php");

        include("templates/standard.php");
    }

    /** to allow user to go back from browsers */
    public function filterList() {
        $filter = new FilterHelper('email_tracker');

        redirect(APP_URL . 'email_tracker');
    }

    public function view($email_tracker_id) {
        $email_tracker_id = (int)$email_tracker_id;

        $sql = "SELECT * 
                FROM email_tracker
                WHERE email_tracker_id = " . quoteSQL($email_tracker_id);
        $email = runQueryGetFirstRow($sql);

        include("views/email_tracker/view.php");
    }

    public function truncate() {
        $sql = "delete from email_tracker where date_sent < DATE_SUB(NOW(), INTERVAL 18 MONTH)";
        runQuery($sql);
        echo "done <a href='" . APP_URL . "email_tracker'>back</a>";
    }
}

