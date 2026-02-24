<?php

class DashboardController extends _Controller {
    public function index() {
        $tmp_hour = date("H");
        if ($tmp_hour < 12) {
            $tmp_msg = 'Good morning';
        } elseif ($tmp_hour < 18) {
            $tmp_msg = 'Good afternoon';
        } else {
            $tmp_msg = 'Good evening';
        }

        # page data
        $sql = "SELECT COUNT(listing_id)
                FROM listing
                WHERE listing_date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                AND listing_status = 'available'
                AND listing_type = 'free'";
        $new_listings_count = runQueryGetFirstValue($sql);
        $sql = "SELECT COUNT(r.report_id)
                FROM report r
                LEFT JOIN user reporter ON reporter.user_id = r.user_id
                JOIN listing ON listing.listing_id = r.listing_id
                JOIN user lister ON lister.user_id = listing.user_id
                WHERE r.report_date >= DATE_SUB(CURDATE(), INTERVAL 3 WEEK)
                AND r.status = 'NEW'";
        $new_report_count = runQueryGetFirstValue($sql);

        $sql = "SELECT COUNT(c.id)
                FROM contact c
                WHERE c.status = 'New'";
        $new_contact_count = runQueryGetFirstValue($sql);

        //$adsense->authenticate();
        //$adsense->fetchEarnings();
        TemplateHandler::setSideMenu("Dashboard");
        TemplateHandler::setPageTitle("Dashboard", $tmp_msg . ", " . paramFromSession('session_realname'));
        TemplateHandler::setMainView("views/dashboard/list.php");

        include("templates/standard.php");
    }


    public function adsense() {
        $adsense = Adsense2::retrieve();
        if ($adsense->hasCurrentData()) {
            include("views/dashboard/adsense.php");
        } elseif ($adsense->hasToken()) {
            try {
                $adsense->fetchEarnings();
                redirect(APP_URL . "/dashboard");
            } catch (Exception $e) {
                echo $e->getMessage();
                echo '<a href="' . Adsense2::adsenseAuthUrl() . '" > Reauthenticate with Google Adsense </a >';
            }
        } else {
            echo '<a href="' . Adsense2::adsenseAuthUrl() . '" > Authenticate with Google Adsense </a >';
        }
    }


    public function googleAdsenseAuthCallback() {
        $adsense = Adsense2::retrieve();
        $adsense->authCodeToToken($_GET['code']);
        $adsense->save();

        $url = APP_URL . "/dashboard";
        header("refresh:5;url=$url");
        echo 'Authentication successful. <br/>You will be redirected in about 5 secs. If not, please click <a href="' . $url . '">here</a>.';
        exit();


    }

    public function cleardate() {
        $adsense = Adsense2::retrieve();
        $adsense->last_updated = '';
        $adsense->save();
    }

    public function breaktoken() {
        $adsense = Adsense2::retrieve();
        $adsense->token['refresh_token'] = 'kdkdkdk';
        $adsense->save();
    }

    /*
    public function oneOff() {
        exit();
        $adsense = Adsense::retrieve();
        $months = $adsense->latestMonths();
        foreach ($months as $m => $data) {
            $dollars = $data['dollars'];
            $int_month = (int)StringHelper::removeNonNumeric($m);
            echo "$int_month: $dollars <br/>";
            $sql = "update stats_monthly set adsense_earnings = $dollars where month = $int_month";
            runQuery($sql);
        }
        echo "Done";
    }
*/
    /* public function oneOff() {

        $sql = "SELECT user_id, user_region as region FROM `user`
        WHERE district_id IS NULL";
        $users = runQueryGetAll($sql);
        foreach ($users as $user) {

            switch ($user['region']) {
                case 'Northland':
                    $district_id = 9;
                    break;
                case 'Auckland':
                    $district_id = 11;
                    break;
                case "Waikato":
                    $district_id = 35;
                    break;
                case "Bay of Plenty":
                    $district_id = 60;
                    break;
                case "Gisborne":
                    $district_id = 64;
                    break;
                case "Hawkes Bay":
                    $district_id = 67;
                    break;
                case "Taranaki":
                    $district_id = 72;
                    break;
                case "Manawatu-Wanganui":
                    $district_id = 78;
                    break;
                case 'Wellington':
                    $district_id = 97;
                    break;
                case 'Nelson-Tasman':
                    $district_id = 101;
                    break;
                case 'Marlborough':
                    $district_id = 104;
                    break;
                case 'West Coast':
                    $district_id = 107;
                    break;
                case 'Canterbury':
                    $district_id = 115;
                    break;
                case 'Otago':
                    $district_id = 136;
                    break;
                case 'Southland':
                    $district_id = 149;
                    break;
            }

            $sql = "UPDATE `user` SET district_id = " . $district_id . " WHERE user_id = " . $user['user_id'];
            runQuery($sql);
            echo $sql . "<br/>";
        }
    }*/
}

