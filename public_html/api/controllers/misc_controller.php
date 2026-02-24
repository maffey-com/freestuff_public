<?php
class MiscController extends _Controller {


    public function terms() {
        $out = nl2br(file_get_contents("../front_end/views/index/terms.php"));
        echo json_encode($out);
    }

    public function dashboardStats() {
        self::loginRequiredAndEchoJsonError();
        echo json_encode(User::activeStats($_SESSION["session_user_id"]));
    }

    public function regions() {
        $bar = array();
        $counts = Listing::getRegionsWithCount();
        foreach (District::$regions as $region_name) {
            $bar[] = array("id" => $region_name,"name" =>$region_name, 'count'=>(isset($counts[$region_name])?$counts[$region_name]:0));
        }
        echo json_encode($bar);
    }

    public function helloWorld($name = 'World') {

        echo json_encode("Hello " . $name);
    }

    public function log() {
        writeLog(paramFromRequest('data'), 'app_log.txt');
    }

    public function verifyEmail($verify_id) {
        $uv = UserVerify::instanceFromId($verify_id);
        // Check if they used a Yahoo email so we can warn about non delivery
        $output = array('verify_id'=>$verify_id);
        $yahoo_warn = strpos($uv->data, '@yahoo') > -1;
        if ($yahoo_warn) {
            $output['yahoo_warn'] = "<p class='text-danger'>We are having issues with Yahoo email addresses not receiving our messages. It's not just us though, in 2016 Spark moved all Xtra customer emails away from Yahoo following 9 years of security hassles, spam, phishing attacks and confusion.<br /><cite>(source: <a target='_blank' href='https://www.nbr.co.nz/article/spark-finally-ditches-yahoo-moves-xtra-mail-new-zealand-company-smx-ck-194220'>https://www.nbr.co.nz/article/spark-finally-ditches-yahoo-moves-xtra-mail-new-zealand-company-smx-ck-194220</a>)</cite><br /><br />
               If you are having trouble receiving our emails we recommend that you try signing up with another email address. If you don't have another email address consider signing up for a free <a target='_blank' href='https://mail.google.com/mail/signup'>Google email here</a> or a <a target='_blank' href='https://signup.live.com/signup'>Microsoft email here</a>.
            </p>";
        }
        echo json_encode($output);
    }

    public function getAllDistricts() {
        echo json_encode(District::getAllNested());
    }

    public function getMyRequestCredits() {
        $request_credit = Thumb::myRequestCredit(SESSION_USER_ID);
        self::echoJsonSuccessAndExit(["request_credit"=>$request_credit, 'refresh_due'=>Thumb::refreshDueStatement(User::instanceFromId(SESSION_USER_ID))]);
    }

    public function updateThumb($requester_user_id, $up_down) {
        if (!in_array($up_down,array('u','d','x'))) {
            raiseError("invalid thumb");
            self::echoJsonErrorsAndExit();
        }

        self::loginRequiredAndEchoJsonError();

        $requester_user_id = (int)$requester_user_id;
        if (!ListingRequest::getAllUsersRequestedFromUserA(SESSION_USER_ID, array($requester_user_id))) {
            raiseError("No current request from this user");
            self::echoJsonErrorsAndExit();
        }

        Thumb::updateThumb(SESSION_USER_ID, $requester_user_id, $up_down);
        self::echoJsonSuccessAndExit();
    }
}
