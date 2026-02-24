function closePopup(){
    $('body').find('#darkBack').remove();
    $('#lastPopup').fadeOut('slow');
    $('#lastPopup').html('');
    $('#lastPopup').remove();
}

(function($){
    /***************************
     * really handy examples **
     $('.popup_link').popupBox();

        $('.popup_link_two').popupBox({
            dimBackground: false,
            background: '#a5cfe5',
            border:'10',
            borderRadius: 0,
            height: 30,
            width: 200,
            dropShadow: false
        });

        $('.popup_link_three').popupBox({
            message: 'You just pressed the button'
        });

        $('#linked_event').click(function(){
            $(this).popupBox({
                chainedEvent: true,
                message: "Yer a fanny Baw"
            });

        });
    ******************************/

    var closeBtn = $("<a href='#' class='popupBoxCloseBtn' style='float:right; font-size:12px'>x close</a>");
    var printBtn = $("<a href='#' class='popupBoxCloseBtn' style='float:left; font-size:12px'>print</a>");
    var theBox;
    var darkBack = $("<div id='darkBack'></div>");

    var darkCss = {
        'background': '#000000',
        'position': 'fixed',
        'height': '100%',
        'width': '100%',
        'top':'0',
        'left': '0',
        'z-index': '4000',
        'display': 'none'
    };

    function makeTheBox(obj, options,$this){

        if(options.event != 'hover'){
            $(theBox).remove();
        }
        else{
            $(obj).mouseout(function(){
                $(theBox).fadeOut('slow', function(){
                    $(theBox).remove();
                });
            });
        }
        if(options.position == 'fixed' || options.position == 'absolute'){
            var marginTop = options.height/2;
            var marginLeft = options.width/2
        }
        theBox = $("<div id='lastPopup'></div>");

        var cssObj = {
            'background-color':options.background,
            'width':options.width,
            'height':options.height,
            '-moz-border-radius':options.borderRadius+"px",
            'border-radius': options.borderRadius+"px",
            'border':options.border+"px solid "+options.borderColour,
            'position':options.position,
            'top':options.top,
            'left':options.left,
            'margin-top':"-"+marginTop+"px",
            'margin-left':"-"+marginLeft+"px",
            'padding':options.padding,
            'overflow':'auto',
            'display':'none',
            'z-index':options.zIndex
        }

        if(options.dropShadow == true){
            var shadowCss = {'-moz-box-shadow': '0 0 20px #000',
            '-webkit-box-shadow': '0 0 20px #000',
            'box-shadow': '0 0 20px #000'}
            $(theBox).css(shadowCss);
        }

        $(theBox).css(cssObj);
        $(closeBtn).click(function(){
            $(theBox).fadeOut();
            $(theBox).remove();
            $(darkBack).fadeOut();
            if (typeof options.onClose == "function"){
				options.onClose();
			}
        });

        $(printBtn).click(function(){
           $("#popup_box_content").printElement( {
            printBodyOptions:
            {
                styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;-moz-box-shadow:none,-webkit-box-shadow:none,box-shadow:none'
            },
            pageTitle:options.printPageTitle
            });
            return true;
        });

        if (options.clickOutsideClose == true) {
            $(darkBack).click(function(){
                $(theBox).fadeOut();
                $(theBox).remove;
                $(darkBack).fadeOut();
                if (typeof options.onClose == "function"){
                    options.onClose();
                }
            });
        }

         if (options.showPrintButton) {
            $(theBox).append(printBtn);
        }
        if (!options.hideCloseButton) {
            $(theBox).append(closeBtn);
        }
        $(theBox).append("<br style='clear:both' />");


        if(options.message == false){
            if (options.chainedEvent) {
                var page = options.href;
            } else {
                var page = $this.attr('href');
            }

            $.ajax({
                type: 'GET',
                url: page,
                success:function(msg){
                    $(theBox).append("<div id='popup_box_content'>" + msg + "</div>");

                },
                statusCode: {
                    404: function() {
                        $(theBox).append('<span style="color:red">!!! - The page being called was not found - !!!</span>');
                    }
                }
            });
        }
        else{
            $(theBox).append("<div id='popup_box_content'>" + options.message + "</div>");
        }

        $('body').append(theBox);

        if(options.dimBackground == true){
            $(darkBack).css(darkCss);
            $('body').append(darkBack);
            $(darkBack).fadeTo('slow', 0.5);

        }
        $(theBox).fadeIn('slow');
    }


    $.fn.popupBox = function(options){

        /** These are the editable params for the plugin **/
        var defaults = {
            width: 400,
            height: 300,
            background:'#ffffff',
            border: 2,
            borderColour: '#cacaca',
            borderRadius: 10, /** only for chrome and firefox **/
            position: 'fixed',
            top: '50%',
            left: '50%',
            margin: '-200px 0 0 -150px',
            dropShadow: true,
            padding: 10,
            event: 'click',
            chainedEvent: false,
            message: false,
            dimBackground: true,
            zIndex: 5000,
            hideCloseButton: false,
            showPrintButton: false,
            printPageTitle: 'Your Printout',
            onClose: false,
            clickOutsideClose: true
        };

        /** end user params **/

        var options = $.extend(defaults, options);

        return this.each(function(){
            var obj = $(this);

            if(options.chainedEvent != true){
                if(options.event=='click'){
                    $(obj).click(function(){
                        makeTheBox(obj, options,$(this));
                        return false;
                    });
                }
                else if(options.event=='hover'){
                    $(obj).mouseover(function(){
                        makeTheBox(obj, options,$(this));
                        return false;
                    });
                } else {
                    $(obj).text("The 'event' parameter is incorrect");
                }
            } else{
                makeTheBox(obj, options,$(this));
            }
        });
    };
})(jQuery);


