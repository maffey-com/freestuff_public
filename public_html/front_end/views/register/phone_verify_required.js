$(document).ready(function () {
    $("#btn_validate_by_landline").click(function (e) {
        $("#mobile_validation_wrapper").hide();
        $("#landline_validation_wrapper").show();
    });
    $("#btn_validate_by_mobile").click(function (e) {
        $("#mobile_validation_wrapper").show();
        $("#landline_validation_wrapper").hide();

    });
    $('#form_mobile_validation').formTools2({
        onSuccess: function (obj) {
            document.location = "user_verify/mobile_form/" + obj.verify_id;
        }
    });
    $('#form_landline_validation').formTools2({
        onSuccess: function (obj) {
            document.location = "user_verify/landline_form/" + obj.verify_id;
        }
    });
});

