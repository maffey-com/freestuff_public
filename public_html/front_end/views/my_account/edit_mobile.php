<div class="container">
    <?
    TemplateHandler::echoPageTitle('Change Mobile Number');
    ?>
    <div class="row" id="edit-mobile-page">
        <div class="col">
            <p>Current Mobile Number <b><?=SmsPi::isValidMobileNumber($user->mobile)?$user->mobile:'<span class="ext-muted">No mobile number set</span>'?></b></p>

            <form class="edit_mobile_form" id='edit_mobile_form' method='post' action='<?= (APP_URL) ?>account/update_mobile'>
                <div class="form-group">
                    <label for="mobile_prefix" class="d-block d-md-inline-block mr-2">New Mobile Number</label>

                    <select class="form-control d-inline-block mr-2 w-auto" id="mobile_prefix" name="mobile_prefix">
                        <option value="020">020</option>
                        <option value="021">021</option>
                        <option value="022">022</option>
                        <option value="026">026</option>
                        <option value="027">027</option>
                        <option value="028">028</option>
                        <option value="029">029</option>
                    </select>
                    <input type="tel" class="form-control d-inline-block w-auto" name="mobile" placeholder="Mobile Number"/>
                </div>

                <div class="form-group">
                    <input type='submit' class='btn primary btn-mobile' value='Save Mobile Number'/>
                    <a class="btn btn-mobile" href="<?=(APP_URL)?>my_freestuff">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
