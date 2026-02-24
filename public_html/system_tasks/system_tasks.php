<?
chdir(__DIR__);
require_once("resources/initial.php");
if (!is_cli()) {
    exit();
}


#include("../common/resources/email_funcs.php");
# expiry listings
$sql = "SELECT l.listing_date, l.title listing_title, l.user_firstname, l.listing_id, l.listing_type,
        li.email lister_email,  li.user_id lister_user_id
        FROM listing l
        JOIN user li ON li.user_id = l.user_id
        WHERE l.listing_date < DATE_ADD(NOW(), INTERVAL -2 WEEK)
        AND l.listing_status IN ('available', 'reserved')
        AND l.expiry_reminded IS NULL
        AND l.removed_date IS NULL
        ORDER BY listing_date ASC
        LIMIT 50";
$reminders = runQueryGetAll($sql);
foreach ($reminders as $reminder) {
    $is_free_listing = ($reminder['listing_type'] == 'free');
    $dict = array("__lister_firstname__" => $reminder["user_firstname"], "__listing_title__" => $reminder["listing_title"], "__link__" => SITE_URL . "r/p");
    if ($is_free_listing) {
        $eh = new EmailHelper();
        $eh->setTemplate("Freestuff expiry reminder", $dict);
        $eh->setTo($reminder["lister_email"]);
        $eh->send();
    } else {
        $eh = new EmailHelper();
        $eh->setTemplate("Wanted stuff expiry reminder", $dict);
        $eh->setTo($reminder["lister_email"]);
        $eh->send();
    }

    $sql = "UPDATE listing SET 
            expiry_reminded = now(),
            listing_status = 'expired' 
            WHERE listing_id = " . quoteSQL($reminder["listing_id"]);
    runQuery($sql);
    echo 'Expired listing: ' . $reminder['listing_id'] . "<br>";
}
// Reserved listings
$sql = "SELECT l.listing_date, l.title listing_title, l.user_firstname, l.listing_id,
        li.email lister_email,  li.user_id lister_user_id
        FROM listing l
        JOIN user li ON li.user_id = l.user_id
        WHERE l.reserved_date < DATE_ADD(NOW(), INTERVAL -" . Listing::$reserve_expiry_hours . " HOUR)
        AND l.listing_status = 'reserved'
        AND l.expiry_reminded IS NULL
        AND l.removed_date IS NULL
        ORDER BY listing_date ASC
        LIMIT 50";
$reminders = runQueryGetAll($sql);
foreach ($reminders as $reminder) {
    $dict = array("__lister_firstname__" => $reminder["user_firstname"], "__listing_title__" => $reminder["listing_title"], "__link__" => SITE_URL . "view?listing_id=" . $reminder["listing_id"]);
    $eh = new EmailHelper();
    $eh->setTemplate("Reserved expiry reminder", $dict);
    $eh->setTo($reminder["lister_email"]);
    $eh->send();

    $sql = "UPDATE listing SET
            reserved_date = NULL,
            listing_status = 'available'
            WHERE listing_id = " . quoteSQL($reminder["listing_id"]);
    runQuery($sql);
    echo 'Reserved listing relisted: ' . $reminder['listing_id'] . "<br>";
}
//reminder to admins
$sql = "SELECT COUNT(listing_id) 
        FROM listing
        WHERE listing_date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
        AND listing_status IN ('available', 'reserved') 
        AND listing_type = 'free'";
$new_listings_count = runQueryGetFirstValue($sql);

$sql = "SELECT COUNT(r.report_id)
        FROM report r
        LEFT JOIN user reporter ON reporter.user_id = r.user_id
        JOIN listing ON listing.listing_id = r.listing_id
        JOIN user lister ON lister.user_id = listing.user_id
        WHERE r.report_date >= DATE_SUB(CURDATE(), INTERVAL 3 WEEK) 
        AND r.status = 'NEW'";
$new_report_count = runQueryGetFirstValue($sql);

$sql = "SELECT COUNT(c.id)
        FROM contact c
        WHERE c.status = 'New'";
$new_contact_count = runQueryGetFirstValue($sql);
$dashboard_note = "New Listings: " . $new_listings_count . "\n";
$dashboard_note .= "New Reports: " . $new_report_count . "\n";
$dashboard_note .= "New Contacts: " . $new_contact_count . "\n";
$dashboard_note .= "\nhttps://freestuff.co.nz/cr";

foreach (User::ADMIN_EMAILS as $email) {
    $eh = new EmailHelper();
    $eh->setSubject("Freestuff Dashboard");
    $eh->setBody($dashboard_note);
    $eh->setTo($email);
    $eh->send();
    #mail($email, "Freestuff Dashboard", $dashboard_note);
}

User::removeExpiredRememberMe();

Thumb::refreshCredit();
Thumb::applyRequestCredit();


$adsense = Adsense2::retrieve();
if (!$adsense->hasToken()) {
    echo "no token\n";
} else {
    echo "fetching adsense earnings\n";
    $earnings = $adsense->fetchEarnings();
    echo "Last Months earnings: " . $earnings['monthly'][sizeof($earnings['monthly']) - 2]['cells'][1]['value'] . "\n";
}


echo "system task completed\n";
die();
