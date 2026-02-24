<form method="post" action="<?=(APP_URL)?>listing/save/<?=($listing->listing_id)?>" id="form-listing" class="form-horizontal">
	<input type="hidden" name="pa" value="update" />
	<input type="hidden" name="listing_id" value="<?=($listing->listing_id)?>" />

	<fieldset>
		<div class="row-fluid">
			<div class="widget">
			    <div class="well">
					<div class="control-group">
						<label class="control-label">ID</label>
						<div class="controls">
							<?=($listing->listing_id)?>

							<?
							$tmp_url = SITE_URL . seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title);
							?>
							(<a target="_blank" href="<?=($tmp_url)?>">Front end view</a>)
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Image</label>
						<div class="controls">
							<?
							$fh = new FileHelper('listing_images', $listing->listing_id);
							$tmp_img = $fh->getImagePathFromTag("most_recent_upload", 120, 120);
							?>
							<img src="<?=($tmp_img)?>" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Title</label>
						<div class="controls">
							<input class="span12" type="text" name="title" value="<? h($listing->title)?>" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Description</label>
						<div class="controls">
							<textarea class="span12" name="description"><? h($listing->description)?></textarea>
						</div>
					</div>
                    <div class="control-group">
                        <label class="control-label">Closest District</label>
                        <div class="controls">
                            <select class="form-control" name="district_id">
                                <option>Please Select</option>
                                <? foreach (District::getAllNested() as $region_name => $districts) {
                                    foreach ($districts as $district_id => $district_name) {
                                        echo FormHelper::option(District::display($district_id), $district_id,$listing->district_id);
                                    }
                                }?>
                            </select>

                        </div>
                    </div>

					<div class="control-group">
						<label class="control-label">Listing Type</label>
						<div class="controls">
							<input name='listing_type' type="radio" <?=(radioValue('free', $listing->listing_type))?> /> Free
							<br />
							<input name='listing_type' type="radio" <?=(radioValue('wanted', $listing->listing_type))?> /> Wanted
						</div>
					</div>

                    <div class="control-group">
                        <label class="control-label">Status</label>
                        <div class="controls">
                            <select class="form-control" name="listing_status">
                                <option>Please Select</option>
                                <? foreach (Listing::$all_statuses as $status) {
                                    echo FormHelper::option(ucfirst($status), $status,$listing->listing_status);
                                }?>
                            </select>
                        </div>
                    </div>

					<div class="control-group">
						<label class="control-label">User</label>
						<div class="controls">
							<b><?=($listing->user_firstname)?></b>

							<?
							$tmp_url = SITE_URL . seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title);
							?>
							(<a target="_blank" href="<?=(APP_URL)?>user/edit/<?=($listing->user_id)?>">View user</a>)
							<br />
							<?=($user->email)?>
							<br />
							<?=(District::display($listing->district_id))?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Original listing date</label>
						<div class="controls">
							<?=(DateHelper::display($listing->original_listing_date, TRUE, TRUE))?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Listing date</label>
						<div class="controls">
							<?=(DateHelper::display($listing->listing_date, TRUE, TRUE))?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Last updated</label>
						<div class="controls">
							<?=(DateHelper::display($listing->last_updated, TRUE, TRUE))?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Visits</label>
						<div class="controls">
							<?=($listing->visits)?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Ip address</label>
						<div class="controls">
							<?=($listing->ip_address)?>
						</div>
					</div>

					<div class="form-actions align-right">
						<a class="btn" href="<?=(APP_URL)?>listing">Cancel</a>
						<button class="btn btn-info" type="submit">Save</button>
					</div>

				</div>
			</div>
		</div>
	</fieldset>
</form>

<script type='text/javascript'>
	$(document).ready(function(){
		$('#form-listing').formTools2({
			onSuccess: function () {
				document.location = '<?=(APP_URL)?>listing/edit/<?=($listing->listing_id)?>';
			}
		});
	});
</script>
