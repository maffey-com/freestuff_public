<form class="form-inline" method='GET' action="<?=(APP_URL)?>listing">
    <div class="well">
        <div class="control-group">
            <input class="input-large" type='text' name='filter_search' value='<?=($filter->search)?>' placeholder="Search (e.g. title, description, listing ID)" />
			<select name="filter_listing_status">
				<?
                echo FormHelper::option('Please select', '', $filter->listing_status);
                foreach(Listing::$all_statuses as $status) {
                    echo FormHelper::option(ucfirst($status), $status, $filter->listing_status);
                }
				?>
			</select>
            <button class="btn btn-info" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-bordered table-striped table-hover" id="list-listings">
    <table class="table table-striped">
		<thead>
            <tr role="row">
				<th><?=($dw_listing->sortableColumnHeading("listing_id", "ID"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("user_firstname", "Firstname"))?></th>
				<th>Location</th>
				<th><?=($dw_listing->sortableColumnHeading("listing_type", "Type"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("title", "Title"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("listing_status", "Listing Status"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("authorised", "Authorised"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("listing_date", "Listing date"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("original_listing_date", "Original Listing date"))?></th>
				<th><?=($dw_listing->sortableColumnHeading("visits", "Visits"))?></th>
				<th width="200">Action</th>
			</tr>
		</thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
			<?
			foreach ($dw_listing->data as $listing) {
				$row_listing_id = $listing['listing_id'];
				?>
				<tr data-listing_id="<?=($row_listing_id)?>">
					<td>
						<?=($row_listing_id)?>
					</td>
					<td>
						<?=($listing["user_firstname"])?>
					</td>
					<td>
						<?=District::display2($listing["district_id"])?>
					</td>
					<td>
						<?=(ucfirst($listing["listing_type"]))?>
					</td>
					<td>
						<?=($listing["title"])?>
						<br />
						(<a target="_blank" href="listing/view_full_details/<?=($row_listing_id)?>" target="_blank">View full details</a>)
                        <br />
						(<a target="_blank" href="listing_full_detail.php?listing_id=<?=($row_listing_id)?>" target="_blank">View extended details</a>)
					</td>
					<td>
						<?=ucfirst($listing["listing_status"])?>
					</td>
					<td>
						<?
						switch ($listing['authorised']) {
							case 'p':
								echo 'Pending';
								break;

							case 'y':
								echo 'Authorised';
								break;

							default:
								echo 'Rejected';
								break;
						}
						?>
					</td>
					<td>
						<?=($listing["listing_date"])?>
					</td>
					<td>
						<?=($listing["original_listing_date"])?>
					</td>
					<td>
						<?=($listing["visits"])?>
					</td>
					<td>
						<a class="btn btn-primary" href="<?=(APP_URL)?>listing/edit/<?=($row_listing_id)?>"><i class="ico-pencil"></i> Edit</a>
                    	<a target="_blank" class="btn" href="<?=(APP_URL)?>report_request/view/<?=($row_listing_id)?>"><i class="ico-eye-open"></i> View requests</a>
						<br />
						<button class="btn btn-danger action-reject" title="Unauthorise + disapprove listing"><i class="ico-thumbs-down"></i> Reject</button>
                    	<button class="btn btn-danger action-delete" title="Delete listing record from database"><i class="ico-trash"></i> Delete</button>
					</td>
				</tr>
		<?	}?>
		</tbody>
	</table>
</div>

<?=($dw_listing->displayPaging())?>

<?
require_once("views/listing/common_list_js.php");
