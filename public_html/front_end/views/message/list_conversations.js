$(function () {
    $('.row-conversation').on("click", function() {
        location.href = $(this).data('href');
    });

    $("#btn-search_conversation").on("click", function() {
        searchConversation();
    });

    $('#input-search_conversation').keypress(function(event){
        let keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            searchConversation();
        }
    });
});


function searchConversation() {
    let search_text = $.trim($("#input-search_conversation").val());

    if (search_text.length == 0) {
        location.href = 'message/inbox';

    } else if (search_text.length < 5) {
        Alert("Search text must be greater than 5 characters");
        return;

    } else {
        location.href = 'message/inbox?filter_q=' + encodeURIComponent(search_text);
    }
}