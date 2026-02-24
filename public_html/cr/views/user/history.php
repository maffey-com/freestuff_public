<?php
TemplateHandler::setTableCaptionText($user->firstname . ' - ' . $user->email . ' [ID: ' . $user->user_id . ']');
TemplateHandler::echoTableCaption();
?>
<div class="form-horizontal" id="history-details">
    <div class="row-fluid">
        <div class="well">
            <div class="control-group">
                <label class="control-label">Mobile</label>
                <div class="controls"><?=($user->mobile)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Location</label>
                <div class="controls"><?=($user->location)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Details</label>
                <div class="controls">
                    <i class="ico-thumbs-up"></i><?=($user->thumbs_up?:0)?> &nbsp;&nbsp; <i class="ico-thumbs-down"></i><?=($user->thumbs_down?:0)?><br />
                    Has been reported <?=$times_reported?> times<br />
                    Has reported <?=$reported_times?> listings
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-bordered table-striped table-hover" id="list-history" style="max-height:285px; overflow-y: scroll">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="75"><?=($dw_user->sortableColumnHeading("type", "Type")) ?></th>
                <th><?=($dw_user->sortableColumnHeading("date", "Date")) ?></th>
                <th width="120"><?= ($dw_user->sortableColumnHeading("item", "Item")) ?></th>
                <th><?= ($dw_user->sortableColumnHeading("additionalInfo", "Additional Info")) ?></th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_user->data as $row) {
            ?>
            <tr>
                <td>
                    <?= ($row['type']) ?>
                </td>
                <td>
                    <?= ($row['date']) ?>
                </td>
                <td>
                    <a href="<?=(SITE_URL)?>cr/listing/view_full_details/<?=($row['listing_id'])?>" target="_blank"><?=($row["item"])?></a><br />
                    (<a href="<?=(SITE_URL)?>view?listing_id=<?=($row['listing_id'])?>" target="_blank">View on front end</a>)
                </td>
                <td>
                    <? if ($row['type'] == 'Thumb') {
                        if ($row['additionalInfo'] == 'u') {
                            echo "<i class=\"ico-thumbs-up\"></i>";
                        } elseif ($row['additionalInfo'] == 'd') {
                            echo "<i class=\"ico-thumbs-down\"></i>";
                        } else {
                            echo '';
                        }
                    } else {
                    echo nl2br($row['additionalInfo']);
                    }?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>
<?=($dw_user->displayPaging())?>
<style>
    #history-details .control-group {
        padding: 8px 6px;
    }
</style>