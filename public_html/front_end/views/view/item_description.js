function setNewStatus(elem) {
    switch (elem.data('status')) {
        case 'delist':
            // TODO: Remove from code if nothing break [CS] 17/07/2024
            // $.ajax({
            //     url: 'list/process_delist/'+elem.data('listing_id'),
            // }).done(function (new_status) {
            //     window.location.reload();
            // });
            break;

        case 'relist':
            document.location = "list/relist/"+elem.data('listing_id');
            break;
    }
}

$(function () {
    $('.status-btn').click(function (e) {
        e.preventDefault();

        setNewStatus($(this));
    });
});
