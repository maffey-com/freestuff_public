<div class="container">
    <?
    TemplateHandler::echoPageTitle('Verify your Mobile Number');
    ?>
    <div class="row">
        <div class="col">
            <p>We have sent you a TXT message (SMS) containing a six digit code.  Please enter this code in the box below and press the Verify Code button.</p>

            <form class="form-inline" method='get' action='<?= (APP_URL) ?>user_verify/mobile_process/<?=$uv->verify_id?>'>
                <div class="form-group mr-2 mb-2">
                    <label for="code_field" class="sr-only">Verification code</label>
                    <input class="form-control" type="text" name="code" style="width: 100px" id="code_field" autofocus/>
                </div>
                <button type='submit' class='btn primary mb-2'>Verify Code</button>
            </form>
            <span class="form-text small text-muted">If you do not receive a TXT message from us, <a href="<?=APP_URL?>user_verify/resend_sms/<?=$uv->verify_id?>">click here</a> to resend the message.</span>

        </div>
    </div>
</div>