
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
            <a href="<?= ($fullsize_img) ?>" target="_blank" class="d-block"><img class="listing_image" src="<?=($thumbnail)?>" /></a>
        </div>

        <div class="col-12 col-md-6">
            <p>
                Items on Freestuff are <b><u>pickup only</u></b> unless the lister has stated otherwise.
            </p>



            <form method="post" id="request_form" action="<?=(APP_URL)?>request/process_request/<?=($listing->listing_id)?>">
                <div class="form-group">
                    <label for="request_comment" class="font-weight-bold">Message to lister</label>
                    <textarea name="request_comment" id="request_comment" class="form-control text_limit_box" data-countdown-label="#request_comment_counter" maxlength="500" rows="8"
                              placeholder="Please be specific about when you are able to collect this item and what you plan to do with it."></textarea>
                    <small class="text-muted text_limit_countdown" id="request_comment_counter">500 characters left</small>
                </div>


                <div class="form-group form-check">
                    <input type="checkbox" name="confirm_collect" id="confirm_collect" class="form-check-input"/>
                    <label for="confirm_collect" class="form-check-label">Please confirm you will be able to pick up the item from <b><?=District::display2($listing->district_id)?></b>.
                        <em class="text-danger"> If the lister offers you the item and you fail to collect it, you will likely get negative <a href="/page/feedback">feedback</a>.</em>
                    </label>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="confirm_credit" id="confirm_credit" class="form-check-input"/>
                    <label for="confirm_credit" class="form-check-label">Requesting this item will use 1 request credit.  <br/>You have <?=StringHelper::singularOfPlural($user->request_credit,"credit","credits")?> remaining.</em>
                    </label>
                </div>



                <div class="form-group">
                    <input type="submit" class="btn primary" value="Send My Request"/>
                </div>
            </form>
        </div>
    </div>
</div>
