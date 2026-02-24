$(function () {
    $("#saved_search_form").formTools2({
        onComplete: function (listing_id) {
            window.location = "search";
        }
    });
});
