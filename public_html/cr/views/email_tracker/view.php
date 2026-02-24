<div class="form-horizontal">
    <div class="row-fluid">
        <div class="widget">
            <?php
            TemplateHandler::setTableCaptionText('Email tracker');
            TemplateHandler::echoTableCaption();
            ?>

            <div class="well">
                <div class="control-group">
                    <label class="control-label">Date</label>
                    <div class="controls"><?= ($email["date_sent"]) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">From</label>
                    <div class="controls"><?= ($email["from_address"]) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">To</label>
                    <div class="controls"><?= ($email["to_address"]) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">Subject</label>
                    <div class="controls"><?= ($email["email_subject"]) ?></div>
                </div>

                <div class="control-group">
                    <?= nl2br($email["email_body"]) ?>
                </div>
            </div>
        </div>
    </div>
</div>