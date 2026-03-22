<div class="container">
    <div class="row fs-page-header" id="home-regions">
        <div class="col-12 d-md-flex justify-content-center text-center">
            <h1>Latest Listings </h1>

            <div class="dropdown header-browse">
                <div class="btn home-dropdown dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                     aria-expanded="false">All Regions
                </div>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <?
                    $regions = Listing::getRegionsWithCount();
                    $browsing_category = TemplateHandler::getBrowseCategoryName();

                    foreach (District::$regions as $region_name) {
                        $count = (int)paramFromHash($region_name, $regions);

                        $row_style = ($browsing_category == $region_name) ? ' active-region' : '';
                        ?>
                        <li class="list-group-item">
                            <a class="dropdown-item d-flex justify-content-between align-items-center <?= ($row_style) ?>"
                               href="browse/by-region/<?= ($region_name) ?>"><span class=""><?= ($region_name) ?></span>&nbsp;<span
                                        class="badge badge-pill badge-fs"><?= ($count) ?></span></a>
                        </li>
                    <? } ?>
                </ul>
            </div>

        </div>
    </div>

    <div class="row" id="home-listings">
        <?
        foreach ($random_approved_items as $k => $v) {
            if (SESSION_USER_ID && !UserBlocked::canSeeListing(SESSION_USER_ID, $v["user_id"])) { // skip blocked users
                continue;
            }

            $tmp_id = $v["listing_id"];
            $tmp_title = preg_replace("/,([^\s])/", ", $1", $v['title']);
            $tmp_description = $v["description"];
            $temp_img = new FileHelper('listing_images', $tmp_id);
            $tmp_img = $temp_img->getImagePathFromTag("most_recent_upload", 320, 320);
            $tmp_url = seoFriendlyURLs($tmp_id, "listing", false, $tmp_title); ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 listing-item-col" title="<?= ($tmp_title) ?>">
                <div class='listing-item listing-item-home text-left'>
                    <div class='pic_bit'>
                        <img src='<?= ($tmp_img) ?>' alt='<?= (htmlentities($tmp_title, ENT_QUOTES)) ?>'
                             title='<?= (htmlentities($tmp_title, ENT_QUOTES)) ?>'/>
                    </div>
                    <div class="listing-body">
                        <h5 data-href="<?= ($tmp_url) ?>" style="cursor:pointer; text-decoration: underline" class="title text-truncate"><?= ($tmp_title) ?></h5>
                        <span>
                            <b>Listed By:</b> &nbsp;<span class="user-listings-link" data-href="<?= APP_URL ?>user/all_listings/<?= ($v['user_id']) ?>" style="cursor:pointer; color: #007bff; text-decoration: underline;"><?= h($v['firstname']) ?></span>

                            <?
                            $givenCount = Listing::getGivenCount($v['user_id']);
                            if ($givenCount >= 25) { $giverBadge = 'Gold Giver'; $giverColor = '#FFD700'; }
                            elseif ($givenCount >= 10) { $giverBadge = 'Silver Giver'; $giverColor = '#D8D8D8'; }
                            elseif ($givenCount >= 3) { $giverBadge = 'Bronze Giver'; $giverColor = '#E8A96A'; }
                            else { $giverBadge = null; $giverColor = null; }

                            if ($giverBadge) { ?>
                                <br>
                                <span class="badge" style="background-color: <?= $giverColor ?>; color: #333;"><?= $giverBadge ?></span>
                            <? } ?>
                        </span>
                    </div>
                    <div class="listing-footer text-muted font-italic"><?= District::display2($v['district_id']) ?></div>
                </div>
            </div>
            <?
        }
        ?>
    </div>
</div>
