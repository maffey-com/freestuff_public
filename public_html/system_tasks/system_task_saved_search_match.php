<?php
require_once("resources/initial.php");

if (paramFromGet('password') != 'chugtit') {
    exit('x');
}

#include("../common/resources/email_funcs.php");

//find any new listings where no notification has been sent
$sql = "SELECT l.user_id lister_id, l.listing_id, l.title listing_title, l.listing_type, d.region, description
        FROM listing l
        JOIN district d on d.district_id = l.district_id
        WHERE l.listing_status IN ('available', 'reserved')
        AND saved_search_date IS NULL
        LIMIT 1";
$listing = runQueryGetFirstRow($sql);

if (!$listing) {
    exit('no new listings');
}
//find users with saved searches that match
$sql = "SELECT DISTINCT 
        u.user_id, u.email, u.firstname 
        FROM saved_search s
        JOIN user u ON (u.user_id = s.user_id AND u.email_bounced_date IS NULL)
        WHERE s.user_id <> " . quoteSQL($listing["lister_id"]) . "
        AND s.regions LIKE " . quoteSQL("%" . $listing["region"] . "%") . "
        AND s.listing_type LIKE " . quoteSQL("%" . $listing["listing_type"] . "%") . "
        AND (match(s.search_string) AGAINST (" . quoteSQL($listing["listing_title"] . " " . $listing["description"]) . "))";
$users = runQueryGetAll($sql);
if (count($users) > 0) {
    foreach ($users as $user) {
        $dict = array(
            "__firstname__" => $user["firstname"],
            "__listing_title__" => $listing["listing_title"],
            "__description__" => $listing["description"],
            "__link__" => SITE_URL . 'r/l/' . $listing["listing_id"],
            "__unsubscribe_link__" => SITE_URL . 'r/d',
            "__saved_searches_link__" => SITE_URL . "r/s"
        );
        //seoFriendlyURLs($listing["listing_id"], "listing", false, $listing["listing_title"]),

        $reply_to = EmailHelper::generateSavedSearchReplyTo($user['user_id']);

        $eh = new EmailHelper();
        $eh->setTemplate("Saved Search Match", $dict);
        $eh->setTo($user["email"]);
        $eh->setReplyTo($reply_to);
        $eh->setFrom($reply_to, "Freestuff NZ");
        $eh->send();
        #sendEmailTemplate("Saved Search Match", $dict, $user["email"]);
    }
} else {
    echo "No saved searches match listing: " . $listing["listing_title"];
}
# mark listing as processed
$sql = "UPDATE listing SET 
		saved_search_date = NOW() 
		WHERE listing_id = " . quoteSQL($listing["listing_id"]);
runQuery($sql);

echo "Listing ID: " . $listing["listing_id"] . " - found (" . count($users) . ")";


