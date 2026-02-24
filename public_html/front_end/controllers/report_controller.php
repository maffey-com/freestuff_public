<?php
class ReportController extends _Controller {
    public function report($listing_id) {
        $listing_id = (int)$listing_id;

        if (SecurityHelper::isLoggedIn()) {
            ModalHelper::setViews("views/report/form.php");

        } else {
            ModalHelper::setViews("views/report/form_login_required.php");
        }

        include("templates/modal_layout.php");
        exit();
    }

    public function processReport($listing_id) {
        self::loginRequiredAndEchoJsonError();

        $listing_id = (int)$listing_id;
        $report_comment = paramFromPost('report_comment');

        if (Listing::report($listing_id, SESSION_USER_ID, $report_comment)) {
            echo '1';
        } else {
            echo json_encode(getErrors());
        }
        exit();
    }

    public function saved() {
        self::loginRequiredAndEchoErrorMessage();

        include("views/report/saved.php");
	    exit();
    }
}