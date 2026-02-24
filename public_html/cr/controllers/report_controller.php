<?php
class ReportController extends _Controller {

    public function index() {
        $filter = new FilterHelper('report');

        $filter->setDefault('old', yn($filter->old, 'n'));
        $filter->setDefault('from_date', date("d/m/Y", strtotime("-3 weeks")));
        $filter->setDefault('to_date', date("d/m/Y", strtotime("tomorrow")));

		$sql = "SELECT report.*,
				reporter.firstname reporter_firstname, reporter.district_id reporter_district_id, reporter.user_id reporter_user_id,
				listing.title listing_title, listing.listing_status,  listing.listing_type,
				lister.firstname lister_firstname, lister.district_id lister_district_id, lister.user_id lister_user_id
				FROM report
				LEFT JOIN user reporter ON reporter.user_id = report.user_id
				JOIN listing ON listing.listing_id = report.listing_id
				JOIN user lister ON lister.user_id = listing.user_id
				WHERE report_date BETWEEN " . DateHelper::db($filter->from_date) . " AND " . DateHelper::db($filter->to_date);
		$sql  .= ($filter->old == 'n') ? " AND status = 'NEW'" : "";

		$dw_report = new DataWindowHelper("report", $sql, "report_id");
        $dw_report->run();

        BreadcrumbHelper::addBreadcrumbs("Listings");
        BreadcrumbHelper::addBreadcrumbs("Reports");

        TemplateHandler::setSideMenu("Listings", "Reports");
        TemplateHandler::setPageTitle("Reports");
        TemplateHandler::setMainView("views/report/list.php");

        include("templates/standard.php");
    }

    /** to allow user to go back from browsers */
    public function filterList() {
        $filter = new FilterHelper('report');

        redirect(APP_URL . 'report');
    }

    public function sendEmail($report_id) {
        $report_id = (int)$report_id;
		$type = paramFromPost('type');
        $type = ($type == 'lister') ? 'lister' : 'reporter';

		$subject = paramFromPost('subject');
		$message = paramFromPost('message');

        $report = Report::instanceFromId($report_id);
		if ($report->sendReportEmail($subject, $message, $type)){
			MessageHelper::setSessionSuccessMessage("Email have been sent to " . $type . ". [Report ID: " . $report->report_id . "]");

		} else {
		    MessageHelper::setSessionSuccessMessage("Fail to send email to " . $type . ". [Report ID: " . $report->report_id . "]");
		}

        redirect(APP_URL . 'report');
    }

    public function email($report_id) {
        $report_id = (int)$report_id;
		$type = paramFromGet('type');
        $type = ($type == 'lister') ? 'lister' : 'reporter';

        $report = Report::instanceFromId($report_id);

    	switch ($type) {
    		case "reporter":
    			$dict = array(
				    			"__reporter_firstname__" => $report->reporter_firstname,
				    			"__listing_title__" => $report->listing_title,
				    			"__freestuff_comment__" => $report->freestuff_comment,
			    			);
    			break;

    		case "lister":
    			$dict = array(
				    			"__lister_firstname__" => $report->lister_firstname,
				    			"__listing_title__" => $report->listing_title,
				    			"__freestuff_comment__" => $report->freestuff_comment,
			    			);
    			break;
    	}

    	$email_template = Report::getReportTemplate($type);
    	$email_template['subject'] = EmailHelper::replaceTextWithDictionary($email_template['subject'], $dict);
    	$email_template['message'] = EmailHelper::replaceTextWithDictionary($email_template['message'], $dict);
    	$email_template['message'] = str_replace("\r\n", "\n", $email_template["message"]);

    	$replaceDict_string = '';
    	foreach ($dict as $key => $value) {
    		$replaceDict_string .= $key . ": " . $value . "\n";
    	}

    	$sql = "UPDATE email_templates SET 
    			translated_code = " . quoteSQL($replaceDict_string) . "
    			WHERE name = " . quoteSQL(Report::getReportTemplateName($type));
    	runQuery($sql);

	    require_once('views/report/email.php');
    }

    public function delist($report_id) {
        $report_id = (int)$report_id;

        $report = Report::instanceFromId($report_id);

		if ($report->updateStatus('Listing Removed')) {
			MessageHelper::setSessionSuccessMessage("Listing has been delisted. [ID: " . $report->listing_id . "]");

		} else {
            MessageHelper::setSessionErrorMessage("Fail to delist listing ID: " . $report->listing_id);
		}

		redirect(APP_URL . 'report');
    }

    public function wanted($report_id) {
        $report_id = (int)$report_id;

        $report = Report::instanceFromId($report_id);
		if ($report->updateStatus('Wanted')) {
			MessageHelper::setSessionSuccessMessage("Report status has been changed to wanted. [ID: " . $report->listing_id . "]");

		} else {
		    MessageHelper::setSessionSuccessMessage("Fail to set report status to wanted. [ID: " . $report->listing_id . "]");
			raiseError("Fail to update report status." . $report->report_id);
		}

		redirect(APP_URL . 'report');
    }

    public function updateStatus($report_id) {
        $report_id = (int)$report_id;

        $report = Report::instanceFromId($report_id);

        $new_status = ($report->status == "NEW") ? 'Closed' : 'NEW';
		$freestuff_comment = $report->freestuff_comment  . '. Manual status updated';

		if ($report->updateStatus($new_status, $freestuff_comment)) {
		    MessageHelper::setSessionSuccessMessage("Report status has been changed to " . $new_status . ". [ID: " . $report->listing_id . "]");

		} else {
		    MessageHelper::setSessionSuccessMessage("Fail to set report status to " . $new_status . ". [ID: " . $report->listing_id . "]");
			raiseError("Fail to update report status." . $report->report_id);
        }

		redirect(APP_URL . 'report');
    }
}

