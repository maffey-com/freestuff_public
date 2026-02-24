<form class="form-inline" method='GET' action="<?=(APP_URL)?>report/filter_list">
    <div class="well">
        <div class="control-group">
            From: <input class="input-small datepicker_2" type='text' name='filter_from_date' value='<?= ($filter->from_date) ?>' placeholder="From date (e.g. DD/MM/YYYY)"/>
            To: <input class="input-small datepicker_2" type='text' name='filter_to_date' value='<?= ($filter->to_date) ?>' placeholder="To date (e.g. DD/MM/YYYY)" />

            <select class="span4" name="filter_old">
                <?
                echo FormHelper::option('Show new only', 'n', $filter->old);
                echo FormHelper::option('Show all', 'y', $filter->old);
                ?>
            </select>
            <button class="btn btn-info" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-bordered table-striped table-hover" id="list-report">
    <table class="table table-striped" style="width:100%;">
        <thead>
            <tr role="row">
                <th width="35"><?=($dw_report->sortableColumnHeading("report_id", "ID")) ?></th>
                <th width="115"><?=($dw_report->sortableColumnHeading("report_date", "Date")) ?></th>
                <th width="200"><?=($dw_report->sortableColumnHeading("reporter_firstname", "Reporter")) ?></th>
                <th width="250"><?=($dw_report->sortableColumnHeading("report_comment", "Comment")) ?></th>
                <th width="200"><?=($dw_report->sortableColumnHeading("lister_firstname", "Lister")) ?></th>
				<th><?=($dw_report->sortableColumnHeading("listing_title", "Title"))?></th>
				<th width="50"><?=($dw_report->sortableColumnHeading("listing_type", "Type"))?></th>
				<th width="75"><?=($dw_report->sortableColumnHeading("listing_status", "Listing status"))?></th>
				<th width="40"><?=($dw_report->sortableColumnHeading("status", "Status"))?></th>
				<th width="115"><?=($dw_report->sortableColumnHeading("freestuff_action_date", "Status last changed"))?></th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_report->data as $row) {
            $row_report_id = $row['report_id'];
			$row_lister_user_id = (int)$row['lister_user_id'];
			$row_reporter_user_id = (int)$row['reporter_user_id'];
			$row_listing_id = (int)$row['listing_id'];
			$row_listing_status    = $row['listing_status'];
			$row_status = $row['status'];

            ?>
            <tr data-report_id="<?= ($row_report_id) ?>">
                <td>
                    <a target="_blank" href="<?=(APP_URL)?>report_request/view/<?=($row_listing_id)?>"><?= ($row_report_id) ?></a>
                </td>
                <td>
                    <?= ($row['report_date']) ?>
                </td>
                <td>
                    <?
					if (!empty($row_reporter_user_id)) {
						?>
						<?=($row["reporter_firstname"])?>
						<br />
						<?=District::resolveRegionName($row["reporter_district_id"])?>
						<br />
						(<a target="_blank" href="<?=(APP_URL)?>user/edit/<?=($row_reporter_user_id)?>">View user</a>)
						<br />
						(<a target="_blank" href="<?=(APP_URL)?>user/history/<?=($row_reporter_user_id)?>" class="dynamic-bootbox">View history</a>)
						<br />
						(<a href="<?=(APP_URL)?>report/email/<?=($row_report_id)?>?type=reporter" class="dynamic-bootbox">Send email</a>)
						<br />
							(<a href="<?=(APP_URL)?>user/ban_reporter/<?=($row_reporter_user_id)?>" onClick="return confirm('Are you sure you want to ban this user?')">Ban this user</a>)
						<?
					}
					?>
                </td>
				<td>
					<?=(nl2br($row['report_comment']))?>
				</td>
                <td>
                    <?=($row["lister_firstname"])?>
					<br />
                    <?=District::resolveRegionName($row["lister_district_id"])?>
					<br />
					(<a target="_blank" href="<?=(APP_URL)?>user/edit/<?=($row_lister_user_id)?>">View user</a>)
					<br />
					(<a class="dynamic-bootbox" href="<?=(APP_URL)?>user/history/<?=($row_lister_user_id)?>">View history</a>)
					<br />
					(<a class="dynamic-bootbox" href="<?=(APP_URL)?>report/email/<?=($row_report_id)?>?type=lister">Send email</a>)
						<br />
						(<a href="<?=(APP_URL)?>user/ban_reporter/<?=($row_lister_user_id)?>" onClick="return confirm('Are you sure you want to ban this user?')">Ban this user</a>)
                </td>
                <td>
                    <?=($row["listing_title"])?>
                    <br />
                    (<a target="_blank" href="<?=(APP_URL)?>listing/view_full_details/<?=($row_listing_id)?>">View listing</a>)
					<br />
					(<a target="_blank" href="<?=(APP_URL)?>listing/edit/<?=($row_listing_id)?>">Edit listing</a>)
					<br />
					(<a target="_blank" href="../view?listing_id=<?=($row_listing_id)?>">Front end view</a>)
					<?
					if (in_array($row_listing_status, array('available', 'reserved'))) { ?>
						<br />
						(<a href="<?=(APP_URL)?>report/delist/<?=($row_report_id)?>" onClick="return confirm('Are you sure you want to remove this item?')">Remove</a>)
						<?
					}
					?>
                </td>
				<td>
					<?
					if ($row['listing_type'] == 'free') {
						?>
						Free
						<br />
						(<a href="<?=(APP_URL)?>report/wanted/<?=($row_report_id)?>">Change to wanted item</a>)
						<?
					} else {
						?>
						Wanted
						<?
					}
					?>
				</td>
				<td>
					<?=($row_listing_status)?>
				</td>
				<td>
					<?
					$tmp_title = ($row_status == 'NEW') ? 'Change to close' : 'Change to NEW';
					?>
					<a href="<?=(APP_URL)?>report/update_status/<?=($row_report_id)?>" title="<?=($tmp_title)?>"><?=($row_status)?></a>
				</td>
				<td>
					<?=($row["freestuff_action_date"])?>
				</td>
            </tr>
        <? } ?>
        </tbody>
    </table>
	<?=($dw_report->displayPaging())?>
</div>
