var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

$(function () {
    $('body').on('click', '.send_request_message', function (e) {
        e.preventDefault();

        var el = $(this).closest('#html-conversation');

        sendMessage(el);
    });

    $('body').on('click', ".thumb-span", function (e) {
        var clicked_on = $(this).hasClass("mr-2") ? "thumbs_up" : "thumbs_down";
        var thumbs_holder = $(this).closest('.thumbs');
        var thumbs_up_count = parseInt(thumbs_holder.find('.thumbs-up').html());
        var thumbs_down_count = parseInt(thumbs_holder.find('.thumbs-down').html());
        var new_thumbs_status = 'x';

        if (thumbs_holder.hasClass('my_thumb_u')) {
            thumbs_holder.removeClass('my_thumb_u');
            thumbs_up_count--;
            if (clicked_on == 'thumbs_down') {
                thumbs_down_count++;
                var new_thumbs_status = 'd';
            }

        } else if (thumbs_holder.hasClass('my_thumb_d')) {
            thumbs_holder.removeClass('my_thumb_d');
            thumbs_down_count--;
            if (clicked_on == 'thumbs_up') {
                thumbs_up_count++;
                var new_thumbs_status = 'u';
            }

        } else {
            thumbs_holder.removeClass('my_thumb_x');
            if (clicked_on == 'thumbs_up') {
                thumbs_up_count++;
                var new_thumbs_status = 'u';
            } else {
                thumbs_down_count++;
                var new_thumbs_status = 'd';
            }
        }

        thumbs_holder.addClass('my_thumb_' + new_thumbs_status);
        thumbs_holder.find('.thumbs-up').html(thumbs_up_count);
        thumbs_holder.find('.thumbs-down').html(thumbs_down_count);

        var request_user_id = $(thumbs_holder).data("request_user_id");

        $.ajax({
            url: 'request/save_thumb/' + request_user_id + '/' + new_thumbs_status
        });
    });

    $('body').on('keyup', '.text_limit_box', function () {
        var limit = $(this).attr('maxlength');
        var textlength = $(this).val().length;
        var label = $($(this).data('countdown-label'));
        var remain = limit - textlength;
        if (label.length > 0) {
            var countdown = label;
        } else {
            var countdown = $(this).parent().parent().find('.text_limit_countdown');
        }

        countdown.html(remain + " characters left");
    });

    $('#regions-button').click(function () {
        $(this).parent().find('ul').slideToggle('fast');
        $(this).toggleClass('show');
        $(this).toggleClass('hide');
    });

    $(document).on('touchstart', function (e) {
        $(document).data('touch_pos', $(window).scrollTop());
    }).on('touchend', function (e) {
        if (Math.abs($(document).data('touch_pos') - $(window).scrollTop()) > 3) {
            $(document).trigger('swipe');
            return;
        }
    });

    $('body').on('click', '.ajax-modal', function (e) {
        e.preventDefault();
        var data_url = $(this).attr('data-href');

       $.get({
            url: data_url
        }).done(function (response) {
            var div = $(response);
            div.appendTo($('body'));
           $(div).modal({
              show: true
            });

          $(div).on('hidden.bs.modal', function (e) {
              $(div).remove();
          });
      });
    });

    $('body').on('click', '.ajax-modal-inbox', function (e) {
        var data_url = $(this).attr('data-href');

        $.get({
            url: data_url
        }).done(function (response) {
            var div = $(response);
            div.appendTo($('body'));
            $(div).modal({
                show: true
            });
            $(div).on('hidden.bs.modal', function (e) {
                $(div).remove();
            });
        });
    });

    $('body').on('click', ".block_user", function (e) {
        let other_user_id = $(this).data("other_user_id");
        $.ajax({
            url: 'user_block/block_user/' + other_user_id
        });
    });
    $('body').on('click', ".unblock_user", function (e) {
        e.preventDefault();
        let other_user_id = $(this).data("other_user_id");
        $.ajax({
            url: 'user_block/unblock_user/' + other_user_id
        }).done(function () {
            location.reload();
        });
    });

    /*$("#btn-history_back").click(function(e) {
        e.preventDefault();
        window.history.back();
    });*/
});

String.prototype.capitalizeFirstLetter = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

function scrollToBottom(target) {
    if (typeof target !== 'undefined') {
        var height = target[0].scrollHeight;
        target.scrollTop(height);
    }
}

var last_check = false,
    checkAlertTimer = 0,
    last_alert = 0,
    is_fetching_message = false,
    last_fetched_message_id = 0;

function showMessageHistory(unread_only) {
    if (is_fetching_message) {
        return;
    }

    if (!unread_only) {
        last_fetched_message_id = 0;
    }

    let conversation_key = $("#html-conversation").data("conversation_key");

    if (conversation_key.length) {
        $.ajax({
            type: 'GET',
            url: 'message/load_messages/' + conversation_key,
            data: {
                unread_only: unread_only,
                last_fetched_message_id: last_fetched_message_id
            },
            success: function (msg) {
                var date = new Date(),
                    year = date.getFullYear(), month = date.getMonth() + 1, day = date.getDate(),
                    properlyFormatted = year + "/" + (month < 10 ? "0" : "") + month + "/" + (day < 10 ? "0" : "") + day + " " + date.getHours() + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + ":" + (date.getSeconds() < 10 ? "0" : "") + date.getSeconds(),
                    obj = $.parseJSON(msg);

                last_check = properlyFormatted;

                if (obj.changed) {
                    var el_thread = $('#html-conversation'),
                    el_messages = el_thread.find('.html-conversation_messages'),
                    html_final = '';

                    $(el_messages).find(".html-initial_message").remove();

                    let previous_listing_id = 0;

                    $.each(obj.messages, function(message_index, _message) {
                        let _listing_id = _message.listing_id || 0;

                        if (_listing_id != 0 && (previous_listing_id != _message.listing_id)) {
                            html_final += '<div class="mb-2 text-center text-muted text-sm">- ';
                            if (_message.is_lister == 'n') {
                                if (_message.is_free == 'y') {
                                    html_final += 'You requested <a title="View listing" href="' + _message.listing_seo + '"><b>' + _message.listing_title + '</b></a> from ' + _message.other_firstname;
                                } else {
                                    html_final += 'You offered <a title="View listing" href="' + _message.listing_seo + '"><b>' + _message.listing_title + '</b></a> to ' + _message.other_firstname;
                                }
                            } else {
                                if (_message.is_free == 'y') {
                                    html_final += _message.other_firstname + ' requested  <a title="View listing" href="' + _message.listing_seo + '"><b>' + _message.listing_title + '</b></a> from you';
                                } else {
                                    html_final += _message.other_firstname + ' offered  <a title="View listing" href="' + _message.listing_seo + '"><b>' + _message.listing_title + '</b></a> to you';
                                }
                            }
                            html_final += ' -</div>';

                            previous_listing_id = _message.listing_id;
                        }
                        last_fetched_message_id = _message.message_id;

                        var div = document.createElement('div');
                        $(div).addClass('message-box');
                        $(div).addClass(_message.d);
                        $(div).append($('<div class="message" />').html(_message.message)).append($('<div class="timestamp" />').html(_message.d.capitalizeFirstLetter() + " " + _message.timeAgo));

                        var row = $('<div class="message-row" />');
                        $(row).addClass(_message.d);
                        row.append(div);

                        var tmp_content = $('<div/>');
                        tmp_content.append(row);

                        html_final += tmp_content.html();
                    });

                    $(el_messages).append(html_final);

                    //if (unread_only) {
                        setTimeout(function () {
                            // force delay. else scrollheight = 0 (for desktop)
                            scrollToBottom(el_messages);
                        }, 300);
                    //}
                }

                is_fetching_message = false;

                if (!unread_only) {
                    $('html,body').animate({
                        scrollTop: $("#input-your_message").offset().top}
                    );

                    $('#input-your_message').focus();
                    document.querySelector('#input-your_message').focus();
                }
            }
        });
    }
}

function showAlert() {
    $.ajax({
        type: 'GET',
        url: 'message/count_unread',
        success: function(response) {
            var obj = $.parseJSON(response);

            if (obj.success) {
                $(".html-count_inbox_unread").html("");

                if (obj.count_unread) {
                    $(".html-count_inbox_unread").html(' (<strong>' + parseInt(obj.count_unread) + '</strong>)');

                    $.each(obj.conversation_keys, function(key, value) {
                        $("#row-conversation-" + value + " .unread").removeClass('d-none');
                    });
                }
            }
        }
    });

    if (checkAlertTimer == 0) {
        checkAlertTimer = setInterval(function () {
            showAlert(false);
        }, 15000);

    }
}

function limitChars(textid, limit, infodiv) {
    var text = $('#' + textid).val(),
        textlength = text.length;

    if (textlength > limit) {
        $('#' + infodiv).html('<span class="errorMsg">You cannot write more than ' + limit + ' characters!</span>');
        $('#' + textid).val(text.substr(0, limit));
        return false;
    } else {
        $('#' + infodiv).html((limit - textlength) + ' characters remaining.');
        return true;
    }
}

function sendMessage(el) {
    var data = {};
    data.message = el.find('textarea[name="message"]').val();

    if (data.message !== '') {
        let sendButton = el.find('button.send_request_message');
        sendButton.html('<div class="loader"></div>');
        sendButton.prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: 'message/send/' + el.data("conversation_key"),
            data: data,
            success: function (msg) {
                var obj = $.parseJSON(msg);

                if (obj.success) {
                    $(el).find('textarea[name="message"]').val('');
                    $(el).find('.text_limit_countdown').html('');

                    showMessageHistory(true);

                    /*scrollToBottom($(el).find('.message_history'));
                    if (typeof window.message_send_callback === 'function') {
                        window.message_send_callback();
                    }*/
                } else {
                    var error = '';
                    $.each(obj.messages, function (key, value) {
                        error += value + "\n";
                    });
                    Alert(error, 'Error');
                }

                sendButton.text('Send Message');
                sendButton.prop('disabled', false);
            }
        });
    }
}


$("body").on("click", ".btn-share", function(e) {
    var title = $(this).prop("title");
    if (navigator.share) {
        e.preventDefault();
        navigator.share({
            title: 'Freestuff',
            text: title,
            url: window.location.href,
        })
            .then(() => console.log('Successful share'))
            .catch((error) => console.log('Error sharing', error));
    }
});
