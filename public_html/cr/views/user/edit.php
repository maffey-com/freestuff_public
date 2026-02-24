<form method="post" action="<?=(APP_URL)?>user/save/<?=($user->user_id)?>" id="form-user" class="form-horizontal">
    <input type="hidden" name="user_id" value="<?=($user->user_id)?>" />

    <!--<input type="hidden" name="created_on" value="<?/*=$user->created_on*/?>" />
	<input type="hidden" name="email_validated" value="<?/*=$user->email_validated*/?>" />
	<input type="hidden" name="mobile_validated" value="<?/*=$user->mobile_validated*/?>" />
	<input type="hidden" name="last_login" value="<?/*=$user->last_login*/?>" />
	<input type="hidden" name="passkey" value="<?/*=$user->passkey*/?>" />-->

    <fieldset>
        <div class="row-fluid">
            <div class="widget">
                <div class="well">
                    <div class="control-group">
                        <label class="control-label">ID</label>
                        <div class="controls"><?=($user->user_id)?></div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Email</label>
                        <div class="controls">
                            <input type="text" class="span6" name="email" value="<?=($user->email)?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Mobile</label>
                        <div class="controls">
                            <input type="text" class="span6" name="mobile" value="<?=($user->mobile)?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">First name</label>
                        <div class="controls">
                            <input type="text" class="span6" name="firstname" value="<?=($user->firstname)?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Closest District</label>
                        <div class="controls">
                            <select class="form-control" name="district_id">
                                <option>Please Select</option>
                                <? foreach (District::getAllNested() as $region_name => $districts) {
                                    foreach ($districts as $district_id => $district_name) {
                                        echo FormHelper::option(District::display($district_id), $district_id,$user->district_id);
                                    }
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Request Credit</label>
                        <div class="controls">
                            <input type="text" class="span2" name="request_credit" value="<?=($user->request_credit)?>" />
                        </div>
                    </div>

                    <?/*
			    	<div class="control-group">
			            <label class="control-label">Password</label>
			            <div class="controls">
			            	<input type="password" class="span6" name="password" value="<?=(User::decryptPassword($user->password))?>" />
						</div>
			        </div>
                    */?>

                    <div class="control-group">
                        <label class="control-label">Thumbs</label>
                        <div class="controls">
                            <?= $user->thumbs_up?><i class="ico-thumbs-up"></i> &nbsp;
                            <?= $user->thumbs_down?><i class="ico-thumbs-down"></i>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Created on</label>
                        <div class="controls">
                            <?= (DateHelper::display($user->created_on, true, true)) ?>
                        </div>
                    </div>


                    <div class="control-group">
                        <label class="control-label">Email validated</label>
                        <div class="controls">
                            <?= ($user->email_validated ? DateHelper::display($user->email_validated, true) : "Email not validated") ?>
                            <? if (empty($user->email_validated)) { ?>
                                [<a href="#" id="manual_validate_email">Validated Now</a>]
                            <? } ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Mobile validated</label>
                        <div class="controls">
                            <?= ($user->mobile_validated ? DateHelper::display($user->mobile_validated, true) : "Mobile not validated") ?>
                            <? if (empty($user->mobile_validated)) { ?>
                                [<a href="#" id="manual_validate">Validated Now</a>]
                            <? } ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Last login</label>
                        <div class="controls">
                            <?= ($user->last_login ? DateHelper::display($user->last_login, true) : "Never logged in") ?>
                        </div>
                    </div>


                    <div class="form-actions align-right">
                        <a class="btn" href="<?=(APP_URL)?>user">Cancel</a>
                        <button class="btn btn-info" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</form>

<script type='text/javascript'>
    $(function(){

        $("#manual_validate").click(function(e){
            e.preventDefault();

            location.href = '<?=(APP_URL)?>user/validate_mobile_number/<?=($user->user_id)?>';
        });

        $("#manual_validate_email").click(function(e){
            e.preventDefault();

            location.href = '<?=(APP_URL)?>user/validate_email/<?=($user->user_id)?>';
        });

        $('#form-user').formTools2({
            onStart: function () {

            },
            onComplete: function (msg) {

            },
            onSuccess: function () {
                document.location = '<?=(APP_URL)?>user/edit/<?=($user->user_id)?>';
            }
        });
    });
</script>
