<?php
$search_string = TemplateHandler::getSearchText();
$save_search_text = '';
$giverBadge = null;
$giverColor = null;

switch (_Controller::getControllerName()) {
    case 'Search':
        $title = "Search Results ".($search_string ? "for " . $search_string : "");
        $current_sort = paramFromGet('sort') === 'oldest' ? 'oldest' : 'newest';
        $current_type = in_array(paramFromGet('listing_type'), ['free', 'wanted', 'all']) ? paramFromGet('listing_type') : 'all';

        if ($search_string) {
            $save_search_text = '<p><a href="'.APP_URL.'search/save/'.urlencode(TemplateHandler::getSearchText()).'">Click here to save this search</a>.</p>' .
                            '<p>You will get notified by email when new items are listed that match your saved search words.</p>';
        }
        break;

    case 'User':
        $title = "All listings from " . $user->firstname;
        $current_sort = paramFromGet('sort') === 'oldest' ? 'oldest' : 'newest';
        $current_type = in_array(paramFromGet('listing_type'), ['free', 'wanted', 'all']) ? paramFromGet('listing_type') : 'all';
        $givenCount = Listing::getGivenCount($user->user_id);

        if ($givenCount >= 25) { $giverBadge = 'Gold Giver'; $giverColor = '#FFD700'; }
        elseif ($givenCount >= 10) { $giverBadge = 'Silver Giver'; $giverColor = '#D8D8D8'; }
        elseif ($givenCount >= 3) { $giverBadge = 'Bronze Giver'; $giverColor = '#E8A96A'; }
        else { $giverBadge = null; $giverColor = null; }
        break;

    default:
        $title = "Freestuff in ".TemplateHandler::getBrowseCategoryName();
        $save_search_text = '<p><a href="'.APP_URL.'browse/save/'.urlencode(TemplateHandler::getBrowseCategoryName()).'">Click here to receive notifications about new listings in this region</a>.</p>';

        $current_sort = paramFromGet('sort') === 'oldest' ? 'oldest' : 'newest';
        $current_type = in_array(paramFromGet('listing_type'), ['free', 'wanted', 'all']) ? paramFromGet('listing_type') : 'all';
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
                <span class="badge" style="background-color: <?= $giverColor ?>; color: #333;"><?= $giverBadge ?></span>
            <? }

            if (in_array(_Controller::getControllerName(), ['Browse', 'Search', 'User'])) {
                $q_param = $search_string ? '?q=' . urlencode($search_string) . '&' : '?';
                $controller = _Controller::getControllerName();
                if ($controller === 'Browse') {
                    $base_url = APP_URL . 'browse/by-region/' . urlencode(TemplateHandler::getBrowseCategoryName()) . '?';
                } elseif ($controller === 'Search') {
                    $base_url = APP_URL . 'search/search' . $q_param;
                } else {
                    $base_url = APP_URL . 'user/alllistings/' . $user->user_id . '?';
                }

                ?>
                <div class="mt-2">
                    <a href="<?= $base_url ?>listing_type=free<?= $current_sort === 'oldest' ? '&sort=oldest' : '' ?>" class="btn btn-sm <?= $current_type === 'free' ? 'btn-primary' : 'btn-outline-secondary' ?>">Free</a>
                    <a href="<?= $base_url ?>listing_type=wanted<?= $current_sort === 'oldest' ? '&sort=oldest' : '' ?>" class="btn btn-sm <?= $current_type === 'wanted' ? 'btn-primary' : 'btn-outline-secondary' ?>">Wanted</a>
                    <a href="<?= $base_url ?>listing_type=all<?= $current_sort === 'oldest' ? '&sort=oldest' : '' ?>" class="btn btn-sm <?= $current_type === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
                    &nbsp;
                    <a href="<?= $base_url ?>listing_type=<?= $current_type ?>" class="btn btn-sm <?= $current_sort === 'newest' ? 'btn-primary' : 'btn-outline-secondary' ?>">Newest</a>
                    <a href="<?= $base_url ?>listing_type=<?= $current_type ?>&sort=oldest" class="btn btn-sm <?= $current_sort === 'oldest' ? 'btn-primary' : 'btn-outline-secondary' ?>">Oldest</a>
                </div>
            <? } ?>
        </div>
    </div>
</div>
