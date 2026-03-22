<div class="container">
    <div class="row" id="listing-page" data-listing_id="<?= ($listing->listing_id) ?>">
        <div class="col-12 col-md-6 text-center text-md-left mb-4 mb-md-0">
            <?
            $temp_img = new FileHelper('listing_images', $listing->listing_id);
            $thumbnail = $temp_img->getImagePathFromTag("most_recent_upload", 600, 600);
            $ih = $temp_img->getImageHelperFromTag("most_recent_upload");

            $ih->setTargetWidthAndHeight('1200', '1200', 'thumbnail');
            $fullsize_img = $temp_img->cacheImageFromImageHelper($ih);

            $listing_url = SITE_URL . seoFriendlyURLs($listing->listing_id, "listing", '', $listing->title);
            ?>
            <a href="<?= ($fullsize_img) ?>" target="_blank" class="d-block mb-3"><img class="listing_image"
                                                                                       src="<?= ($thumbnail) ?>"/></a>
            <?
            if ($listing->isActive()) {
                ?>
                <a target="_blank"
                   href="mailto:?subject=Freestuff The website where everything is free&body=You might be interest in this item <?= urlencode($listing->title) ?>: <?= $listing_url ?>"
                   class="btn btn-share radius-2 w-50 p-1 "
                   title="<?= str_replace('"', '&quot;', $listing->title) ?>"><span class="fa fa-share-alt"></span>
                    SHARE</a>
                <?
                if (!$listing->isMyListing()) {
                    ?>
                    <a href="#" data-href="<?= (APP_URL) ?>report/report/<?= ($listing->listing_id) ?>"
                       class="btn danger radius-2 w-50 p-1 mt-3 ajax-modal">Report This Listing</a>
                    <?
                }
            }
            ?>
        </div>
        <div class="col-12 col-md-6">
            <?
            if (!$listing->canBeRequested()) {
                ?>
                <div class="bg-warning p-3 mb-3 text-dark">This listing
                    is <?= ($listing->isReserved() ? 'Reserved' : ($listing->isWanted() ? 'Inactive' : 'Unavailable')) ?>!
                </div>
                <?
            }
            ?>
            <p>
                <b>Listing No: </b> <?= ($listing->listing_id) ?>
            </p>

            <p>
                <b>Listed By:</b> <span class="user-listings-link" data-href="<?= APP_URL ?>user/all_listings/<?= ($listing->user_id) ?>" style="cursor:pointer; color: #007bff; text-decoration: underline;"><?= ($listing->user_firstname) ?></span>
                <br>
                
                <?
                $givenCount = Listing::getGivenCount($listing->user_id);
                if ($givenCount >= 25) { $giverBadge = 'Gold Giver'; $giverColor = '#FFD700'; }
                elseif ($givenCount >= 10) { $giverBadge = 'Silver Giver'; $giverColor = '#D8D8D8'; }
                elseif ($givenCount >= 3) { $giverBadge = 'Bronze Giver'; $giverColor = '#E8A96A'; }
                else { $giverBadge = null; $giverColor = null; }

                if ($giverBadge) { ?>
                    <span class="badge" style="background-color: <?= $giverColor ?>; color: #333;"><?= $giverBadge ?></span>
                <? }

                if ($listing->isMyListing()) {
                    ?>
                    <span class="badge badge-warning">My Listing</span>
                    <?
                } 
                ?>
            </p>
            <?
            $date_format = "l j<\s\u\p>S</\s\u\p> F" . (DateHelper::difference($listing->listing_date, date('Y-m-d'), 'MONTH') > 6 ? ' Y' : '');
            ?>
            <p>
                <b>Listing Date:</b> <?= (date($date_format, DateHelper::timestamp($listing->listing_date))) ?>
            </p>
            <p>
                <b>Views:</b> <?= ($listing->visits) ?>
            </p>
            <?
            if ($listing->isActive()) { ?>
                <p>
                    <?
                    echo nl2br($listing->description); ?>
                </p>
                <?
                if ($listing->isMyListing()) {
                    if ($listing->isWanted()) {
                        $tmp_relist_delist_text = $listing->canBeRequested() ? 'Mark as no longer wanted' : '';
                    } else {
                        $tmp_relist_delist_text = $listing->canBeRequested() ? 'Delete My Item' : '';
                    }
                    ?>
                    <p><a class="mt-3 btn secondary edit-link" href="list/edit/<?= $listing->listing_id ?>">Edit</a></p>
                    <button class="btn btn-danger ajax-modal"
                            data-href="<?= (APP_URL) ?>list/delete_modal/<?= $listing->listing_id ?>">
                        Delete My Item
                    </button>
                    <?
                }
            } ?>
        </div>

    </div>
</div>
