<?php
/**
 * @var array $my_thumbs
 * @var User $u_requester
 * @var bool $is_thumb_clickable
 */
$my_thumb_for_requester = paramFromHash($u_requester->user_id, $my_thumbs, 'x');
$class_name = $is_thumb_clickable ? 'thumbs' : '';
$thumb_size = $is_thumb_clickable ? 'h3 mb-0' : '';
$wrapper_style = $is_thumb_clickable ? 'my-2 my-sm-0' : '';
?>
<div class="<?= ($wrapper_style) ?> d-sm-inline-block d-md-flex  align-items-center ml-auto ml-md-0">
<!--    <div class="mb-1 mb-md-0 mr-3 d-inline-block ">-->
<!--        --><?php //= (StringHelper::singularOfPlural($u_requester->user_listing_count, 'Give', 'Gives')) ?>
<!--        , --><?php //= (StringHelper::singularOfPlural($u_requester->user_request_count, 'Request', 'Requests')) ?>
<!--    </div>-->

    <div class="mb-0 mr-2 mt-sm-0 <?= ($class_name) ?> d-inline-block my_thumb_<?= $my_thumb_for_requester ?>"
         data-request_user_id="<?= ($u_requester->user_id) ?>">
        <span class="thumb-span mr-2">
            <span class="<?= ($thumb_size) ?>" style="line-height:1"><span class="fa thumb fa-thumbs-up"
                                                                           title="Thumb up"></span></span>
            <span class="thumbs-up"><?= $u_requester->thumbs_up ?></span>
        </span>
        <span class="thumb-span">
            <span class="<?= ($thumb_size) ?>" style="line-height:1"><span class="fa thumb fa-thumbs-down"
                                                                           title="Thumb down"></span></span>
            <span class="thumbs-down"><?= $u_requester->thumbs_down ?></span>
        </span>
    </div>
</div>