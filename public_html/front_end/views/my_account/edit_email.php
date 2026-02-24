<div class="container">
    <?
    TemplateHandler::echoPageTitle('Change Email');
    ?>

    <div class="row" id="edit-email-page">
        <div class="col">
            <form id='edit_email_form' method='post' action='<?= (APP_URL) ?>account/update_email'>
                <div class="form-group">
                    <label>Current email address <span class=""><?= ($user->email) ?></span></label>
                </div>

                <div class="form-group">
                    <label for="email">New email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your new email address"/>
                </div>

                <div class="form-group">
                    <input type='submit' class='btn primary btn-mobile' value='Save Email'/>
                    <a class="btn btn-mobile" href="<?=(APP_URL)?>my_freestuff">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
