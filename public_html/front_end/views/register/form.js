$(function () {

    $("#btn-sign_up_with_email").click(function() {
        $("#registration_form").removeClass('d-none');
        $("#html-sign_up_options").addClass('d-none');
    });

    $("#btn-cancel_sign_up").click(function(){
        $("#registration_form").addClass('d-none');
        $("#html-sign_up_options").removeClass('d-none');
    });

    //$('#email').focus();

    $('#registration_form').formTools2({
        onSuccess: function (obj) {
            document.location = "user_verify/email_form/" + obj.verify_id;
        }
    });

    $('#registration_form input[type="text"]').keydown(function (event) {
        if ((event.keyCode == 13)) {
            event.preventDefault();
            return false;
        }
    });

    $("#btn-sign_up_with_fb").click(function() {
        triggerFbLogin('register');
    });
});
