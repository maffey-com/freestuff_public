<div class="container">
    <div class='row'>
        <?php if (count($listings->data) == 0) {
            ?>
            <div class="col">
                <p class="p-3 bg-light">No results found! Try searching again.</p>
            </div>
            <?php
        } else {

            foreach ($listings->data as $listing) {
                if (SESSION_USER_ID && !UserBlocked::canSeeListing(SESSION_USER_ID, $listing["user_id"])) { // skip blocked users
                    continue;
                }
                $row_listing_id = $listing['listing_id'];
                $row_title = $listing['title'];
                $row_url = seoFriendlyURLs($row_listing_id, "listing", FALSE, $row_title);

                $row_is_wanted = ($listing['listing_type'] == 'wanted');
                $row_is_my_listing = ($listing['user_id'] == SESSION_USER_ID);

                $temp_img = new FileHelper('listing_images', $row_listing_id);
                $thumbnail = $temp_img->getImagePathFromTag("most_recent_upload", 320, 320); ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 listing-item-col">
                    <a class='listing-item listing-item-home text-left' href="<?= ($row_url) ?>">
                        <div class='pic_bit'>
                            <img src='<?= ($thumbnail) ?>' alt='<?= ($row_title) ?>' title='<?= ($row_title) ?>'/>
                        </div>
                        <div class="listing-body">
                            <?php
                            if ($row_is_wanted || $row_is_my_listing) {
                                ?>
                                <div class="h6 mb-1">
                                    <?php
                                    if ($row_is_wanted) {
                                        ?>
                                        <span class="badge badge-danger mr-1">Wanted</span>
                                        <?
                                    }

                                    if ($row_is_my_listing) {
                                        ?>
                                        <span class="badge badge-warning">My Listing</span>
                                        <?
                                    }
                                    ?>
                                </div>
                                <?
                            }
                            ?>
                            <h5 class="title text-truncate"><?= ($row_title) ?></h5>
                            <div>
                                <b>Listed By:</b> &nbsp;<?= ($listing['firstname']) ?>
                            </div>
                        </div>
                        <div class="listing-footer text-muted font-italic"><?= District::display2($listing['district_id']) ?></div>
                    </a>
                </div>
                <?
            }
        }
        ?>
    </div>
    <?
    require('templates/common_pager_bottom.php');
    ?>
</div>
