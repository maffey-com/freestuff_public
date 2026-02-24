<script type='text/javascript'>
    $(document).ready(function () {

        $("#list-listings .action-reject").click(function () {
            var el_listing = $(this).closest("tr");
            var listing_id = el_listing.data("listing_id");

            bootbox.prompt("Reason to reject listing?", function (reason) {
                if (reason == '') {
                    alert("Reason is required");

                } else {
                    $.ajax({
                        url: '<?=(APP_URL)?>listing/reject/' + listing_id,
                        data: ({
                            reason: reason
                        }),
                        success: function (msg) {
                            el_listing.remove();
                        }
                    });
                }
            });
        });

        $("#list-listings .action-boot").click(function () {
            var el_listing = $(this).closest("tr");
            var listing_id = el_listing.data("listing_id");

            bootbox.prompt("Reason to ban this user?", function (reason) {
                if (reason == '') {
                    alert("Reason is required");

                } else {
                    $.ajax({
                        url: '<?=(APP_URL)?>listing/boot/' + listing_id,
                        data: ({
                            reason: reason
                        }),
                        success: function (msg) {
                            el_listing.remove();
                        }
                    });
                }
            });
        });

        $("#list-listings .action-wanted").click(function () {
            var el_listing = $(this).closest("tr");
            var listing_id = el_listing.data("listing_id");

            $.ajax({
                url: '<?=(APP_URL)?>listing/wanted/' + listing_id,
                success: function (msg) {
                    el_listing.remove();
                }
            });
            return false;
        });

        $("#list-listings .action-close").click(function () {
            var el_listing = $(this).closest("tr");
            var listing_id = el_listing.data("listing_id");

            $.ajax({
                url: '<?=(APP_URL)?>listing/close/' + listing_id,
                success: function (msg) {
                    el_listing.remove();
                }
            });
            return false;
        });

        $("#list-listings .action-delete").click(function () {
            var el_listing = $(this).closest("tr");
            var listing_id = el_listing.data("listing_id");

            $.ajax({
                url: '<?=(APP_URL)?>listing/delete/' + listing_id,
                success: function (msg) {
                    el_listing.remove();
                }
            });
            return false;
        });
    });
</script>