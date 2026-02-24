$(function () {
        $("#report_form").formTools2({
            onComplete: function (listing_id) {
                $("#modal-box .modal-content").load("report/saved");
            }
        });
});
