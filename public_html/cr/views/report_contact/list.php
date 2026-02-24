<form class="form-inline" method='GET' action="<?=(APP_URL)?>report_contact/filter_list">
    <div class="well">
        <div class="control-group">
            <select class="span4" name="filter_status">
				<option value="">All</option>
				<?php
				echo option('New', 'New only', $filter->status);
				echo option('Closed', 'Closed only', $filter->status);
				?>
			</select>
            <button class="btn btn-info" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-bordered table-striped table-hover" id="list-contact">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="70"><?= ($dw_contact->sortableColumnHeading("id", "ID")) ?></th>
				<th width="250"><?= ($dw_contact->sortableColumnHeading("name", "Name")) ?></th>
				<th>Enquiry</th>
				<th>Reply</th>
                <th width="120"><?= ($dw_contact->sortableColumnHeading("contact_date", "Contact date")) ?></th>
                <th width="120"><?= ($dw_contact->sortableColumnHeading("freestuff_action_date", "Action date")) ?></th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_contact->data as $row) {
            $row_id = $row['id'];
			$row_status = $row['status'];
			$row_email = $row['email'];
            ?>
            <tr data-contact_id="<?= ($row_id) ?>">
                <td>
                    <?= ($row_id) ?>
                    <br />
                    - <b><?= ($row_status) ?></b>
                </td>
                <td>
                    <?=($row["name"])?>
					<br />
					<a type="View user info" target="_blank" href="<?=(APP_URL)?>user/?filter_name=<?=($row_email)?>"><?=($row_email)?></a>
					<br />
					<?=($row["phone"])?>
                </td>
                <td>
                    <?= ($row['enquiry']) ?>
                    <br />
                    - <b>IP address: <?= ($row['ip_address']) ?></b>
                </td>
                <td>
                    <?= ($row['reply']) ?>
                </td>
                <td>
                    <?= ($row['contact_date']) ?>
                </td>
                <td>
                    <?= ($row['freestuff_action_date']) ?>
                </td>
                <td>
					<?
					if ($row_status == 'New') {
						?>
						<a class="btn" href="<?=(APP_URL)?>report_contact/view/<?= ($row_id) ?>"><i class="ico-pencil"></i> Reply</a>
						<a class="btn" href="<?=(APP_URL)?>report_contact/close/<?= ($row_id) ?>" onclick="return confirm('Are you sure you want to close this contact?');"><i class="ico-remove"></i> Close</a>
						<?
					} else {
						?>
						<a class="btn" href="<?=(APP_URL)?>report_contact/view/<?= ($row_id) ?>"><i class="ico-eye-open"></i> View</a>
                        <a class="btn" href="<?=(APP_URL)?>report_contact/unclose/<?= ($row_id) ?>" onclick="return confirm('Are you sure you want to re-open this contact?');">Reopen</a>
                        <?
					}
					?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
	<?=($dw_contact->displayPaging())?>
</div>
