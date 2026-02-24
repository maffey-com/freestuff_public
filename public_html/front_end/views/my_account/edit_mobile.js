$(document).ready(function () {
    $('#edit_mobile_form').formTools2({
        onSuccess: function (obj) {
            document.location = 'user_verify/mobile_form/' + obj.verify_id;
        }
    });
    return false;
});


