<!-- Modal content-->
<div class="modal-content" id="mark-as-taken">
    <div class="modal-header">
        <h5 class="modal-title">Did anyone take this item?</h5>
    </div>
    <div class="modal-body">
        <? 
            $available_requests = array_filter($requests, fn($r) => $r['no_show'] !== 'y');

            if (count($available_requests) == 0) {
                ?>
                    <p>All requesters have been marked as no shows.</p>
                <? 
            } else {
                ?>
                    <p>Selecting a user will also give them a positive feedback</p>
                <?
            }

            foreach ($requests as $request) {
                if ($request['no_show'] === 'y') continue;
                ?>
                    <p>
                        <span class="btn btn-link mark-item-taken" data-user_id="<?= $request['user_id'] ?>">
                            <b><?= $request['user_firstname'] ?></b> of <b><?= $request['district'] ?></b>
                        </span>
                    </p>
                <?
            }
        ?>
    </div>
    <div class="modal-footer">
        <span class="btn btn-secondary mark-item-taken" data-user_id="0">
            <?= count($available_requests) == 0 ? 'Mark as gone' : 'None of the above' ?>
        </span>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.mark-item-taken').click(function () {
            let listing_id = "<?= $listing_id ?>";
            let request_user_id = $(this).attr('data-user_id');
            $.ajax({
                url: 'list/mark_as_gone/' + listing_id + '/' + request_user_id,
            }).done(function () {
                $('#modal-box').modal('hide');
                window.location.reload();
            });
        });
    });
</script>