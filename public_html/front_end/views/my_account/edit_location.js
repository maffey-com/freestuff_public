$(document).ready(function () {
    $('#edit_district_form').formTools2({
        onSuccess: function (obj) {
            document.location = 'my_freestuff';
        }
    });
    return false;
});


