<?php
/**
 * @var User $user
 * @var int $count_saved_searaches
 */

?>
<div class="container">
    <?
    TemplateHandler::echoPageTitle('My Account');
    ?>

    <div class="row fs-block" id="dashboard-page-details">
        <div class="col-12 col-md-6  my-details">
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">First Name</span><?= $user->firstname ?>
                    <?= get_cfg_var('server_environment') == 'DEV' ? ' (' . $user->user_id . ')' : '' ?>
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>account/edit_name">(Change)</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Email</span><?= $user->email ?>
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>account/edit_email">(Change)</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Closest District</span><?= District::display2($user->district_id) ?>
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>account/edit_location">(Change)</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Feedback</span>
                    <? if ($user->thumbs_up == 0 && $user->thumbs_down == 0) { ?>
                        No feedback yet
                    <? } else { ?>
                        <div class="mb-0 mt-sm-0 d-inline-block">
                        <span class=" thumb-span mr-2">
                            <span style="line-height:1"><span class="fa thumb fa-thumbs-up"
                                                              title="Thumb up"></span></span>
                            <span class="thumbs-up"><?= $user->thumbs_up ?? 0 ?></span>
                        </span>
                            <span class="thumb-span">
                            <span style="line-height:1"><span class="fa thumb fa-thumbs-down" title="Thumb down"></span></span>
                            <span class="thumbs-down"><?= $user->thumbs_down ?? 0 ?></span>
                        </span>
                        </div>
                    <? } ?>
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>page/feedback">(about feedback)</a>
                </div>
            </div>

        </div>
        <div class="col-12 col-md-6 my-details">
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Mobile</span><?= SmsPi::isValidMobileNumber($user->mobile) ? $user->mobile : '<span class="small text-muted">No mobile number set</span>' ?>
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>account/edit_mobile">(Change)</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Password</span>******
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>account/edit_password">(Change)</a>
                </div>
            </div>
            <?

            ?>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Saved Searches</span><?= $count_saved_searaches ?> saved
                    search<?= $count_saved_searaches == 1 ? '' : 'es' ?>
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>search">(Manage)</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Request Credits</span><?= $user->request_credit ?>
                    credit<?= $user->request_credit == 1 ? '' : 's' ?> available
                    <br/>
                    <a class="my-details-edit" href="<?= APP_URL ?>page/request_credit">(about request credits)</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span class="my-details-label">Blocked users</span><?= count($blocked_users) ?> blocked
                    user<?= count($blocked_users) == 1 ? '' : 's' ?>
                    <br/>
                    <? if (count($blocked_users) > 0) { ?>
                        <a class="my-details-edit" href="<?= APP_URL ?>page/blocked_users">(View blocked users)</a>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>

    <?
    /**
     * @var int $latest_count
     * @var array $latest_listings
     *
     * @var int $requested_count
     * @var array $requested_listings
     *
     * @var int $previous_count
     * @var array $previous_listings
     */
    ?>

    <div id="dashboard-listings">
        <div class="row fs-block">
            <div class="col">
                <h4><?
                    if (!$latest_count == 0) {
                        ?>
                        <a name="current" href="<?= (APP_URL) ?>my_freestuff/listings/current">Current Listings</a>
                        <?
                    } else {
                        ?>
                        Current Listings
                        <?
                    }
                    ?><span class="text-sm">(<a name="previous" href="<?= (APP_URL) ?>my_freestuff/listings/previous">View previous listings</a>)</span>
                </h4>
                <?
                if ($latest_count == 0) {
                    ?>
                    <p>You have no current listings.</p>
                    <p>Give something away today to remove clutter from your house and make somebody elses day.</p>

                    <a class="btn btn-primary btn-mobile" href="<?= (APP_URL) ?>list">Click here to list an item</a>
                    <?
                } else { ?>
                    <div class="container-fluid px-0">
                        <div class="row">
                            <?
                            foreach ($latest_listings as $k => $v) {
                                $tmp_id = $v["listing_id"];
                                $tmp_title = preg_replace("/,([^\s])/", ", $1", $v['title']);
                                $tmp_description = $v["description"];
                                $temp_img = new FileHelper('listing_images', $tmp_id);
                                $tmp_img = $temp_img->getImagePathFromTag("most_recent_upload", 320, 320);
                                $tmp_url = seoFriendlyURLs($tmp_id, "listing", FALSE, $tmp_title);
                                $extra_class = $k == 2 ? 'd-none d-lg-block' : ($k == 1 ? 'd-none d-md-block' : '');
                                ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 listing-item-col <?= $extra_class ?>">
                                    <a class='listing-item listing-item-home' href="<?= ($tmp_url) ?>">
                                        <div class='pic_bit'>
                                            <img src='<?= ($tmp_img) ?>' alt='<?= ($tmp_title) ?>'
                                                 title='<?= ($tmp_title) ?>'/>
                                        </div>
                                        <div class="listing-body">
                                            <h5 class="listing-title"><?= ($tmp_title) ?></h5>
                                        </div>
                                        <div class="listing-footer text-muted font-italic text-left <?= $v['request_count'] > 0 ? 'green' : '' ?>">
                                            <?
                                            if ($v['listing_type'] == 'wanted') { ?>
                                                <span class="badge badge-danger text"
                                                      style="font-size: 100%;">Wanted</span>
                                                <?
                                            } elseif ($v['request_count'] > 0) {
                                                ?>
                                                <span class="fa fa-bell"></span> You have <?= $v['request_count'] ?: 0 ?> request<?= $v['request_count'] > 1 ? 's' : '' ?> for this item
                                                <?
                                            } else {
                                                ?>
                                                No requests on this item yet
                                                <?
                                            }
                                            ?>
                                        </div>
                                    </a>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>
        </div>

        <?
        if ($requested_count) {
            ?>
            <div class="row fs-block">
                <div class="col">
                    <h4>
                        Requested Items
                        <span class="text-sm">(<a name="<?= ('watchlist') ?>"
                                                  href="<?= (APP_URL) ?>my_freestuff/listings/<?= ('watchlist') ?>">View older requests</></a>
                            )</span>
                    </h4>

                    <div class="container-fluid px-0">
                        <div class="row no-gutters">
                            <?
                            if ($requested_listings) {
                                foreach ($requested_listings as $k => $v) {
                                    $tmp_id = $v["listing_id"];
                                    $tmp_title = preg_replace("/,([^\s])/", ", $1", $v['title']);
                                    $tmp_description = $v["description"];
                                    $temp_img = new FileHelper('listing_images', $tmp_id);
                                    $tmp_img = $temp_img->getImagePathFromTag("most_recent_upload", 320, 320);
                                    $tmp_url = seoFriendlyURLs($tmp_id, "listing", FALSE, $tmp_title);
                                    $extra_class = $k == 2 ? 'd-none d-lg-block' : ($k == 1 ? 'd-none d-md-block' : '');
                                    ?>
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 listing-item-col <?= $extra_class ?>">
                                        <a class='listing-item listing-item-home' href="<?= ($tmp_url) ?>">
                                            <div class='pic_bit'>
                                                <img src='<?= ($tmp_img) ?>' alt='<?= ($tmp_title) ?>'
                                                     title='<?= ($tmp_title) ?>'/>
                                            </div>
                                            <div class="listing-body">
                                                <h5 class="listing-title"><?= ($tmp_title) ?></h5>
                                            </div>
                                        </a>
                                    </div>
                                <? }
                            } else {
                                ?>
                                <div class="col-12">
                                    <p>You have no recently requested listings.</p>
                                </div>
                                <?
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?
        }
        ?>

        <div class="row">
            <div class="col">
                <h4>
                    Delete My Account
                </h4>
                <a class="btn btn-danger btn-mobile" href="<?= APP_URL ?>account/delete">Delete My Account</a>
            </div>
        </div>
    </div>
</div>
