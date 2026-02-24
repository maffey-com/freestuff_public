<div class="container">
    <?
    TemplateHandler::echoPageTitle('Change Password');
    ?>
    <div class="row" id="edit-password-page">
        <div class="col">
            <form id='change_password_form' method='post' action='<?= (APP_URL) ?>account/update_password'>
                <div class="form-group">
                    <label for="old_password">Current Password</label>
                    <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Please enter your current password" />
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Please enter a password" />
                </div>
                <div class="form-group">
                    <label for="new_password_again">Confirm New Password</label>
                    <input type="password" class="form-control" id="new_password_again" name="new_password_again" placeholder="Please enter your new password again" />
                </div>
                <div class="form-group">
                    <input type='submit' class='btn primary btn-mobile' value='Save Password'/>
                    <a class="btn btn-mobile" href="<?=(APP_URL)?>my_freestuff">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>


