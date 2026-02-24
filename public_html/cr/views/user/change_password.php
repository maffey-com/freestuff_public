<form method="post" action="<?=(APP_URL)?>user/save_password" id="form-password" class="form-horizontal">
	<fieldset>
		<div class="row-fluid">
			<div class="widget">
			    <div class="well">
			    	<div class="control-group">
			            <label class="control-label">Old password</label>
			            <div class="controls">
			            	<input type="password" class="span6" name="old_password" />
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">New password</label>
			            <div class="controls">
			            	<input type="password" class="span6" name="password" />
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">Confirm password</label>
			            <div class="controls">
			            	<input type="password" class="span6" name="confirm_password" />
						</div>
			        </div>

					<div class="form-actions align-right">
						<button class="btn btn-info" type="submit">Save</button>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</form>

<script type='text/javascript'>
	$(document).ready(function(){
		$('#form-password').formTools2({
			onStart: function () {

			},
			onComplete: function (msg) {

			},
			onSuccess: function () {
				document.location = '<?=(APP_URL)?>user/change_password';
			}
		});
	});
</script>
