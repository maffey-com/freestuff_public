<form method="post" action="<?=(APP_URL)?>report/send_email/<?=($report->report_id)?>" id="form-report-email" class="form-horizontal">
	<input type="hidden" name="type" value="<?=($type)?>" />

		<div class="row-fluid">
			<div class="widget">
				<?
				TemplateHandler::setTableCaptionText("Send email to '" . $type . "'");
				TemplateHandler::echoTableCaption();
				?>

			    <div class="well">
			    	<div class="control-group">
			            <label class="control-label">Subject</label>
			            <div class="controls">
			            	<input type="text" class="span12" name="subject" value="<?=($email_template['subject'])?>" />
						</div>
			        </div>

			    	<div class="control-group">
			            <label class="control-label">Message</label>
					</div>

					<div class="control-group">
						<textarea name="message" class="span12" style="height:150px;" ><?=($email_template['message'])?></textarea>
			        </div>

					<div class="form-actions">
						<button class="btn btn-info" type="submit">Send email</button>
					</div>
				</div>
			</div>
		</div>
</form>

