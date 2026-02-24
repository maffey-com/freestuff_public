<div class="container">
    <div class="row fs-block">
        <div class="col-12 col-md-6 text-center pb-2 pb-md-0 text-md-left fs-block">
            <?
            // Get image URLs
            $temp_img = new FileHelper('listing_images', $listing->listing_id);
            $thumbnail = $temp_img->getImagePathFromTag("most_recent_upload", 600, 600);
            $ih = $temp_img->getImageHelperFromTag("most_recent_upload");
            $ih->setTargetWidthAndHeight('1200', '1200', 'thumbnail');
            $fullsize_img = $temp_img->cacheImageFromImageHelper($ih);
            ?>
            <a href="<?= ($fullsize_img) ?>" target="_new" style="display: inline-block;"><img class="listing_image" src="<?= ($thumbnail) ?>"/></a>
        </div>

        <div class="col-12 col-md-6">
            <form method="post" id="request_form" action="<?=(APP_URL)?>request/process_request/<?=($listing->listing_id)?>">

                <div class="form-group">
                    <label for="request_comment">Comment to lister</label>
                    <textarea name="request_comment" id="request_comment" class="text_limit_box form-control" maxlength="200" placeholder="" data-countdown-label="#request_comment_count"></textarea>
                    <small class="text_limit_countdown text-muted" id="request_comment_count">200 characters left</small>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn primary" value="Send My Offer"/>
                </div>
            </form>
        </div>
    </div>

</div>
