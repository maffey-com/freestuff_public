<div id="listing-banner">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-block d-md-flex">
                    <div class=" mb-3 mb-md-0">
                        <?
                        if ($listing->isWanted()) {
                            ?>
                            <div class="badge badge-danger ">Wanted Item</div>
                            <?
                        }
                        ?>
                        <h1 class="mb-3">
                            <?= ($listing->title) ?>
                        </h1>

                        <span id="location-pointer"
                              class="fa fa-map-marker"></span> <?= District::display2($listing->district_id) ?>
                    </div>

                    <div class="d-block d-md-flex ml-auto">
                        <div>
                            <?
                            if ($listing->isMyListing()) {
                                # IS my listing
                                if ($listing->isWanted()) {
                                    $tmp_button_taken_text = $listing->isActive() ? 'Mark as no longer wanted' : '';
                                } else {
                                    $tmp_button_taken_text = $listing->isActive() ? 'Mark as gone' : '';
                                    $tmp_button_reserved_text = $listing->canBeRequested() ? 'Mark as Reserved' : 'Remove Reserve';
                                }

                                if ($listing->isActive()) {
                                    ?>
                                    <!--                                    <a class="btn secondary btn-mobile"-->
                                    <!--                                       href="--><? // (APP_URL) ?><!--list/edit/--><?php //= $listing->listing_id ?><!--">Edit</a>-->

                                    <? if (!$listing->isWanted()) { ?>
                                        <button class="btn btn-primary ajax-modal"
                                                data-href="<?= (APP_URL) ?>list/mark_as_gone_modal/<?= $listing->listing_id ?>">
                                            <?= $tmp_button_taken_text ?>
                                        </button>
                                        <button class="btn btn-primary ajax-modal"
                                                data-href="<? (APP_URL) ?>list/mark_as_reserved_modal/<?= $listing->listing_id ?>">
                                            <?= $tmp_button_reserved_text ?>
                                        </button>
                                    <? } else { ?>
                                        <a data-status="delist" data-listing_id="<?= $listing->listing_id ?>"
                                           class='status-btn btn danger btn-mobile'><?= $tmp_button_taken_text ?></a>
                                        <?
                                    }
                                } else {
                                    ?>
                                    <a data-status="relist" data-listing_id="<?= $listing->listing_id ?>"
                                       class='status-btn btn primary btn-mobile'>Relist this item</a>
                                    <?
                                }

                                # NOT my listing
                            } elseif ($listing->isActive()) {
                                if ($listing->haveIRequestedThisItem()) {
                                    ?>
                                    <div class="mb-3">
                                        <strong>You Have <?= $listing->isWanted() ? 'Offered' : 'Requested' ?> This
                                            Item</strong>
                                    </div>
                                    <?
                                } elseif ($listing->hasReachedMaxRequestLimit()) { ?>
                                    <div class="text-danger">
                                        <?= (int)$listing->request_count ?> out of <?= REQUESTS_PER_ITEM_HARD_LIMIT ?>
                                        requests<br/>
                                        This item can no longer be requested
                                    </div>

                                <? } elseif ($listing->canBeRequested()) {
                                    # is available
                                    if ($listing->isWanted()) {
                                        $tmp_request_btn_label = 'Contact the Lister';
                                    } else {
                                        $tmp_request_btn_label = 'Request this item';
                                    }
                                    ?>
                                    <a href="<?= (APP_URL) ?>request/request/<?= ($listing->listing_id) ?>"
                                       class='btn primary btn-mobile'><?= ($tmp_request_btn_label) ?></a><br/>
                                    <?= (int)$listing->request_count ?> out of  <?= REQUESTS_PER_ITEM_HARD_LIMIT ?> requests
                                    <?
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
