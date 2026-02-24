<!-- Modal content-->
<div class="modal-content">
    <form method="post" action="<?= (APP_URL) ?>report/process_report/<?= ($listing_id) ?>" id="report_form">
        <div class="modal-header">
            <h5 class="modal-title">Report this listing</h5>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <p><b>Is there something wrong with this listing?</b></p>
            <p>At Freestuff we are committed to ensuring that all listings are genuinely free!</p>

            <div class="form-group">
                <label for="issue">Describe what is wrong with this listing</label>
                <textarea name="report_comment" id="report_comment" class="form-control"></textarea>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class='btn danger' id="report">Report</button>
        </div>
    </form>
</div>
