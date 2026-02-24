<div class="container">
    <?
    TemplateHandler::echoPageTitle('Reset password', 'Please enter a new password');
    ?>

    <div class="row" id="reset-password-page">
        <div class='col'>
            <form method='post' action='<?= (APP_URL) ?>login/forgotten_password_process_reset' id="reset_form">
                <input type="hidden" name="verify_id" value="<?= $verify_id ?>"/>
                <input type="hidden" name="code" value="<?= $code ?>"/>

                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input type='password' class='form-control' id='new-password' name='new_password' value=''/>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Re-Enter Password</label>
                    <input type='password' class='form-control' id='confirm-password' name='confirm_password' value=''/>
                </div>
                <button type='submit' class='btn primary'>Change Password</button>
            </form>
        </div>
    </div>
</div>
