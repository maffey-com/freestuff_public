<h1>Report</h1>
<style>
	.standard_form ol li {
		height:15px;
		min-height:15px;
	}
</style>

<form method="post" action="report.php" class="standard_form" id="report_form">
	<fieldset>
		<p>Report date: <? h($report->report_date)?></p>
		<h3>Reporter</h3>
		<ol>		
			<li>
				<label for="user_id">User Id</label>
				<? h($report->reporter_user_id)?>
				(<a href="<?=(APP_URL)?>/user/history/<?=($report->reporter_user_id)?>" class="popup"">View history</a>)
			</li>
			<li><label>Firstname</label><? h($report->reporter_firstname)?></li>
			<li><label>Location</label><? h($report->reporter_location)?></li>
			<li><label for="report_comment">Report comment</label><? h($report->report_comment)?></li>
			<li><label for="ip_address">Ip address</label><? h($report->ip_address)?></li>
		</ol>
		<h3>Lister</h3>
		<ol>
			<li>
				<label for="user_id">User Id</label><? h($report->lister_user_id)?>
				(<a href="<?=(APP_URL)?>/user/history/<?=($report->lister_user_id)?>" class="popup")">View history</a>)
			</li>
			<li><label>Firstname</label><? h($report->lister_firstname)?></li>
			<li><label>Location</label><? h($report->lister_location)?></li>
			<li><label for="listing_id">Listing id</label><? h($report->listing_id)?></li>
			<li><label for="listing_title">Listing Title</label><? h($report->listing_title)?></li>
		</ol>
		<h3>Outcome</h3>
		<ol>
			<li><label for="status">Status</label><? h($report->status)?></li>
			<? 	if ($report->status != "NEW") {?>
					<li><label for="freestuff_comment">Freestuff comment</label><? h($report->freestuff_comment)?></li>
					<li><label for="freestuff_action_date">Freestuff action date</label><? h(DateHelper::display($report->freestuff_action_date))?></li>
			<?	}?>
		</ol>
		<? 	if ($report->status == "NEW") {?>
				<div style="border:1px solid red; margin:10px 0; padding:10px;">
					<h3>Action Required</h3>
					<p><input type="radio" name="action" value="Remove Listing" />Remove Listing</p>
					<p><input type="radio" name="action" value="Warn Lister" />Warn Lister </p>
					<p><input type="radio" name="action" value="Reject Report" />Reject Report </p>
					<p>Message to user:<br /><textarea name="freestuff_comment"></textarea></p>
					<span class="button tick" id="save" onclick="document.getElementById('report_form').submit()">Save</span>
					<span class="button cross" id="cancel" onclick="document.location='report.php'">Cancel</span>	
					<input type="hidden" name="pa" value="action" />
					<input type="hidden" name="report_id" value="<?=$report->report_id?>" />
					<br class="clear" /><br />
				</div>
		<?	}?>
	</fieldset>
</form>	