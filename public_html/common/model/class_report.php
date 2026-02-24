<?
class Report extends CRModel {
	public $report_id;
	public $user_id;
	public $report_date;
	public $report_comment;
	public $ip_address;
	public $listing_id;
	public $freestuff_comment;
	public $status;
	public $freestuff_action_date;

	public $reporter_firstname;
	public $reporter_location;
	public $reporter_user_id;
	public $reporter_email;

	public $lister_firstname;
	public $lister_location;
	public $lister_user_id;
	public $lister_email;

	public $listing_title;
	public $listing_visits;

	public function __construct() {}

	public function retrieveFromID($report_id) {
		$report_id = (int)$report_id;

		$sql = "SELECT report.report_id, report.report_date, report.report_comment, report.ip_address, report.listing_id,
				report.freestuff_comment, report.status, report.freestuff_action_date,
		 		reporter.firstname reporter_firstname,  reporter.user_id reporter_user_id, reporter.email reporter_email,
				listing.user_firstname lister_firstname, listing.district_id listing_district_id, listing.user_id lister_user_id, lister.email lister_email, listing.title listing_title, listing.visits listing_visits
				FROM report 
				LEFT JOIN user reporter on reporter.user_id = report.user_id
				JOIN listing on listing.listing_id = report.listing_id
				JOIN user lister on listing.user_id = lister.user_id
				WHERE report_id = " . $report_id;
		$row = runQueryGetFirstRow($sql);
		if ($row) {
			$this->_populateFromArray($row);
		}
	}

	private static function _translateStatusCode($status) {
		switch ($status) {
			case "Remove Listing":
				return "Listing Removed";
				break;

			case "Warn Lister":
				return "Warning";
				break;

			case 'Reject Report':
				return "Report Rejected";
				break;

			default:
				return $status;
				break;
		}
	}

	public function updateStatus($new_status, $freestuff_comment = NULL) {
		$new_status = self::_translateStatusCode($new_status);

		$sql = "UPDATE report SET";
		$sql .= " status = " . quoteSQL($new_status);
		$sql .= ",freestuff_action_date = NOW()";
		if (!empty($freestuff_comment)) {
			$sql .= ",freestuff_comment = " . quoteSQL($freestuff_comment);
		}
		$sql .= " WHERE report_id = " . (int)$this->report_id;
		if (runQuery($sql)) {
			$this->status = $new_status;

			if ($this->removeItem()) {
				return $this->moveListingToWanted();
			}
		}
	}

	public function moveListingToWanted() {
		if ($this->status == 'Wanted') {
			$sql = "UPDATE listing SET 
					listing_type = 'wanted' 
					WHERE listing_type <> 'wanted'
					AND listing_id = " . (int)$this->listing_id;
			return runQuery($sql);
		} else {
			return TRUE;
		}
	}

	public function removeItem() {
		if (in_array($this->status, array('Listing Removed', 'Warning'))) {
			$sql = "UPDATE listing SET
					listing_status = 'removed'
					WHERE listing_id = " . (int)$this->listing_id;
			return runQuery($sql);
		} else {
			return TRUE;
		}
	}

	public static function getReportTemplateName($type) {
		return ($type == "lister") ? "Report email to Lister" : "Report email to Reporter";
	}

	public static function getReportTemplate($type) {
		$emailTemplate = self::getReportTemplateName($type);

		$sql = "SELECT * 
				FROM email_templates 
				WHERE name = " . quoteSQL($emailTemplate);
		return runQueryGetFirstRow($sql);
	}

	public function sendReportEmail($subject, $message, $type) {
		$templateInfo = self::getReportTemplate($type);

		switch ($type) {
			case "reporter":
				$toAddress = $this->reporter_email;
				break;

			case "lister":
				$toAddress = $this->lister_email;
				break;
		}

		/*$fromAddress = (isset($templateInfo['from_address']) && !empty($templateInfo['from_address'])) ? $templateInfo["from_name"] . "<" . $templateInfo["from_address"] . ">" : SITE_MASTER_MAIL;
		$replyAddress = (isset($templateInfo['reply_address']) && !empty($templateInfo['reply_address'])) ? $templateInfo["reply_name"] . "<" . $templateInfo["reply_address"] . ">" : SITE_MASTER_MAIL;

		$headers  = "From: ". $fromAddress ;
		$headers  .= "\nReply-to: ". $replyAddress ;
		$headers .= "\nContent-type: text/plain; charset=UTF-8";*/

        $eh = new EmailHelper();
        $eh->setFrom($templateInfo['from_address'], $templateInfo["from_name"]);
        $eh->setReplyTo($templateInfo['reply_address'], $templateInfo["reply_name"]);
        $eh->setTo($toAddress);
        $eh->setSubject($subject);
        $eh->setBody($message);
        $eh->send();

		#include_once("resources/email_funcs.php");
		#return mail2($toAddress, $subject, $message, $headers, "", $templateInfo['name']);
	}
}
