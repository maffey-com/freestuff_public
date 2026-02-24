<?
$site_error      = paramFromHash('error', $GLOBALS);
$session_error   = MessageHelper::getSessionErrorMessage();
$session_success = MessageHelper::getSessionSuccessMessage();
?>
<div class="container" id="message-holder">
    <?
    if (hasErrors() || (!empty($site_error)) || (!empty($session_error))) {?>
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php
                foreach (getErrors() as $tmp) {
                    echo '<p>' . $tmp . '</p>';
                }
                if (!empty($site_error)) {
                    echo '<p>' . $site_error . '</p>';
                }
                if (!empty($session_error)) {
                    echo $session_error;
                }
            ?>
        </div>
        <?
    }

    if (!empty($session_success)) {?>
        <div class="row alert alert-primary alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?= ($session_success) ?>
        </div>
        <?
    }
    ?>
</div>

<?
MessageHelper::unsetSessionErrorMessage();
MessageHelper::unsetSessionSuccessMessage();