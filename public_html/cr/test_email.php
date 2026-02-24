<?php
require_once("resources/initial.php");

$dict = array("__firstname__" => 'Caleb',
    "__regions__" => 'Auckland, Chch',
    "__listings__" => "\n" . 'listing part',
    "__saved_searches_link__" => SITE_URL . "r/s",
    "__unsubscribe_link__" => SITE_URL . 'r/d',
);
//seoFriendlyURLs($listing["listing_id"], "listing", false, $listing["listing_title"]),

$reply_to     = EmailHelper::generateSavedSearchReplyTo('7');

$eh = new EmailHelper();
$eh->setTemplate("Saved Region Match", $dict);
$eh->setTo('caleb@maffey.com');
$eh->setReplyTo($reply_to);
$eh->setFrom($reply_to,"Freestuff NZ");
$eh->send();