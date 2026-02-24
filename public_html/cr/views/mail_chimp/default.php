<div class="form-horizontal">
    <div class="row-fluid">
        <div class="widget">
            <?
            TemplateHandler::setTableCaptionText('Users not in MailChimp');
            TemplateHandler::echoTableCaption();
            ?>
            <div class="well">
                <div class="control-group">
                    <label class="control-label">From date</label>
                    <div class="controls"><?= (DateHelper::display($row["from_date"])) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">To date</label>
                    <div class="controls"><?= (DateHelper::display($row["to_date"])) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">Count</label>
                    <div class="controls"><?= ($row["count_users"]) ?></div>
                </div>

                <div class="form-actions align-right">
                    <button type="button" id="push_to_mailchimp" class="btn btn-info">Push to MailChimp</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?
TemplateHandler::setTableCaptionText('Results');
TemplateHandler::echoTableCaption();
?>
<div id="mailchimp_results" class="well body"></div>

<script type="text/javascript">
    $("document").ready(function() {
        $("#push_to_mailchimp").click(function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?=(APP_URL)?>mail_chimp/push',
                success: (function(msg) {
                    $("#mailchimp_results").append(msg);

                    if (msg != 'Finished') {
                        $("#push_to_mailchimp").click();
                    }
                })
            });
        });
    });
</script>
