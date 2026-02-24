<div class="form-horizontal">
    <div class="row-fluid">
        <?php
            TemplateHandler::setTableCaptionText('Listing info');
            TemplateHandler::echoTableCaption();
        ?>

        <div class="well">
            <div class="control-group">
                <label class="control-label">Listing</label>
                <div class="controls"><?=($listing->title)?> [<a target="_blank" title="Edit listing" href="<?=(APP_URL)?>listing/edit/<?=($listing->listing_id)?>">ID: <?=($listing->listing_id)?></a>]</div>
            </div>
            <div class="control-group">
                <label class="control-label">Listing date</label>
                <div class="controls"><?=($listing->listing_date)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Listing type</label>
                <div class="controls"><?=($listing->listing_type)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Description</label>
                <div class="controls"><?=($listing->description)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Listing Status</label>
                <div class="controls"><?=($listing->listing_status)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Views</label>
                <div class="controls"><?=($listing->visits)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Requests</label>
                <div class="controls"><?=($listing->request_count)?></div>
            </div>
            <div class="control-group">
                <label class="control-label">IP Address</label>
                <div class="controls"><?=($listing->ip_address)?></div>
            </div>
        </div>
    </div>
    <br />
    <div class="row-fluid">
        <?php
            TemplateHandler::setTableCaptionText('Lister details');
            TemplateHandler::echoTableCaption();
        ?>
        <div class="well">
            <div class="control-group">
                <a class="btn" target="_blank" href="<?=(APP_URL)?>user/edit/<?=($user->user_id)?>">View user</a> <a class="btn dynamic-bootbox" target="_blank" href="<?=(APP_URL)?>/user/history/<?=($user->user_id)?>">View history</a>
                <a class="btn btn-danger" style="float:right;" href="<?=(APP_URL)?>user/ban_reporter/<?=($user->user_id)?>" onClick="return confirm('Are you sure you want to ban this user?')">Ban this user</a>
            </div>
            <div class="control-group">
                <label class="control-label">Name</label>
                <div class="controls"><?=$user->firstname?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Email</label>
                <div class="controls"><?=($user->email)?> - <?=!empty($user->email_validated)?'Validated ('.$user->email_validated.')':'Not validated'?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Mobile</label>
                <div class="controls"><?=($user->mobile)?> - <?=!empty($user->mobile_validated)?'Validated ('.$user->mobile_validated.')':'Not validated'?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Location</label>
                <div class="controls"><?=(District::display($listing->district_id))?></div>
            </div>
            <div class="control-group">
                <label class="control-label">Thumbs</label>
                <div class="controls">
                    <i class="ico-thumbs-up"></i><?=($user->thumbs_up?:0)?> &nbsp;&nbsp; <i class="ico-thumbs-down"></i><?=($user->thumbs_down?:0)?><br />
                    Has been reported <a class="dynamic-bootbox" target="_blank" href="<?=(APP_URL)?>listing/list_reported/<?=($user->user_id)?>"><?=$user->times_reported?></a> times<br />
                    Has reported <?=$user->reported_times?> listings
                </div>
            </div>
        </div>
    </div>
    <br />

    <div class="row-fluid">
        <?
            TemplateHandler::setTableCaptionText('Listings Reported');
            TemplateHandler::echoTableCaption();
        ?>
        <div class="well">
            <? if (count($reports)) {?>
            <table id="reports-table" style="width:100%;">
                <thead>
                    <tr>
                        <th width="80px">ID</th>
                        <th width="180px">User</th>
                        <th>Comment</th>
                        <th width="350px"></th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($reports as $reports_users) {
                        foreach ($reports_users as $report) { ?>
                            <tr>
                                <td><?= $report['report_id'] ?></td>
                                <td>
                                    <?= $report['firstname'] ?><br />
                                    <?=$report['email']?><br />
                                    (<a target="_blank" href="<?= (APP_URL) ?>user/history/<?= $report['user_id'] ?>" class="dynamic-bootbox">View history</a><br />
                                    (<a target="_blank" href="<?=(APP_URL)?>user/edit/<?=($report['user_id'])?>">View user</a>)

                                </td>
                                <td><?= $report['report_comment'] ?></td>
                                <td>
                                    <a class="btn dynamic-bootbox" href="<?=(APP_URL)?>report/email/<?=($report['report_id'])?>?type=reporter">Email reporter</a>
                                    <a class="btn dynamic-bootbox" href="<?=(APP_URL)?>report/email/<?=($report['report_id'])?>?type=lister">Email lister</a>
                                    <a class="btn btn-danger" href="<?=(APP_URL)?>user/ban_reporter/<?=($report['user_id'])?>" onClick="return confirm('Are you sure you want to ban this user?')">Ban the reporter</a>
                                </td>
                            </tr>
                        <?
                        }
                    }?>
                </tbody>
            </table>
            <?} else {?>
              <p>This listing has not been reported.</p>
            <?}?>
        </div>
    </div>
    <br />

    <div class="row-fluid">
        <?
            TemplateHandler::setTableCaptionText('Requests');
            TemplateHandler::echoTableCaption();
        ?>
        <div class="well">
            <? if ($listing->request_count > 0) {?>
            <table class="request-table" style="width:100%;">

                <thead>
                    <tr>
                        <th width="80px">ID</th>
                        <th width="130px">Date</th>
                        <th width="130px">User</th>
                        <th width="150px">User Location</th>
                        <th width="150px">IP</th>
                        <th>Request Log</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach ($requests as $request) {?>
                    <tr>
                        <td><?=$request['request_id']?></td>
                        <td><?=$request['request_timestamp']?></td>
                        <td><?=$request['user_firstname']?> [<a target="_blank" href="<?=(APP_URL)?>user/history/<?=$request['user_id']?>" class="dynamic-bootbox">View history</a>]</td>
                        <td><?=District::display($request['district_id'])?></td>
                        <td><?=$request['user_ip_address']?></td>
                        <td rowspan="2">
                        <? foreach ($request['message_history'] as $message) {
                            $message_class = $message['sender_user_id'] == $request["user_id"]?"requester":"lister"; ?>
                            <div class="message <?= ($message_class) ?>">
                                <i><?= ($message['timestamp']) ?></i><br />
                                <?= ($message['message']) ?>
                            </div>
                        <?} ?>
                        </td>
                    </tr>
                    <tr class="no-border">
                        <td colspan="5">
                            <? if (!empty($request['reported'])) { ?>
                            <table class="report-table">
                                <tbody>
                                    <? foreach ($request['reported'] as $report) { ?>
                                    <tr>
                                        <td>
                                            <strong>Reported</strong><br />
                                            <?=$report['report_comment']?>
                                        </td>
                                    </tr>
                                    <? } ?>
                                </tbody>
                            </table>
                            <? } ?>
                        </td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
            <?} else {?>
                <p>This listing has no requests.</p>
            <?}?>
        </div>
    </div>
    <br/>
</div>

<style>
    label {
        padding-top: 0 !important;
    }
    .received {
        text-align: left;
        background: #F2F2F2;
    }
    th {
        text-align: left;
        padding: 3px;
    }
    td {
        box-sizing: border-box;
        padding-top: 0;
        padding-right: 0;
        padding-left: 3px;
        padding-bottom: 3px;
        vertical-align: top;
    }
    .request-table tr {
        border-top: 1px solid #333333;
    }
    .message-table tr {
        border-top: 0;
    }
    .report-table tr {
        border-top: 0;
    }
    .report-table th {
        vertical-align: top;
    }
    tr.no-border {
        border: 0;
    }
    .listing-requests {
        width: 100%;
        padding: 5px;
        box-sizing: border-box;
    }
    .listing-requests .message {
        margin-bottom: 5px;
        padding: 5px;
        box-sizing: border-box;
    }
    .listing-requests .message:last-child {
        margin-bottom: 0;
    }
    /* yellow*/
    .requester {
        background-color: #FFEDBB;
        border-color: #f2d083;
    }

    .lister {
        background-color: #75c386;
        margin-left: 100px;
    }
</style>
