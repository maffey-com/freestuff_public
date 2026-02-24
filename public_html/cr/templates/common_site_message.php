<?php
$session_error = MessageHelper::getSessionErrorMessage();
if (hasErrors() || (!empty($session_error))) {
	foreach (getErrors() as $tmp) {
		?>
		<div class="alert alert-error" style="margin-top: 16px;">
			<button data-dismiss="alert" class="close" type="button">x</button>
			<strong>Warning!</strong> <?=($tmp)?>
		</div>
		<?
	}
		
	if (!empty($session_error)) {
		?>
		<div class="alert alert-error" style="margin-top: 16px;">
			<button data-dismiss="alert" class="close" type="button">x</button>
			<strong>Warning!</strong> <?=($session_error)?>
		</div>
		<?	
	}
}
MessageHelper::unsetSessionErrorMessage();

$session_success = MessageHelper::getSessionSuccessMessage();
if (!empty($session_success)) {
	?>
	<div class="alert alert-success" style="margin-top: 16px;">
		<button data-dismiss="alert" class="close" type="button">x</button>
		<?=($session_success)?>
	</div>
	<?
}
MessageHelper::unsetSessionSuccessMessage();