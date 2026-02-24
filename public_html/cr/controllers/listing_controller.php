<?php
class ListingController extends _Controller
{
    public function index() {
        $filter = new FilterHelper('listings');

        $sql = "SELECT *
        		FROM listing
        		WHERE 1 = 1 ";
        if ($filter->listing_status) {
            $sql .= " AND listing_status = " . quoteSQL($filter->listing_status);
        }

		if ($filter->search) {
			if (is_numeric($filter->search)) {
				$sql .= " AND (MATCH(title, description) AGAINST (" . quoteSQL($filter->search) . ")";
				$sql .= " OR listing_id = " . quoteSQL($filter->search) . ")";
				$sql .= " OR user_firstname LIKE " . quoteSQL("%" . $filter->search . "%");
			} else {
				$sql .= " AND (MATCH(title, description) AGAINST (" . quoteSQL($filter->search) . ")";
				$sql .= " OR user_firstname LIKE " . quoteSQL("%" . $filter->search . "%") . ")";
			}
		}

		$dw_listing = new DataWindowHelper("listing", $sql, "listing_date", "DESC");
        $dw_listing->run();

        BreadcrumbHelper::addBreadcrumbs("Listings", APP_URL . 'listing');

        TemplateHandler::setSideMenu("Listings", "All listings");
        TemplateHandler::setPageTitle("Listings");
        TemplateHandler::setMainView("views/listing/list.php");

        include("templates/standard.php");
    }

    /** to allow user to go back from browsers */
    public function filterList() {
        $filter = new FilterHelper('listings');

        redirect(APP_URL . 'listing');
    }

    public function currentList() {
        $filter = new FilterHelper('new_listings');

        $sql = "SELECT *
				FROM listing
				WHERE listing_status IN ('available','reserved')
				AND listing_type = 'free'";
        if ($filter->search) {
            if (is_numeric($filter->search)) {
                $sql .= " AND (MATCH(title, description) AGAINST (" . quoteSQL($filter->search) . ")";
                $sql .= " OR listing_id = " . quoteSQL($filter->search) . ")";
            } else {
                $sql .= " AND match(title, description) AGAINST (" . quoteSQL($filter->search) . ")";
            }
        }

        $dw_listing = new DataWindowHelper("listing", $sql, "listing_date", "desc",1000);
        $dw_listing->run();

        TemplateHandler::setSideMenu("Listings", "New listings");
        TemplateHandler::setPageTitle("New Listings");
        TemplateHandler::setMainView("views/listing/new_listing.php");

        include("templates/standard.php");
    }


    /**
     * reject the listing
     * @param $listing_id
     */
    public function reject($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
        if ($listing->remove(paramFromGet('reason'))) {
            $user = User::instanceFromId($listing->user_id);
            $user_naughty = new UserNaughty($user->email, $user->mobile, 'Listing rejected',10,paramFromGet('reason'));
            $user_naughty->insert();
            echo '1';
        }
    }

    /**
     * kick the user
     * @param $listing_id
     */
    public function boot($listing_id) {
        # get user_id for the listing
        $listing = Listing::instanceFromId($listing_id);

        $user = new User();
		$user->retrieveFromID($listing->user_id);
		if ($user->ban(paramFromGet('reason'))) {
            echo '1';
        }
    }

    public function wanted($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
		if ($listing->switchToWanted()) {
            echo '1';
        }
    }

    public function close($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
		if ($listing->markAsGone()) {
            echo '1';
        }
    }

    public function delete($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
		if ($listing->delete()) {
            echo '1';
        }
    }

    public function edit($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
        $user = User::instanceFromId($listing->user_id);
        BreadcrumbHelper::addBreadcrumbs("Listings", APP_URL . 'listing');
        BreadcrumbHelper::addBreadcrumbs("Edit");

        TemplateHandler::setSideMenu("Listings", "New listings");
        TemplateHandler::setPageTitle("Listing", "Edit an existing listing");
        TemplateHandler::setMainView("views/listing/edit.php");

        include("templates/standard.php");
    }

    public function save($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
        $listing->buildFromBackendPost();

        if (empty($listing->listing_id)) {
            raiseError("You cannot create listing this way.");

        } elseif ($listing->updateBackend()) {
            echo "1";
            MessageHelper::setSessionSuccessMessage("Listing has been updated");
            die();
        }

        echo json_encode(getErrors());
        die();

    }

    public function viewFullDetails($listing_id) {
        // TODO: make this work, make it faster
        //todo: review adverts, not much money coming in now
        //todo: adsense data should be cached, system tasked
        $sql = "select * from listing where listing_id = " . quoteSQL($listing_id);
        $listing_data = runQueryGetFirstRow($sql);

        $listing = new Listing();
        $listing->retrieveFromID($listing_id);

        $requests = ListingRequest::getRequestsForListing($listing_id);

        $user = new User();
        $user->retrieveFromID($listing->user_id);

        $sql = "SELECT COUNT(DISTINCT(r.report_id)) FROM report r
                JOIN listing l ON l.user_id = ".quoteSQL($listing->user_id)."  AND l.listing_id = r.listing_id";
        $user->times_reported = runQueryGetFirstValue($sql);

        $sql = "SELECT COUNT(DISTINCT(r.report_id)) FROM report r
                WHERE user_id = ".quoteSQL($listing->user_id);
        $user->reported_times = runQueryGetFirstValue($sql);

        //cross-reference ip_addresses and user_ids
        $_user_id_sql = quoteSQL($listing->user_id);
        $_ip_address_sql = quoteSQL($listing->ip_address);
        $sql =
<<<CROSS_REF_SQL
    WITH RECURSIVE cte AS (
        -- Anchor member: Select initial rows that match either user_id or ip_address condition
        SELECT
            listing_id,
            user_id,
            ip_address
        FROM listing
        WHERE user_id = $_user_id_sql OR ip_address = $_ip_address_sql
    
        UNION ALL
    
        -- Recursive member: Join the CTE with itself to recursively find more matches
        SELECT
            l.listing_id,
            l.user_id,
            l.ip_address
        FROM listing l
        INNER JOIN cte c ON l.ip_address = c.ip_address
        LIMIT 1000 -- Limit the depth of recursion to avoid excessive load
    )
    SELECT * FROM cte
    ORDER BY listing_id DESC
    ;
CROSS_REF_SQL;

        $result = runQueryGetAll($sql);

        $ip_addresses = array_unique(
            array_column($result, 'ip_address')
        );
        $user_ids = array_unique(
            array_column($result, 'user_id')
        );

        // we're only using this result
        $listing_ids = array_unique(
            array_column($result, 'listing_id')
        );

//        $ip_addresses = array($listing->ip_address);
//        $user_ids = array($listing->user_id);
//        $listing_ids = array($listing->listing_id);
//        $last_ip_size = 0;
//        $last_user_size = 0;
//        while (sizeof($ip_addresses) != $last_ip_size || sizeof($user_ids) != $last_user_size) {
//            $last_ip_size = sizeof($ip_addresses);
//            $last_user_size = sizeof($user_ids);
//
//            $sql = "select listing_id,user_id,ip_address from listing";
//            $sql .= " where ip_address in (" . arrayToSQLIn($ip_addresses) . ")";
//            $sql .= " or user_id in  (" . arrayToSQLIn($user_ids) . ")";
//            $result = runQueryGetAll($sql);
//            foreach ($result as $row) {
//                if (!in_array($row["ip_address"],$ip_addresses)) {
//                    $ip_addresses[] = $row["ip_address"];
//                }
//                if (!in_array($row["user_id"],$user_ids)) {
//                    $user_ids[] = $row["user_id"];
//                }
//                if (!in_array($row["listing_id"],$listing_ids)) {
//                    $listing_ids[] = $row["listing_id"];
//                }
//            }
//        }

        $sql = "select listing_date,title,description, listing_status, listing_id,ip_address,u.user_id,u.email,u.firstname";
        $sql .= " from listing";
        $sql .= " join user u on u.user_id = listing.user_id";
        $sql .= " where listing.listing_id in (" . arrayToSQLIn($listing_ids) .")";
        $sql .= " order by listing.listing_date desc";
        $other_listings = runQueryGetAll($sql);
        $other_listings = ArrayHelper::nestByColumn($other_listings, 'user_id');

        //reports
        $sql = "select report_id,listing_id,user.user_id,user.firstname,user.email,user.mobile,user.email,report_comment";
        $sql .= " from report ";
        $sql .= " join user on user.user_id = report.user_id";
        $sql .= " where listing_id in (" . arrayToSQLIn($listing_ids) .")";
        $sql .= " order by listing_id desc";
        $reports = runQueryGetAll($sql);
        $reports = ArrayHelper::nestByColumn($reports, 'user_id');

        $reports_user_ids = array_keys($reports);

        if (count($requests)) {
            $requests = ArrayHelper::sortByColumn($requests,"request_timestamp",'DESC');
            foreach ($requests as &$request) {
                $request['message_history'] = array(); //TODO: needs work for new messaging
                if (in_array($request['user_id'], $reports_user_ids)) {
                    $request['reported'] = $reports[$request['user_id']];
                } else {
                    $request['reported'] = array();
                }
            }
            unset($request); // Release the reference
        }

        BreadcrumbHelper::addBreadcrumbs("Listings");
        BreadcrumbHelper::addBreadcrumbs("View Detailed");

        TemplateHandler::setSideMenu("Listings", "View Detailed");
        TemplateHandler::setPageTitle("View Detailed");
        TemplateHandler::setMainView("views/listing/view_full.php");

        include("templates/standard.php");
    }

    public function listReported($user_id) {
        $user = User::instanceFromId($user_id);

        $sql = "SELECT l.listing_id, l.title, r.report_comment, r.report_date FROM listing l JOIN report r ON r.listing_id = l.listing_id WHERE l.user_id = ".quoteSQL($user_id);
        $reported_listings = runQueryGetAll($sql);

        require_once('views/user/reported.php');
    }
}
