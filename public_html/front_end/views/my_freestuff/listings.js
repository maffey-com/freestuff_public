$(function() {
    $("#input-age").change(function() {
        location.href = $php.current_url + '?filter_age=' + $(this).val()
    });
});