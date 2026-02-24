$(document).ready(function () {
    $('#forgotten_password_mobile_form').formTools2({
        onSuccess: function (obj) {
            document.location = "user_verify/mobile_form/" + obj.verify_id;
        }
    });
    $('#forgotten_password_email_form').formTools2({
        onSuccess: function (obj) {
            document.location = "user_verify/email_form/" + obj.verify_id;
        }
    });
});

