$(document).ready(function () {
    $('#edit_email_form').formTools2({
        onSuccess: function (obj) {
            document.location = 'user_verify/email_form/'+obj.verify_id;
        }
    });
});