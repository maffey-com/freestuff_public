$(function () {
    var cropper = $('#picture').croppie({
        url: $php.image,
        enableExif: false,
        viewport: {
            width: 260,
            height: 260
        },
        boundary: {
            width: 300,
            height: 300
        },
        enableOrientation: true,

    });

    cropper.croppie('bind', {url: $php.image, zoom: 0});

    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#picture').addClass('ready');
                $('#rotate').removeClass('d-none');
                cropper.croppie('bind', {
                    url: e.target.result,
                    zoom: 0
                });
            };
            reader.readAsDataURL(input.files[0]);
        }
        else {
            alert("Sorry - you're browser doesn't have the required features.");
        }
    }

    $('#upload').on('change', function () {
        readFile(this);
    });

    $('#rotate').on('click', function () {
        cropper.croppie('rotate', -90);
    });

    cropper.on('update.croppie', function (ev, cropData) {
        cropper.croppie('result', {type: 'base64', size: {width: 800}, format: 'jpeg'}).then(function (data) {
            $("input[name='image_data']").val(data);
        });
    });

    function adjustReservedPosition() {
        $('.sold-out-image').each(function () {
            //gets the height of the h4 right above it
            let h4_height = $(this).parent().prev().height() + 25;
            //sets the top position of the sold out to the bottom of the h4
            $(this).css('top', h4_height + 'px');
        });
    }

    // TODO: Remove from code if nothing break [CS] 17/07/2024
    // $('.btn-delist').click(function (e) {
    //     e.preventDefault();
    //
    //     $.ajax({
    //         url: 'list/process_delist/' + $(this).data('listing_id'),
    //     }).done(function (new_status) {
    //         window.location.href = 'my_freestuff#previous';
    //     });
    // });

    $('.btn-submit-form').click(function (e) {
        e.preventDefault();
        $(this).attr('disabled',true);
        $('#list_form').submit();
    });

    $('.btn-cancel-edit').click(function (e) {
        e.preventDefault();
        window.location.href = $(this).data('return')
    });

    $('#list_form').formTools2({
        successMsg: false,
        onComplete: function (listing_id) {
            $('.btn-submit-form').attr('disabled', false);
        },
        onSuccess: function(listing_id) {
            if ($php.listing_url) {
                document.location = $php.listing_url;
            } else {
                document.location = "list/success/" + listing_id;
            }
        }
    });

    $('#list_form #listing_type input').change(function () {
        var val = $(this).val();
        $('.agree').toggleClass('d-none', true);
        $('#agree-' + val).toggleClass('d-none', false);
    });

});
