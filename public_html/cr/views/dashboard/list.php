<div id="quick-actions" style="display: block; position: static; visibility: visible;" class="active">
    <ul class="statistics">
        <li>

            <div class="top-info">
                <a class="blue-square" title="" href="<?=(APP_URL)?>listing/current_list"><i class="icon-plus"></i></a>
                <strong><?= ($new_listings_count) ?></strong>
            </div>
            <div class="progress progress-micro">
                <div style="width: 60%;" class="bar"></div>
            </div>
            <span>New Listings</span>
        </li>
        <li>
            <div class="top-info">
                <a class="red-square" title="" href="<?=(APP_URL)?>report"><i class="icon-hand-up"></i></a>
                <strong><?= ($new_report_count) ?></strong>
            </div>
            <div class="progress progress-micro">
                <div style="width: 20%;" class="bar"></div>
            </div>
            <span>New Reports</span>
        </li>
        <li>
            <div class="top-info">
                <a class="purple-square" title="" href="<?=(APP_URL)?>report_contact?filter_status=New"><i class="icon-shopping-cart"></i></a>
                <strong><?= ($new_contact_count) ?></strong>
            </div>
            <div class="progress progress-micro">
                <div style="width: 90%;" class="bar"></div>
            </div>
            <span>New Contacts</span>
        </li>

    </ul>
</div>

<div class="row-fluid adsense">
    Fetching adsense data...
</div>


<script>
    $(".adsense").load("<?=APP_URL?>dashboard/adsense");
</script>