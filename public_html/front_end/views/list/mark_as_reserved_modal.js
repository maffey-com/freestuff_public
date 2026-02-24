$(function () {
    $("#mark-as-reserved-modal").formTools2({
        onComplete: function (msg) {
            location.reload();
        }
    });
});
