$(function () {
    $("#block_user_modal").formTools2({
        onComplete: function (msg) {
            location.reload();
        }
    });
});
