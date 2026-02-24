    <div class="span6">
        <div class="navbar">
            <div class="navbar-inner"><h6>Adsense - Monthly</h6></div>
        </div>
        <table class="table">
            <tr>
                <th>Month</th>
                <th style='width:100px;text-align:right'>Total</th>
                <th style='width:100px;text-align:right'>Daily</th>
            </tr>
            <? foreach ($adsense->latestMonths() as $array) { ?>
                <tr>
                    <td><?= $array["pretty_date"] ?></td>
                    <td style='width:100px;text-align:right'>$<?=(number_format($array["dollars"]??0, 2))?></td>
                    <td style='width:100px;text-align:right'>$<?=($array["per_day"]??0)?></td>
                </tr>
            <? } ?>
        </table>
    </div>

    <div class="span6">
        <div class="navbar">
            <div class="navbar-inner"><h6>Adsense - Daily</h6></div>
        </div>
        <table class="table">
            <tr>
                <th>Day</th>
                <th style='width:100px;text-align:right'>Total</th>
            </tr>
            <? foreach ($adsense->latestDays() as $date => $dollars) { ?>
                <tr>
                    <td><?=date("d F", strtotime($date))?></td>
                    <td style='width:100px;text-align:right'>$<?=($dollars)?></td>
                </tr>
            <? } ?>
        </table>
    </div>
    <?php


