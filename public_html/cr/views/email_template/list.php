<div class="table-bordered table-striped table-hover" id="list-email-templates">
    <table class="table table-striped">
		<thead>
            <tr role="row">
				<th>Title</th>
                <th>Subject</th>
				<th>To Address</th>
				<th>From Address</th>
				<th>Reply to Address</th>
				<th>BCC</th>
				<th>Times sent</th>
            </tr>
        </thead>
		<tbody role="alert" aria-live="polite" aria-relevant="all">
		<?
		foreach ($emails as $row_template_name => $row) {
			$row_email_template_id = (int)paramFromHash('email_template_id', $row);

			if (empty($row_email_template_id)) {?>
				<tr>
					<td><a href="<?=(APP_URL)?>email_template/add/<?=(urlencode($row_template_name))?>"><?=($row_template_name)?></a></td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
				</tr>
			<? } else { ?>
				<tr>
					<td>
						<a href="<?=(APP_URL)?>email_template/edit/<?=($row_email_template_id)?>"><?=($row_template_name)?></a>
					</td>
					<td>
						<?=($row["subject"])?>
					</td>
					<td>
						<?
						if ($row["to"] == FALSE) {
							echo nl2br($row["to_address"]);
						} else {
							echo $row["to"];
						}
						?>
					</td>
					<td>
						<?
						if (!isset($row["send"]) || ($row["send"] == FALSE)) {
							echo nl2br($row["from_address"]);
						} else {
							echo $row["send"];
						}
						?>
					</td>
					<td>
						<?
						if ($row["reply"] == FALSE) {
							echo nl2br($row["reply_to_address"]);
						} else {
							echo $row["reply"];
						}
						?>
					</td>
					<td>
						<?
						if ($row["bcc"] == FALSE) {
							echo substr($row["bcc_list"]??'', 0, 20);
							if (strlen($row["bcc_list"]??'') > 20) {
								echo "...";
							}
						} else {
							echo $row["bcc"];
						}
						?>
					</td>
					<td>
						<?=($row["count"])?>
					</td>
				</tr>
			<? }
		} ?>
		</tbody>
    </table>
</div>
