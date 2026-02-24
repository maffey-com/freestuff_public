<?php
/**
 * Created by PhpStorm.
 * User: maggie
 * Date: 10/10/2016
 * Time: 5:06 PM
 */
class ConversationThumbnailHandler {
    public static function display($div_class, array $listings, $limit, $is_icon_clickable = FALSE,  $is_overlap = FALSE, $max_height_main_desktop = NULL, $max_height_main_tablet = NULL) {
        $max_height_main_desktop = empty($max_height_main_desktop) ? 120 : $max_height_main_desktop;
        $max_height_main_tablet = empty($max_height_main_tablet) ? 90 : $max_height_main_tablet;

        $max_height_extra_desktop = $is_overlap ? (int)$max_height_main_desktop * .42 : $max_height_main_desktop;
        $max_height_extra_tablet = $is_overlap ? (int)$max_height_main_tablet * .42 : $max_height_main_tablet;

        $listings = array_slice($listings, 0, $limit);
        ?>
        <div class="html-listing_thumbs <?=($div_class)?>">
            <?
            $thumb_z_index = 3;

            foreach ($listings as $_listing_index => $_listing) {
                $_listing_id = $_listing['listing_id'];

                if (empty($_listing_index)) {
                    $_thumb_h = $_thumb_w = $max_height_main_desktop;

                } else {
                    $_thumb_h = $_thumb_w = $max_height_extra_desktop;
                }

                $_fh_img = new FileHelper('listing_images', $_listing_id);
                $_img_thumbnail = $_fh_img->getImagePathFromTag("most_recent_upload", $_thumb_w, $_thumb_h);

                $_listing_seo = seoFriendlyURLs($_listing_id, "listing", FALSE, $_listing['title']);

                $_is_lister = ($_listing['is_lister'] == 'y');

                $_img_classes = [];
                $_img_classes[] = 'rounded-circle thumb';
                $_img_classes[] = $_listing_index ? 'thumb-more' : '';
                $_img_classes[] = $_is_lister ? 'thumb-lister' : '';

                if ($is_icon_clickable) {
                    $_img_title = '';
                    $_a_title = 'Link here to view the listing for ' . clean($_listing['title']) . ' [ID: ' . $_listing_id . ']';

                } else {
                    $_img_title = $_is_lister ? 'You listed: ' : 'You requested: ';
                    $_img_title .= clean($_listing['title']) . ' [ID: ' . $_listing_id . ']';
                    $_img_title = 'title="' . $_img_title . '"';
                }
                $_listing_image = '<img ' . $_img_title . ' src="' .  ($_img_thumbnail) . '" class="' . implode(" ", $_img_classes) . '" style="left: ' . ((int)$max_height_main_desktop * .6 + (int)(.60 * $_thumb_w * ($_listing_index-1))) . 'px; z-index:' . ($_listing_index) . '" />';
                if ($is_icon_clickable) {
                    ?>
                    <a class="d-inline-block" title="<?=($_a_title)?>" href="<?=($_listing_seo)?>"><?=($_listing_image)?></a>
                    <?
                } else {
                    echo $_listing_image;
                }
                $thumb_z_index--;
            }
            ?>
        </div>

        <style>
            .<?=($div_class)?>.html-listing_thumbs {
                position: relative;
                width: 100%;
                <?
                if ($is_overlap) {
                    ?>
                    height: <?=($max_height_main_desktop)?>px;
                    <?
                }
                ?>
            }

            .<?=($div_class)?> .thumb {
                <?
                if ($is_overlap) {
                    ?>
                    display:block;
                    position:absolute;
                    max-width: 100% !important;
                    <?
                } else {
                    ?>
                    display: inline-block;
                    <?
                }
                ?>
                border: 3px solid var(--fs-secondary);
                top: 0px;
                left: 0;
                width:<?=($max_height_main_desktop)?>px;
            }

            .<?=($div_class)?> .thumb-lister {
                border: 3px solid var(--fs-primary);
            }


            .<?=($div_class)?> .thumb-more {
                <?
                if ($is_overlap) {
                    ?>
                    top: auto;
                    <?
                }
                ?>
                bottom: 0px;
                /*border: 2px solid #fff !important;*/
                width: <?=($max_height_extra_desktop)?>px;
            }

            @media all and (max-width: 768px) {
                .<?=($div_class)?> .thumb {
                    width: <?=($max_height_main_tablet)?>px;
                }

                <?
                if ($is_overlap) {
                    ?>
                    .<?=($div_class)?>  .thumb-more {
                        /*display: none !important;*/
                        bottom: 15px !important;
                        left: 20px !important;
                        width: <?=($max_height_extra_tablet)?>px !important;
                    }

                    .<?=($div_class)?> .thumb-more:last-child {
                        left: 40px !important
                    }
                    <?
                }
                ?>
            }

            @media all and (max-width: 400px) {
                .<?=($div_class)?> .thumb {
                    width:100%;
                }

                .<?=($div_class)?> .thumb-more {
                    display: none;

                }
            }
        </style>
        <?php
    }
}