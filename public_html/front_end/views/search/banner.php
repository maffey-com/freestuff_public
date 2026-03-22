<?php
$search_string = TemplateHandler::getSearchText();
$save_search_text = '';
$giverBadge = null;
$giverColor = null;

switch (_Controller::getControllerName()) {
    case 'Search':
        $title = "Search Results ".($search_string ? "for " . $search_string : "");

        if ($search_string) {
            $save_search_text = '<p><a href="'.APP_URL.'search/save/'.urlencode(TemplateHandler::getSearchText()).'">Click here to save this search</a>.</p>' .
                            '<p>You will get notified by email when new items are listed that match your saved search words.</p>';
        }
        break;

    case 'User':
        $title = "All listings from " . $user->firstname;
        $givenCount = Listing::getGivenCount($user->user_id);
        if ($givenCount >= 25) { $giverBadge = 'Gold Giver'; $giverColor = '#FFD700'; }
        elseif ($givenCount >= 10) { $giverBadge = 'Silver Giver'; $giverColor = '#D8D8D8'; }
        elseif ($givenCount >= 3) { $giverBadge = 'Bronze Giver'; $giverColor = '#E8A96A'; }
        else { $giverBadge = null; $giverColor = null; }
        break;

    default:
        $title = "Freestuff in ".TemplateHandler::getBrowseCategoryName();
        $save_search_text = '<p><a href="'.APP_URL.'browse/save/'.urlencode(TemplateHandler::getBrowseCategoryName()).'">Click here to receive notifications about new listings in this region</a>.</p>';

        break;
}

$current_page = $_SERVER['REQUEST_URI'];
$x = strstr($current_page,'?',true);
if ($x) {
    $current_page = $x;
}
if (paramFromRequest('q')) {
    $current_page = $current_page . '?q=' . paramFromRequest('q') . '&';
} else {
    $current_page = $current_page . '?';
}
?>

<div class="container mb-4" id="search-banner">
    <div class="row">
        <div class="col-12 col-lg-8 text-center text-md-left">
            <?
            TemplateHandler::echoPageTitle($title);
            echo $save_search_text;

            if ($giverBadge) { ?>
                <span class="badge ml-2" style="background-color: <?= $giverColor ?>; color: #333;"><?= $giverBadge ?></span>
            <? } ?>
        </div>
    </div>
</div>
