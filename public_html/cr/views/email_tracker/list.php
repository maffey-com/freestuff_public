<form class="form-inline" method='GET' action="<?=(APP_URL)?>email_tracker/filter_list">
    <div class="well">
        <div class="control-group">
            <input class="input-large" type='text' name='filter_to_address' value='<?= ($filter->to_address) ?>'
                   placeholder="Search (e.g. To address)"/>

            <select class="span4" name="filter_template">
                <?
                echo FormHelper::option('All templates', '', $filter->template);
                foreach (EmailTemplate::getAllAvailableEmailTemplates() as $tmp_template) {
                    echo FormHelper::option($tmp_template, $tmp_template, $filter->template);
                }
                ?>
            </select>
            <button class="btn btn-info" type="submit">Filter</button>
            <a class="btn btn-danger"  style="float:right" href="<?= (APP_URL) ?>email_tracker/truncate">Truncate</a>
        </div>
    </div>
</form>

<div class="table-bordered table-striped table-hover" id="list-users">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="120"><?= ($dw_email->sortableColumnHeading("date_sent", "Sent")) ?></th>
                <th width="250"><?= ($dw_email->sortableColumnHeading("to_address", "To address")) ?></th>
                <th><?= ($dw_email->sortableColumnHeading("template_name", "Template")) ?></th>
                <th><?= ($dw_email->sortableColumnHeading("email_subject", "Subject")) ?></th>
                <th width="60"><?= ($dw_email->sortableColumnHeading("recipient_user_id", "Recipient")) ?></th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_email->data as $row) {
            $row_tracker_id = $row['email_tracker_id'];
            ?>
            <tr data-tracker_id="<?= ($row_tracker_id) ?>">
                <td>
                    <?= ($row['date_sent']) ?>
                </td>
                <td>
                    <?= ($row['to_address']) ?>
                </td>
                <td>
                    <?= ($row['template_name']) ?>
                </td>
                <td>
                    <?= ($row['email_subject']) ?>
                </td>
                <td>
                    <?= ($row['recipient_user_id']) ?>
                </td>
                <td>
                    <a class="btn dynamic-bootbox" href="<?=(APP_URL)?>email_tracker/view/<?=($row_tracker_id)?>"><i class="ico-eye-open"></i> View</a>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
	<?=($dw_email->displayPaging())?>
</div>