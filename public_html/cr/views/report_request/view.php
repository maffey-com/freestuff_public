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
                <label class="control-label">Lister</label>
                <div class="controls"><?=($lister->email)?> [<a target="_blank" title="Edit user" href="<?=(APP_URL)?>user/edit/<?=($listing->user_id)?>">ID: <?=($listing->user_id)?></a>]</div>
            </div>

            <div class="control-group">
                <label class="control-label">Listing date</label>
                <div class="controls"><?=($listing->listing_date)?></div>
            </div>
        </div>
    </div>
</div>

<?
if (count($requests) == 0) {
    ?>
    <div class="page-header">
        <div class="page-title">
            <h5>0 requests for this listing</h5>
        </div>
    </div>
    <?
} else {
    ?>
    <div class="page-header">
        <div class="page-title">
            <h5>Requests for this listing</h5>
        </div>
    </div>

    <?
    foreach ($requests as $request) {
        $request_id = $request['request_id'];
        $request_email = $request['email'];
        $request_user_id = $request['user_id'];
        ?>
        <!-- Collapsible widget -->
        <div class="widget">
            <div class="navbar">
                <div class="navbar-inner">
                    <h6><a target="_blank" title="View user details"
                           href="<?=(APP_URL)?>user?filter_name=<?= ($request_user_id) ?>"><?= ($request_email) ?></a>
                        - <?= (DateHelper::display($request['request_timestamp'], true, true)) ?></h6>
                    <div class="nav pull-right">
                        <a data-toggle="collapse" class="navbar-icon" data-target="#request_<?= ($request_id) ?>"><i
                                class="icon-resize-vertical"></i></a>
                    </div>
                </div>
            </div>
            <div class="collapse" id="request_<?= ($request_id) ?>">
                <div class="well body">
                    <div class="listing-requests">
                        <?
                        foreach ($messages[$request_id]['nested_items'] as $r_id => $message) {
                            if ($message['sender_user_id'] == $request["user_id"]) {
                                $message_class = "requester";
                            } else {
                                $message_class = "lister";
                            }
                            ?>

                            <div class="message <?= ($message_class) ?>">
                                <?= ($message['message']) ?>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /collapsible widget -->
        <?
    }
}
?>

<style>
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
