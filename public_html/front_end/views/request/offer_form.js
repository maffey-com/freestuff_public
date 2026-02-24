$(function () {
    $('#request_form').formTools2({
        onSuccess: function () {
            document.location = 'request/submitted/' + $php.listing_id
        }
    });
});

