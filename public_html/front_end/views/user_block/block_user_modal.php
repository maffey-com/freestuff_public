<!-- Modal content-->
<div class="modal-content">
    <form method="post" action="<?= (APP_URL) ?>user_block/block_user/<?= ($other_user_id) ?>" id="block_user_modal">
        <div class="modal-header">
            <h5 class="modal-title">Block this user?</h5>
        </div>
        <div class="modal-body">
            <p><b>Are you sure you want to block this user?</b></p>

            <div class="form-check">
                <input type="checkbox" name="hide_messages" id="hide_messages" value="y" class="form-check-input" />
                <label class="form-check-label" for="hide_messages">Hide previous messages?</label>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class='btn danger' id="report">Block</button>
        </div>
    </form>
</div>
