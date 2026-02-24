<div class="table-bordered table-striped table-hover" id="list-record">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="120">&nbsp;</th>
                <th colspan="2" style="ali">Views</th>
                <th colspan="2">New listings</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th width="120">&nbsp;</th>
            </tr>
            <tr role="row">
                <th>Date</th>
                <th class="align-right">Item</th>
                <th class="align-right">Category</th>
                <th class="align-right">Free</th>
                <th class="align-right">Wanted</th>
                <th class="align-right">New members</th>
                <th class="align-right">Mobile validation</th>
                <th class="align-right">Contact</th>
                <th class="align-right">Adsense</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        while ($display_month >= $start_month) {
	        $data = paramFromHash($display_month, $month_stats, array());

            $row_listing_views = (int)paramFromHash('listing_views', $data);
            $row_category_views = (int)paramFromHash('category_views', $data);
            $row_new_free_listings = (int)paramFromHash('new_free_listings', $data);
            $row_new_wanted_listings = (int)paramFromHash('new_wanted_listings', $data);
            $row_new_users = (int)paramFromHash('new_users', $data);
            $row_mobile_validations = (int)paramFromHash('mobile_validations', $data);
            $row_contacts = (int)paramFromHash('contacts', $data);
            $row_adsense = (float)paramFromHash('adsense_earnings', $data);

            ?>
            <tr >
                <td>
                    <?=(date('F Y', strtotime(substr($display_month, 0, 4) . "-" . substr($display_month, 4, 2) . "-01")))?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_listing_views)) ?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_category_views)) ?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_new_free_listings)) ?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_new_wanted_listings)) ?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_new_users)) ?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_mobile_validations)) ?>
                </td>
                <td class="align-right">
                    <?= (number_format($row_contacts)) ?>
                </td>
                <td class="align-right">
                    $<?= (number_format($row_adsense,2)) ?>
                </td>
                <td>
                    <a class="btn" href="<?=(APP_URL)?>report_stats/build_month/<?=($display_month)?>"><i class="ico-repeat"></i> Re-calculate</a>
                </td>
            </tr>
            <?
            $display_month = (int)$display_month -1;

            if (substr((string)$display_month, 4, 2) == "00") {
                $display_month = (int)$display_month -88;
            }
        } ?>
        </tbody>
    </table>
</div>


