
//Annoy fun fun
var annoy_html = '<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content">' +
    '<div class="modal-header">' +
    '<h5 class="modal-title" ></h5>' +
    '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
    '<span aria-hidden="true">&times;</span>' +
    '</button>' +
    '</div>' +
    '<div class="modal-body"></div>' +
    '<div class="modal-footer">' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';

function Annoy(options) {
    var defaults = {
        title: 'Information',
        body: '',
        buttons: [

            {
                text: "Ok",
                callback: function () {
                },
                dismiss: true,
                class: 'btn-danger'
            },
            {
                text: "Cancel",
                callback: function () {
                },
                dismiss: true,
                class: 'btn-primary'

            }

        ]

    };

    var button_defaults = {
        text: "Ok",
        callback: function () {
        },
        dismiss: true,
        class: 'btn-primary'
    };

    var settings = $.extend({}, defaults, options);
    var modal = $(annoy_html);
    modal.find('.modal-title').html(settings.title);
    modal.find('.modal-body').html(settings.body);


    for (var i = 0; i < settings.buttons.length; i++) {
        var button_options = settings.buttons[i];
        var button_settings = $.extend({}, button_defaults, button_options);

        var button = $('<button type="button" class="btn"></button>');
        button.html(button_settings.text);
        button.addClass(button_settings.class);
        if (button_settings.dismiss) {
            button.attr('data-dismiss', 'modal');
        }
        button.on('click', button_settings.callback);

        modal.find('.modal-footer').append(button);
    }
    modal.appendTo($('body'));
    modal.modal('show');

}

function Alert(body, title, callback) {
    options = {
        body: body,
        title: title || 'Alert',
        buttons: [
            {
                text: "Ok",
                callback: callback || function () {
                },
                dismiss: true,
                class: 'btn-primary'
            }

        ]
    };
    Annoy(options);
}

function Confirm(body, title, callback, buttion1, button2) {
    options = {
        body: body,
        title: title || 'Confirm',
        buttons: [
            {
                text: buttion1 || "Ok",
                callback: callback || function () {
                },
                dismiss: true,
                class: 'btn-danger'
            },
            {
                text: button2 || "Cancel",
                dismiss: true,
                class: 'btn'

            }

        ]
    };
    Annoy(options);
}
$(function () {
    $('body').on('click', '.annoy-ajax-confirm',function (e) {
        e.preventDefault();
        var url = $(this).attr('data-confirm_href');
        var confirm_body = $(this).attr('data-confirm_body') || 'Are you sure?';
        var redirect = $(this).attr('data-confirm_redirect');
        Confirm(confirm_body, 'Confirm', function() {
            $.ajax({
                url: url
            }).done(function (msg) {
                if (redirect) {
                    document.location = redirect;
                } else {
                    document.location.reload();
                }
            });
        });
    })
})
