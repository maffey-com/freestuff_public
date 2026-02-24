<!-- Modal content-->
<?
$status_text = $listing->listing_status == 'reserved' ? 'Remove Reserve' : 'Mark as Reserved';
?>
<div class="modal-content">
    <form method="post" action="<?= (APP_URL) ?>list/mark_as_reserved/<?= $listing_id ?>" id="mark-as-reserved-modal">
        <div class="modal-header">
            <h5 class="modal-title"><?= $status_text ?>?</h5>
        </div>
        <div class="modal-body">
            <? if ($listing->listing_status == 'reserved') { ?>
            <p><b>Marking this item as Available will allow people to request it again.</b></p>
            <? } else { ?>
            <p><b>Marking this item as Reserved will prevent people from requesting it.</b></p>
            <p><b>If no action is taken the reserve will be removed in <?= Listing::$reserve_expiry_hours ?> hours</b></p>
            <? } ?>
        </div>
        <div class="modal-footer">
            <button class='btn btn-secondary' data-dismiss="modal">Cancel</button>
            <button type="submit" class='btn btn-success' id="reserve-item"><?= $status_text ?></button>
        </div>
    </form>
</div>