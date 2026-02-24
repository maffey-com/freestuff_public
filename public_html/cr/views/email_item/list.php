<form method='POST' action="<?=(APP_URL)?>email_item/get_html" onSubmit="submitToPopup(this)">
	<div class="widget">
		<button class="btn btn-info" type="submit">Get HTML</button>
	</div>

	<div class="table-bordered table-striped table-hover" id="list-listing">
		<table class="table table-striped">
			<thead>
				<tr role="row">
					<th>ID</th>
					<th>Date</th>
					<th>Region</th>
					<th>Item</th>
					<th>Image</th>
					<th>Age</th>
					<th>Visit</th>
				</tr>
			</thead>
			<tbody role="alert" aria-live="polite" aria-relevant="all">
			<?
			foreach ($items as $row_index => $row) {

				$row_listing_id = (int)paramFromHash('listing_id', $row);
				$row_title = $row['title'];
				$row_age = $row['dd'];
				$row_visits = $row['visits'];

				$row_checked = ($row_index < 12) ? " checked='checked'" : "";

				$row_img = new FileHelper('listing_images', $row_listing_id);
				$row_img_url = $row_img->getImagePathFromTag("most_recent_upload", 100, 100);
				?>
				<tr>
					<td>
						<?=($row_index+1)?>
					</td>
					<td>
						<input type="checkbox" name="listing_ids[]" value="<?=($row_listing_id)?>" <?=($row_checked)?> />
					</td>
					<td>
						<?=District::display($row['district_id'])?>
					</td>
					<td>
						<?=($row_title)?> [ID: <?=($row_listing_id)?>]

					</td>
					<td>
						<?
						if ($row_img_url) {
							?>
							<img src='<?=($row_img_url)?>' />
							<?
						}
						?>
					</td>
					<td>
						<?=($row_age)?>
					</td>
					<td>
						<?=($row_visits)?>
					</td>
				</tr>
				<?
			} ?>
			</tbody>
		</table>
	</div>

	<div class="widget">
		<button class="btn btn-info" type="submit">Get HTML</button>
	</div>
</form>

<script type="text/javascript">
	function submitToPopup(myForm) {
		var w = window.open('about:blank', 'Popup_Window', 'width=800,height=600,status=mp,resizable=yes,scrollbars=yes');
		myForm.target = 'Popup_Window';
		return true;
	}
</script>

<style>
	tr.special > td,
	tr.special > th {
		background-color: #FFEDBB !important;
	}
</style>
