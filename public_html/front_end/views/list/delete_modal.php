<!-- Modal content-->
<div class="modal-content">
    <form method="post" action="<?= (APP_URL) ?>list/delete/<?= $listing_id ?>" id="delete_modal">
        <div class="modal-header">
            <h5 class="modal-title">Delete this item?</h5>
        </div>
        <div class="modal-body">
            <p><b>This will permanently delete this item.</b></p>
            <p><b>Are you sure you want to proceed?</b></p>
        </div>
        <div class="modal-footer">
            <button class='btn btn-secondary' data-dismiss="modal">Cancel</button>
            <button type="submit" class='btn danger' id="report">Delete</button>
        </div>
    </form>
</div>
