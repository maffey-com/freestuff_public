<?php
/**
 * @var SavedSearch $saved_search
 */
?>
<div class="container">
    <?
    TemplateHandler::echoPageTitle('Edit Saved Search', 'Saved searches allow you to get notifications from us when new stuff is listed');
    ?>

    <div class="row" id="list-saved-search-page">
        <div class="col">
            <form id='saved_search_form' method='post' action='<?= (APP_URL) ?>search/update/<?= ($saved_search->search_id) ?>'>
                <div class="form-group mb-4">
                    <label for="search_string">Search Text</label>
                    <input type="text" id="search_string" class="form-control" name="search_string" value="<?= $saved_search->search_string ?>">
                </div>

                <?
                TemplateHandler::echoSubTitle('Regions');
                ?>
                <div class="form-group mb-4">
                    <div class="container-fluid">
                        <div class="row">
                            <? foreach (District::$regions as $region) { ?>
                                <div class="form-check col-12 col-md-6">
                                    <input type="checkbox" id="rgn_<?= $region ?>" class="form-check-input" name="regions[]"
                                           value="<?= $region ?>" <?= in_array($region, $saved_search->regions) ? "CHECKED" : "" ?>>
                                    <label class="form-check-label" for="rgn_<?= $region ?>"><?= $region ?></label>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>

                <?
                TemplateHandler::echoSubTitle('Listing Types');
                ?>
                <div class="form-group mb-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-check col-md-6">
                                <input type="checkbox" class="form-check-input" id="free" name="listing_type[]"
                                       value="free" <?= in_array("free", $saved_search->listing_type) ? "CHECKED" : "" ?> />
                                <label class="form-check-label" for="free">Free Items</label>
                            </div>
                            <div class="form-check col-md-6">
                                <input type="checkbox" class="form-check-input" id="wanted" name="listing_type[]"
                                       value="wanted" <?= in_array("wanted", $saved_search->listing_type) ? "CHECKED" : "" ?> />
                                <label class="form-check-label" for="wanted">Wanted Items</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <input type='submit' class='btn primary btn-mobile' value='Update'/>
                    <button data-confirm_href="<?= (APP_URL) ?>search/delete/<?= ($saved_search->search_id) ?>" class="btn btn-danger btn-mobile annoy-ajax-confirm"
                            data-confirm_redirect="<?= APP_URL ?>my_freestuff">Delete
                    </button>
                    <a class="btn  btn-mobile" href="<?= (APP_URL) ?>search">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
