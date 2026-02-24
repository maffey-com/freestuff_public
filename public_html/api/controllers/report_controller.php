<?php
    class ReportController extends _Controller {
        public function process($listing_id) {
            self::loginRequiredAndEchoJsonError();

            $listing_id = (int)$listing_id;
            $report_comment = paramFromPost('report_comment');

            if (Listing::report($listing_id, SESSION_USER_ID, $report_comment)) {
                self::echoJsonSuccessAndExit();
            } else {
                self::echoJsonErrorsAndExit();
            }
            exit();
        }
    }
