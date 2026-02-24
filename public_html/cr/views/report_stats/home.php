<div class="form-horizontal">
    <div class="row-fluid">
        <div class="widget">
            <div class="well">
                <div class="control-group">
                    <label class="control-label">Total users</label>
                    <div class="controls"><?= ($no_of_users) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">Current listings</label>
                    <div class="controls"><?= ($no_current_listings) ?></div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="table-bordered table-striped table-hover" id="list-record">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="120">Date</th>
                <th>Views</th>
                <th>Web Views</th>
                <th>Mobile Views</th>
                <th>New listing</th>
                <th>New Wanted</th>
                <th>New member</th>
                <th>Email validations</th>
                <th>Mobile validations</th>
                <th>Contacts</th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($report as $row_day => $data) {
            $row_new_listings = paramFromHash('new_listings', $data, array());

            ?>
            <tr >
                <td>
                    <?= (date('D d M', strtotime($row_day))) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("listing_views", $data)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("web_views", $data)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("mobile_views", $data)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("free", $row_new_listings)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("wanted", $row_new_listings)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("new_users", $data)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("email_validated", $data)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("mobile_validated", $data)) ?>
                </td>
                <td style="text-align: right">
                    <?= ((int)paramFromHash("contacts", $data)) ?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>





