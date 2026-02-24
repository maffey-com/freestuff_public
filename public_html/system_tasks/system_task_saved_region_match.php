<?php
require_once("resources/initial.php");
if (paramFromGet('password') != 'chugtit') {
    exit('x');
}

// Find any new listings where no notification has been sent
$sql = "SELECT l.user_id lister_id, l.listing_id, l.title listing_title, l.listing_type, d.region, description
        FROM listing l
        join district d on d.district_id = l.district_id
        WHERE l.listing_status IN ('available', 'reserved')
        AND saved_region_search_date IS NULL";
$listings = runQueryGetAll($sql);

if (!$listings) {
    exit('no new listings');
}

$region_listings = ArrayHelper::nestByColumn($listings, 'region');

$user_email_contents = array();

foreach ($region_listings as $region => $listings) {
    // Find users with saved searches that match
    $sql = "SELECT DISTINCT ss.user_id, u.email, u.firstname, ss.listing_type
        FROM saved_search ss
        JOIN user u ON u.user_id = ss.user_id
        WHERE ss.regions LIKE " . quoteSQL("%" . $region . "%") . "
        AND ss.search_string IS NULL";
    $users = runQueryGetAll($sql);

    if (count($users) == 0) {
        continue;
    }

    $listing_ids = array();
    $lister_ids_free = array();
    $lister_ids_wanted = array();
    $listing_text_free = array();
    $listing_text_wanted = array();
    foreach ($listings as $listing) {
        $listing_ids[] = $listing['listing_id'];

        if ($listing['listing_type'] == 'free') {
            $lister_ids_free[$listing['listing_id']] = $listing['lister_id'];
            $listing_text_free[$listing['listing_id']] = $listing['listing_title'] . "\n" . SITE_URL . 'r/l/' . $listing["listing_id"] . "\n\n";
        } else {
            $lister_ids_wanted[$listing['listing_id']] = $listing['lister_id'];
            $listing_text_wanted[$listing['listing_id']] = $listing['listing_title'] . "\n" . SITE_URL . 'r/l/' . $listing["listing_id"] . "\n\n";
        }
    }

    foreach ($users as $user) {
        $users_text = "";
        $users_listings = array_merge(array(), array_keys($lister_ids_free, $user['user_id']), array_keys($lister_ids_wanted, $user['user_id']));

        if (strpos($user['listing_type'], 'free') > -1) {
            foreach ($listing_text_free as $listing_id => $listing_text) {
                if (!in_array($listing_id, $users_listings)) {
                    $users_text .= $listing_text;
                }
            }
        }
        if (strpos($user['listing_type'], 'wanted') > -1) {
            foreach ($listing_text_wanted as $listing_id => $listing_text) {
                if (!in_array($listing_id, $users_listings)) {
                    $users_text .= $listing_text;
                }
            }
        }

        if ($users_text !== "") {
            if (!isset($user_email_contents[$user['user_id']])) {
                $user_email_contents[$user['user_id']] = array(
                    'name' => $user["firstname"],
                    'email' => $user['email'],
                    'regions' => array(),
                    'user_text' => ''
                );
            }
            $user_email_contents[$user['user_id']]['regions'][] = $region;
            $user_email_contents[$user['user_id']]['user_text'] .= $users_text;
        }
    }

    $sql = "UPDATE listing SET
        saved_region_search_date = NOW()
        WHERE listing_id IN (" . arrayToSQLIn($listing_ids) . ")";
    runQuery($sql);
}

foreach ($user_email_contents as $user_id => $details) {
    $dict = array("__firstname__" => $details["name"],
        "__regions__" => implode(', ', $details['regions']),
        "__listings__" => "\n" . $details['user_text'],
        "__saved_searches_link__" => SITE_URL . "r/s",
        "__unsubscribe_link__" => SITE_URL . 'r/d',
        );

    // test
    $reply_to = EmailHelper::generateSavedSearchReplyTo($user_id);

    $eh = new EmailHelper();
    $eh->setTemplate("Saved Region Match", $dict);
    $eh->setTo($details["email"]);
    $eh->setReplyTo($reply_to);
    $eh->setFrom($reply_to, "Freestuff NZ");
    $eh->send();
}
