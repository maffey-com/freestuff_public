<div class="container">
    <?
    TemplateHandler::echoPageTitle('Please verify your Phone Number');
    ?>
    <div class="row">
        <div class="col">
            <p>Robbie the robot should be ringing you very soon with a six digit code.  Please enter this code in the box below and press the Verify Code button.</p>

            <form class="form-inline" method='get' action='<?= (APP_URL) ?>user_verify/landline_process/<?=$uv->verify_id?>'>
                <div class="form-group mb-2 mr-2">
                    <label for="code_field" class="sr-only">Verification code</label>
                    <input type="text" name="code" class="form-control" id="code_field" style="width: 100px;" />
                </div>
                <button type='submit' class='btn primary mb-2'>Verify Code</button>
            </form>
        </div>
    </div>
</div>

