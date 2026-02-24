<?php
TemplateHandler::setTableCaptionText($user->firstname . ' - ' . $user->email . ' [ID: ' . $user->user_id . ']');
TemplateHandler::echoTableCaption();
?>
<div class="table-bordered table-striped table-hover" id="list-history" style="max-height:285px; overflow-y: scroll">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="75">Date</th>
                <th>Item</th>
                <th width="120">Comment</th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($reported_listings as $row) {
            ?>
            <tr>
                <td>
                    <?= ($row['report_date']) ?>
                </td>
                <td>
                    <a href="<?=(SITE_URL)?>cr/listing/view_full_details/<?=($row['listing_id'])?>" target="_blank"><?= ($row['title']) ?></a>
                </td>
                <td>
                    <?= ($row['report_comment']) ?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>