    $(document).ready(function () {
        $('#reset_form').formTools2({
            onSuccess: function (msg) {
                document.location = 'login';
            }
        });
    });

