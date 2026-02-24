<form method="post" action="<?=(APP_URL)?>report_contact/send_reply" id="form-contact" class="form-horizontal">
	<input type="hidden" name="contact_id" value="<?=($contact->id)?>" />

	<fieldset>
		<div class="row-fluid">
			<div class="widget">
			    <div class="well">
			    	<div class="control-group">
			            <label class="control-label">ID</label>
			            <div class="controls">
							<?=($contact->id)?>
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">Name</label>
			            <div class="controls">
							<?=($contact->name)?>
							<br />
							<?=($contact->email)?> (<a target="_blank" href="<?=(APP_URL)?>user?filter_name=<?=($contact->email)?>">View details</a>)
							<br />
							<?=($contact->phone)?>
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">Contact date</label>
			            <div class="controls">
							<?=($contact->contact_date)?>
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">IP address</label>
			            <div class="controls">
							<?=($contact->ip_address)?>
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">Status</label>
			            <div class="controls">
							<?=($contact->status)?> - <?=($contact->freestuff_action_date)?>
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">Enquiry</label>
			            <div class="controls">
							<?=(nl2br($contact->enquiry))?>
						</div>
			        </div>

					<?
					if ($contact->status == 'New') {
						?>
						<div class="control-group">
							<label class="control-label">Reply</label>
							<div class="controls">
								<?
								if ($contact->status == 'New') {
									?>
									<textarea name="reply" class="auto span12 " style="min-height: 120px"></textarea>
									<?
								} else {
									echo nl2br($contact->reply);
								}
								?>
							</div>
						</div>
						<div class="form-actions align-right">
							<button class="btn btn-info" type="submit">Send</button>
							<a class="btn" href="<?=(APP_URL)?>report_contact/close/<?= ($contact->id) ?>" title="Close this">Close</a>
						</div>
						<?
					} else {
						?>
						<div class="control-group">
							<label class="control-label">Reply</label>
							<div class="controls">
								<?=(nl2br($contact->reply))?>
							</div>
						</div>
						<?
					}
					?>
				</div>
			</div>
		</div>
	</fieldset>
</form>

<script type='text/javascript'>
	$(document).ready(function(){
		$('#form-contact').formTools2({
			onSuccess: function () {
				document.location = '<?=(APP_URL)?>report_contact';
			}
		});
	});
</script>