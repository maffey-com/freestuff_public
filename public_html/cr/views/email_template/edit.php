<form method="POST" action="<?= (APP_URL) ?>email_template/update" id="form-template" class="form-horizontal">
    <input type="hidden" name="email_template_id" value="<?= ($et->email_template_id) ?>"/>

    <fieldset>
        <div class="row-fluid">
            <div class="widget">
                <div class="well">
                    <div class="control-group">
                        <label class="control-label">Template name</label>
                        <div class="controls"><?= ($et->name) ?></div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Subject</label>
                        <div class="controls">
                            <input type="text" class="span12" name="subject" value="<?= ($et->subject) ?>"/>
                        </div>
                    </div>

                    <?
                    if ($et->to_variable == FALSE) { ?>
                        <div class="control-group">
                            <label class="control-label">To name</label>
                            <div class="controls">
                                <input type="text" class="span12" name="to_name" value="<?= ($et->to_name) ?>"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">To email</label>
                            <div class="controls">
                                <input type="text" class="span12" name="to_address" value="<?= ($et->to_address) ?>"/>
                            </div>
                        </div>
                        <?
                    } else { ?>
                        <div class="control-group">
                            <label class="control-label">To variable</label>
                            <div class="controls">
                                <?= ($et->to_variable) ?>
                            </div>
                        </div>
                        <?
                    }

                    if ($et->from_variable == FALSE) { ?>
                        <div class="control-group">
                            <label class="control-label">From name</label>
                            <div class="controls">
                                <input type="text" class="span12" name="from_name" value="<?= ($et->from_name) ?>"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">From email</label>
                            <div class="controls">
                                <input type="text" class="span12" name="from_address"
                                       value="<?= ($et->from_address) ?>"/>
                            </div>
                        </div>
                        <?
                    } else { ?>
                        <div class="control-group">
                            <label class="control-label">From variable</label>
                            <div class="controls">
                                <?= ($et->from_variable) ?>
                            </div>
                        </div>
                        <?
                    }

                    if ($et->reply_variable == FALSE) { ?>
                        <div class="control-group">
                            <label class="control-label">Reply to name</label>
                            <div class="controls">
                                <input type="text" class="span12" name="reply_to_name"
                                       value="<?= ($et->reply_to_name) ?>"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Reply to email</label>
                            <div class="controls">
                                <input type="text" class="span12" name="reply_to_address"
                                       value="<?= ($et->reply_to_address) ?>"/>
                            </div>
                        </div>
                        <?
                    } else { ?>
                        <div class="control-group">
                            <label class="control-label">Reply to variable</label>
                            <div class="controls">
                                <?= ($et->reply_variable) ?>
                            </div>
                        </div>
                        <?
                    }

                    if ($et->bcc_variable == FALSE) { ?>
                        <div class="control-group">
                            <label class="control-label">BCC email (separate by comma)</label>
                            <div class="controls">
                                <input type="text" class="span12" name="bcc" value="<?= ($et->bcc) ?>"/>
                            </div>
                        </div>
                        <?
                    } else { ?>
                        <div class="control-group">
                            <label class="control-label">BCC variable</label>
                            <div class="controls">
                                <?= ($et->bcc_variable) ?>
                            </div>
                        </div>
                        <?
                    }
                    ?>
                    <div class="control-group">
                        <label class="control-label">Body</label>
                        <div class="controls">
                            <textarea class="span12" name="message" id="editor-message"
                                      style="height:450px"><?= ($et->message) ?></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Times sent</label>
                        <div class="controls">
                            <?= ($et->count) ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Translated code</label>
                        <div class="controls">
                            <?= (nl2br($et->translated_code ?? '')) ?>
                        </div>
                    </div>

                    <div class="form-actions align-right">
                        <a class="btn" href="<?= (APP_URL) ?>user">Cancel</a>
                        <button class="btn btn-info" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</form>

<script type='text/javascript'>
    $(document).ready(function () {
        $('#form-template').formTools2({
            onSuccess: function () {
                document.location = '<?=(APP_URL)?>email_template/edit/<?=($et->email_template_id)?>';
            }
        });
    });
</script>