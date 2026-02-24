
    $(function () {
        $('#btn-yes_delete').click(function(){
            $.ajax({
                type   : 'GET',
                url    : 'search/confirm_delete/'+$php.search_id,
                success: function (msg) {
                    var obj = $.parseJSON(msg);

                    if (obj.success) {
                        window.location = $php.app_url + "search";

                    } else {
                        $("#modal-box .modal-content").modal('hide');

                        var error = '';
                        $.each(obj.messages, function(key, value) {
                            error += value + "\n";
                        });

                        alert(error);
                    }
                }
            });
        });
    });
