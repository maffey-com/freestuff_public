<div class="container">
    <?
    TemplateHandler::echoPageTitle('Register for Freestuff', 'You need to be logged in to give away and receive free stuff.');
    ?>

    <div class="row" id="register-page">
        <div class="col">

            <form id='registration_form' class="" method='post' action='register/process_registration'>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" class="form-control" name="email" value="<?= ($user->email) ?>"/>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Password</label>
                        <small id="passwordHelpInline" class="text-muted">
                            Must be 5-20 characters long.
                        </small>
                        <input type="password" id="password" class="form-control" name="password"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password2">Confirm Password</label>
                        <input type="password" id="password2" class="form-control" name="password2"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="firstname">First name</label>
                    <input type="text" id="firstname" class="form-control" name="firstname" value="<?= ($user->firstname) ?>"/>
                    <input type="text" id="lastname" class="form-control d-none" name="lastname" value="" placeholder="leave this field blank" />
                </div>


                <div class="form-group">
                    <label for="district_id_field">Closest District</label>
                    <select class="form-control" name="district_id" id="district_id_field">
                        <option>Please Select</option>
                        <? foreach (District::getAllNested() as $region_name => $districts) {
                            foreach ($districts as $district_id => $district_name) {
                                echo FormHelper::option(District::display($district_id), $district_id,$user->district_id);
                            }
                        }?>
                    </select>

                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type='checkbox' class="form-check-input" name="terms" id="terms"/>&nbsp
                        <label class="form-check-label" for="terms">Agree to our <a href='page/terms' target="_blank">terms</a>.</label>
                    </div>
                </div>

                <div class="form-group">
                        <small class="text-danger">All fields are required</small>
                </div>

                <div class="form-group">
                    <button type='submit' class='btn primary btn-mobile'>Register</button>
                    <button type='button' class='btn btn-secondary btn-mobile' id="btn-cancel_sign_up">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
