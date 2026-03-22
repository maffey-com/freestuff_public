<?php
/**
 * @var FilterHelper $filter
 * @var String $title
 * @var String $which
 * @var ArrayObject $listings
 */
?>
<div class="container">
    <div id="" class="row fs-page-header">
        <div class="col">
            <div class="d-block d-md-flex align-items-start">
                <div>
                    <h1 class="mb-2"><?=($title)?></h1>
                    <?
                    $base_url = APP_URL . 'my_freestuff/listings/' . $which . '?';
                    $current_sort = paramFromGet('sort') === 'oldest' ? 'oldest' : 'newest';
                    $current_type = in_array(paramFromGet('listing_type'), ['free', 'wanted', 'all']) ? paramFromGet('listing_type') : 'all';
                    ?>
                    <div class="mb-2">
                        <a href="<?= $base_url ?>listing_type=free<?= $current_sort === 'oldest' ? '&sort=oldest' : '' ?>" class="btn btn-sm <?= $current_type === 'free' ? 'btn-primary' : 'btn-outline-secondary' ?>">Free</a>
                        <a href="<?= $base_url ?>listing_type=wanted<?= $current_sort === 'oldest' ? '&sort=oldest' : '' ?>" class="btn btn-sm <?= $current_type === 'wanted' ? 'btn-primary' : 'btn-outline-secondary' ?>">Wanted</a>
                        <a href="<?= $base_url ?>listing_type=all<?= $current_sort === 'oldest' ? '&sort=oldest' : '' ?>" class="btn btn-sm <?= $current_type === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
                        &nbsp;
                        <a href="<?= $base_url ?>listing_type=<?= $current_type ?>" class="btn btn-sm <?= $current_sort === 'newest' ? 'btn-primary' : 'btn-outline-secondary' ?>">Newest</a>
                        <a href="<?= $base_url ?>listing_type=<?= $current_type ?>&sort=oldest" class="btn btn-sm <?= $current_sort === 'oldest' ? 'btn-primary' : 'btn-outline-secondary' ?>">Oldest</a>
                    </div>
                </div>

                <?
                if ($which != 'current') {
                    ?>
                    <select id="input-age" class="form-control w-auto ml-md-auto">
                        <?
                        option('30', "Last 30 days", $filter->age);
                        option('60', "Last 60 days", $filter->age);
                        option('90', "Last 90 days", $filter->age);
                        option('', "All time", $filter->age);
                        ?>
                    </select>
                    <?
                }
                ?>
            </div>
        </div>
    </div>

    <div class="row" id="my-listings-page">
		<?
		if (count($listings->data)) {
			foreach ($listings->data as $listing) {
                if (SESSION_USER_ID && !UserBlocked::canSeeListing(SESSION_USER_ID, $listing["user_id"])) { // skip blocked users
                    continue;
                }
				$tmp_id = $listing["listing_id"];
				$tmp_user_id = $listing["user_id"];
				$tmp_title = $listing["title"];
				$tmp_description = $listing["description"];
				$tmp_listing_type = $listing['listing_type'];
				$tmp_request_count = (int)$listing['request_count'];
				$tmp_is_wanted = ($tmp_listing_type == 'wanted');
				$tmp_url = seoFriendlyURLs($tmp_id, "listing", FALSE, $tmp_title);

				$tmp_img = new FileHelper('listing_images', $tmp_id);
				$thumbnail = $tmp_img->getImagePathFromTag("most_recent_upload", 320, 320); ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 listing-item-col">
                    <? if ($which == 'watchlist') { ?>
                        <div class='listing-item listing-item-home text-left'>
                            <div class='pic_bit'>
                                <img src='<?= ($thumbnail) ?>' alt='<?= ($tmp_title) ?>' title='<?= ($tmp_title) ?>'/>
                            </div>
                            <div class="listing-body">
                                <h5 data-href="<?= ($tmp_url) ?>" style="cursor:pointer; text-decoration: underline" class="title text-truncate"><?= ($tmp_title) ?></h5>
                                <span><b>Listed By:</b> &nbsp;<span class="user-listings-link" data-href="<?= APP_URL ?>user/all_listings/<?= ($listing['user_id']) ?>" style="cursor:pointer; color: #007bff; text-decoration: underline;"><? h($listing['firstname']); ?></span></span><br/>
                                <?
                                $givenCount = Listing::getGivenCount($listing['user_id']);
                                if ($givenCount >= 25) { $giverBadge = 'Gold Giver'; $giverColor = '#FFD700'; }
                                elseif ($givenCount >= 10) { $giverBadge = 'Silver Giver'; $giverColor = '#D8D8D8'; }
                                elseif ($givenCount >= 3) { $giverBadge = 'Bronze Giver'; $giverColor = '#E8A96A'; }
                                else { $giverBadge = null; $giverColor = null; }

                                if ($giverBadge) { ?>
                                    <br>
                                    <span class="badge" style="background-color: <?= $giverColor ?>; color: #333;"><?= $giverBadge ?></span>
                                <? }
                                if ($listing['listing_type'] == 'wanted') {?>
                                    <span class="badge badge-danger text mt-1" style="font-size: 100%;">Wanted</span>
                                <?
                                }?>
                            </div>
                            <div class="listing-footer text-muted font-italic"><?= (District::display2($listing['district_id'])) ?></div>
                        </div>
                    <? } else { ?>
                        <a class='listing-item listing-item-home text-left' href="<?= ($tmp_url) ?>">
                            <div class='pic_bit'>
                                <img src='<?= ($thumbnail) ?>' alt='<?= ($tmp_title) ?>' title='<?= ($tmp_title) ?>'/>
                            </div>
                            <div class="listing-body">
                                <h5 class="title text-truncate"><?= ($tmp_title) ?></h5>
                                <?
                                if ($listing['listing_type'] == 'wanted') {?>
                                    <span class="badge badge-danger text mt-1" style="font-size: 100%;">Wanted</span>
                                <?
                                }?>
                            </div>
                            <div class="listing-footer text-muted font-italic"><?= (District::display2($listing['district_id'])) ?></div>
                        </a>
                    <? } ?>
                </div>
			<? } ?>
			<?
		} else { ?>
            <div class="col-12">
                <p class="mb-4">You have no listings <?= (empty($filter_age) ? '' : ' in the last <b>' . $filter_age . '</b> days') ?>. </p>

                <p>Give something away today to remove clutter from your house and make somebody elses day.</p>
                <a class="btn btn-primary btn-mobile" href="<?= (APP_URL) ?>list">Click here to list an item</a>
            </div>
			<?
		}
		?>
    </div>
</div>

<?
require_once('templates/common_pager_bottom.php');