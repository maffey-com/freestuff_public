<div class="container">
    <?
    TemplateHandler::echoPageTitle('Change Name');
    ?>
    <div class="row">
        <div class="col">
            <form id='edit_name_form' method='post' action='<?= (APP_URL) ?>account/update_name'>
                <div class="form-group">
                    <label for="firstname">Your Name</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Please enter your first name" value="<?=($user->firstname)?>" />
                </div>
                <div class="form-group">
                    <input type='submit' class='btn primary btn-mobile' value='Save Name'/>
                    <a class="btn btn-mobile" href="<?=(APP_URL)?>my_freestuff">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
