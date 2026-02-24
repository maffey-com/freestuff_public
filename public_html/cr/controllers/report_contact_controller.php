<?php
class ReportContactController extends _Controller {

    public function index() {
        $filter = new FilterHelper('report_contact');

        $sql = "SELECT * 
        		FROM contact
        		WHERE 1 = 1 ";
		if ($filter->status) {
			$sql .= " AND status = " . quoteSQL($filter->status);
		}
		$dw_contact = new DataWindowHelper("contact", $sql, "contact_date", "DESC");
		$dw_contact->run();

        BreadcrumbHelper::addBreadcrumbs("Reports");
        BreadcrumbHelper::addBreadcrumbs("Contacts");

        TemplateHandler::setSideMenu("Reports", "Contacts");
        TemplateHandler::setPageTitle("Contacts");
        TemplateHandler::setMainView("views/report_contact/list.php");

        include("templates/standard.php");
    }

    /** to allow user to go back from browsers */
    public function filterList() {
        $filter = new FilterHelper('report_contact');

        redirect(APP_URL . 'report_contact');
    }

    public function view($contact_id) {
        $contact_id = (int)$contact_id;

        $contact = new Contact();
        $contact->retrieveFromID($contact_id);

        BreadcrumbHelper::addBreadcrumbs("Reports");
        BreadcrumbHelper::addBreadcrumbs("Contacts", APP_URL . 'report_contact');
        BreadcrumbHelper::addBreadcrumbs("View");

        TemplateHandler::setSideMenu("Reports", "Contacts");
        TemplateHandler::setPageTitle("Contacts");
        TemplateHandler::setMainView("views/report_contact/view.php");

        include("templates/standard.php");
    }

    public function close($contact_id) {
        $contact_id = (int)$contact_id;

        Contact::updateStatus($contact_id, 'Closed');

        MessageHelper::setSessionSuccessMessage('Contact has been closed.');
		redirect(APP_URL . "report_contact");
    }

    public function unclose($contact_id) {
        $contact_id = (int)$contact_id;

        Contact::updateStatus($contact_id, 'New');

        MessageHelper::setSessionSuccessMessage('Contact has been re-opened.');
		redirect(APP_URL . "report_contact");
    }

    public function sendReply() {
        $contact_id = (int)paramFromPost('contact_id');

        $contact = new Contact();
        $contact->retrieveFromID($contact_id);
        $contact->saveReply(paramFromPost('reply'));

        if (hasErrors()) {
            echo json_encode(getErrors());
        } else {
            MessageHelper::setSessionSuccessMessage('Reply message has been sent.');
        	echo "1";
		}
		die();
    }
}

