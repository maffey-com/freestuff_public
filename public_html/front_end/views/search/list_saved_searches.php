<?php
/**
* @var array $saved_searchs
 */
?>
<div class="container">
    <?
    TemplateHandler::echoPageTitle('Saved search', 'Saved searches allow you to get notifications from us when new stuff your interested in is listed');
    ?>

    <div class="row fs-block">
        <div class="col">
            <p>To create a new saved search, use the search box above to search for something, then click the link to "save this search".</p>
        </div>
    </div>
    <div class="row" id="list-saved-search-page">
        <div class="col">
            <?
            TemplateHandler::echoSubTitle('My Saved Searches');

            if (count($saved_searchs) > 0) {
                ?>
                <ul class="list-group">
                    <?
                    foreach ($saved_searchs as $saved_search) {
                        if (stristr($saved_search["listing_type"], "free") &&  stristr($saved_search["listing_type"], "wanted")) {
                            $type_text = "Free and Wanted";

                        } elseif (stristr($saved_search["listing_type"], "free")) {
                            $type_text = "Free";

                        } else {
                            $type_text = "Wanted";
                        }
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?=($type_text)?> items<?=(!empty($saved_search["search_string"])?' matching "'.$saved_search["search_string"].'"':'')?> in <?=($saved_search["regions"])?>
                            <a href="<?=(APP_URL)?>search/edit/<?=($saved_search["search_id"])?>" class="btn primary">Edit</a>
                          </li>
                        <?
                    } ?>
                </ul>
                <?
            } else {
                ?>
                <p>You do not have any saved search.</p>
                <?
            }
            ?>
        </div>
    </div>
</div>