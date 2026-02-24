$(function () {
    $("#delete_modal").formTools2({
        onComplete: function (msg) {
            history.back();
        }
    });
});
