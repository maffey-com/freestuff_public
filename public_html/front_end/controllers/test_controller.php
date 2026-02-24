<?php
ini_set('display_errors', 1);

class TestController extends _Controller {
    public function index() {

    }

    /** to allow user to go back from browsers */
    public function importThumbs() {
    }

    public function dupeThumbs() {
        $sql = " select min(x.thumb_id)
from thumb x
group by x.lister_id,x.requester_id
having count(*) > 1";
        $ids = runQueryGetAllFirstValues($sql);
        foreach ($ids as $id) {
            $sql = "delete from thumb where thumb_id = " . quoteSQL($id);
            echo $sql . "<br/>";
            runQuery($sql);
        }
    }

    public function flood() {
        echo FloodControlHelper::check($_SERVER['REMOTE_ADDR'],10,5) ? 'true' : 'false';
        echo '<br/>';
        //echo FloodControlHelper::check('fish',4,20) ? 'true' : 'false';
    }

    public function flood2() {
        FloodControlHelper::mkdir();

    }

    public function recent() {
        ArrayHelper::printNice(ListingRequest::recentRequestsBetweenUsers(7,array(17,43151)));
    }


    public function inbox() {
        printArray(Message::myMessages());
    }

    public function latest() {
        Message::setLatestInThread(7,17);
    }
    public function firebase() {
        $user = new User();
        $user->retrieveFromID('139915'); // chris@maffey.com is id: 7
        FirebaseHelper::sendNotification($user, 'test', 'this is a test notification', ['listing_id' => '81628']);
//        FirebaseHelper::sendNotification($user, 'New message from Dan', 'Is this still available?', ['open_message' => true, 'other_user_id' => '65795','other_user_name' => 'Dan']);
    }

    public function t3() {
        UserSuspend::newThumbDown(30635);
    }

    public function t5() {
        $district = new District();
        print_r($_SESSION['session_district']);

    }


}
