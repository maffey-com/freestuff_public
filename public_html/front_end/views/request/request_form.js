$(document).ready(function () {

    $('#request_form').formTools2({
        onSuccess: function () {
            document.location = $php.app_url + 'request/submitted/' + $php.listing_id
        }
    });

    setTimeout(function() {
        $('#request_comment').focus()
    },300
    );

    /*
    $("body").on("click", "#btn-add_time", function() {
        var el_parent = $("#html-collect_time");

        var html = $(el_parent).find(".html-template_time").clone();
        $(html).removeClass('html-template_time d-none');

        $(el_parent).find('li:last').after(html);
    });

    $("body").on("click", ".btn-remove_time", function() {
        $(this).closest('li').remove();
    });

    $("#btn-add_time").trigger('click');*/
});

