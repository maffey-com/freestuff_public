/* Version 1.4 - 19 Feb 2016 */

(function ($) {
    $.fn.formTools2 = function (options) {
        var defaults = {
            debug: false,
            onComplete: false,
            onSuccess: false,
            onFailure: false,
            onStart: false,
            pageScroll: true
        };
        var options = $.extend(defaults, options);
        return this.each(function () {
            var obj = $(this), formId = $(this).attr('id');
            options.form = obj;
            obj.on('submit', function (e) {

                e.preventDefault();
                e.stopPropagation();
                if (typeof options.onStart === "function") {
                    options.onStart();
                }
                var data = obj.serialize(), url = obj.attr('action'), method = obj.attr('method');
                $.ajax({
                    type: method,
                    url: url,
                    data: data,
                    success: function (msg) {
                        if (options.debug === true) {
                            return false;
                        }

                        if (isNaN(msg)) {
                            var rtn = jQuery.parseJSON(msg);
                            if (typeof rtn === "object") {
                                var success = rtn.success;
                            }
                        } else {
                            var success = msg;
                        }

                        if (typeof(rtn) === 'undefined') {
                            rtn = msg;
                        }

                        if (typeof options.onComplete === "function") {
                            options.onComplete(msg);
                        }

                        $('.invalid-feedback').remove();
                        $('.is-invalid').removeClass('is-invalid');
                        if (!isNaN(success) && success !== false) {
                            if (typeof options.onSuccess === "function") {
                                options.onSuccess(rtn);
                            }
                        } else {
                            var space, scroll_to = 10000, move_scroll = false, field_errors = {};
                            if (rtn.errors) {
                                $.each(rtn.errors, function (a,error) {
                                    if (error.field) {
                                        field_errors[error.field] = error.message;
                                    } else {
                                        field_errors[a] = error.message;
                                    }
                                });
                            } else {
                                field_errors = rtn;
                            }
                            $.each(field_errors, function (key, error_msg) {
                                if (key.length) {
                                    var item = $('#' + formId + ' input[name=' + key + '],#' + formId + ' input[type="checkbox"][name="' + key + '[]"],#' + formId + ' textarea[name=' + key + '],#' + formId + ' select[name=' + key + '],#' + formId + ' #' + key);
                                    console.log('#' + formId + ' #' + key);
                                } else {
                                    var item = '';
                                }
                                if (item.length === 0) {
                                    $('#' + formId).append('<div class="invalid-feedback" style="display:block">' + error_msg + '</div>');
                                } else {
                                    if (item.length > 1) {
                                        item = item.last();
                                    }
                                    var error_box = $('<div class="invalid-feedback">' + error_msg + '</div>');
                                    item.parent().append(error_box);
                                    item.bind("change", function () {
                                        $(this).parent().find(".invalid-feedback").fadeOut('300');
                                        $(this).removeClass("is-invalid");
                                    });
                                    item.addClass("is-invalid");
                                }
                                if ($(item).length > 0) {
                                    move_scroll = true;
                                    scroll_to = Math.min(scroll_to, item.offset().top);
                                }
                            });
                            if (options.pageScroll && move_scroll) {
                                $('body,html').scrollTop(scroll_to - 50);
                            }
                            if (typeof options.onFailure === "function") {
                                options.onFailure(rtn);
                            }
                        }
                    }
                });
            });
        });
    }
})(jQuery);