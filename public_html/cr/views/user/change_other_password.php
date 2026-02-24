<div class="form-horizontal">
    <div class="row-fluid">
        <div class="widget">
            <?php
            TemplateHandler::setTableCaptionText('Change user password');
            TemplateHandler::echoTableCaption();
            ?>

            <form class="well" method="post" action="user/save_other_password/<?=($user->user_id)?>">
                <div class="control-group">
                    <label class="control-label">User ID</label>
                    <div class="controls"><?= ($user->user_id) ?></div>
                </div>
                <div class="control-group">
                    <label class="control-label">Email</label>
                    <div class="controls"><?= ($user->email) ?></div>
                </div>

                <div class="control-group">
                    <label class="control-label">New password</label>
                    <input type="text" name="password" value="<?=(StringHelper::randomString(8))?>" />
                </div>

                <div class="control-group">
                    <input type="submit" class="btn btn-danger" value="Change password" />
                </div>
            </form>
        </div>
    </div>
</div>