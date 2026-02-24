<?php

class ListingController extends _Controller {

    public function topListings() {
        $sql = "SELECT listing_id,title,district_id,description
                FROM listing 
                WHERE listing_status = 'available' 
                AND has_image = 'y' 
                AND listing_type = 'free' 
                ORDER BY rand() 
                LIMIT 1";
        $random_approved_items = runQueryGetAll($sql);

        foreach ($random_approved_items as &$item) {
            $item['region'] = 'Thank you';
            $item['district'] = ' 30th November 2025, Thank you for your support';
            $item["title"] = "going offline";
        }

        $items = array(
            array(
                "listing_id" => 1,
                "title" => "Freestuff is going offline",
                "district_id" => 22,
                "description" => "We are not taking new listins, website will be offline 30th November 2025",
                "region" => "Auckland",
                "district" => "New Lynn, Auckland")
        );

        echo json_encode($random_approved_items);
    }

    public function imageThumb($listing_id) {
        $temp_img = new FileHelper('listing_images', $listing_id);
        $tmp_img = $temp_img->getImagePathFromTag("most_recent_upload", 80, 80);
        header('Content-Type: image/jpeg');
        readfile($tmp_img);
    }

    public function image($listing_id) {
        $temp_img = new FileHelper('listing_images', $listing_id);
        $tmp_img = $temp_img->getImagePathFromTag("most_recent_upload", 600, 600);
        header('Content-Type: image/jpeg');
        readfile($tmp_img);
    }

    public function detail($listing_id) {
        $listing = Listing::instanceFromId($listing_id);
        if (empty($listing->listing_id)) {
            echo json_encode(array("error" => "No such listing"));
        }

        $listing->users_active_listings = count(Listing::getAllListingFromUserId($listing->user_id));
        Listing::profileMark($listing_id, 'A');

        $bar = new stdClass();
        $bar->listing_id = $listing->listing_id;
        $bar->user_id = $listing->user_id;
        $bar->title = $listing->title;
        $bar->description = $listing->description;
        $bar->can_be_requested = in_array($listing->listing_status, array('available', 'reserved'));
        $bar->listing_date = $listing->listing_date;
        $bar->visits = $listing->visits;
        $bar->users_active_listings = $listing->users_active_listings;
        $bar->my_request_id = $listing->haveIRequestedThisItem();
        $bar->has_image = $listing->has_image;
        $bar->user_firstname = $listing->user_firstname;
        $bar->district_id = $listing->district_id;
        $bar->listing_type = $listing->listing_type;
        $bar->request_count = $listing->request_count ?: 0;
        $bar->is_my_listing = $listing->is_my_listing;
        $bar->ago = DateHelper::ago($listing->listing_date);
        $bar->requests = ($listing->request_count ?: '0') . '/' . REQUESTS_PER_ITEM_HARD_LIMIT;

        if ($listing->is_my_listing) {
            $bar->request_text = $bar->request_count == 0 ? 'No requests' : (StringHelper::singularOfPlural($bar->request_count, 'request', 'requests') . ' for this item');
        } else {
            if ($bar->my_request_id) {
                if ($bar->request_count > 1) {
                    $bar->request_text = "You have requested this item\n" . ($bar->request_count) . " out of " . REQUESTS_PER_ITEM_HARD_LIMIT . " requests";
                } else {
                    $bar->request_text = "You have requested this item";
                }
            } else {
                $bar->request_text = $bar->request_count == 0 ? 'No requests for this item yet'
                    : ($listing->request_count ?: '0') . ' out of ' . REQUESTS_PER_ITEM_HARD_LIMIT . " requests for this item";
            }
        }
        $bar->max_requests = $bar->request_count >= REQUESTS_PER_ITEM_HARD_LIMIT;

        //this needs to be replace with the new request credit system
        if (User::requestsToday(SESSION_USER_ID) >= 10) {
            $bar->user_request_limit = true;
        } else {
            $bar->user_request_limit = false;
        }
        $bar->region = District::resolveRegionName($bar->district_id);
        $bar->district = District::display2($bar->district_id);

        echo json_encode($bar);

    }

    public function index($browsing_category) {
        $browsing_category = trim($browsing_category);

        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type', 'free');

        $ip = paramFromHash('REMOTE_ADDR', $_SERVER);

        $sql = "SELECT l.listing_id,l.title,d.region,d.district,substring(l.description,1,145) description
                FROM listing l 
                    join district d on d.district_id = l.district_id
                LEFT JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('available', 'reserved')
                AND d.region = " . quoteSQL($browsing_category);
        if ($listing_filter->listing_type != 'all') {
            $sql .= " AND listing_type =" . quoteSQL($listing_filter->listing_type);
        }

        $listings = new DataWindowHelper("browse", $sql, "listing_date", "desc", 10);
        if (paramFromGet("page")) {
            $listings->current_page = paramFromGet("page");
        }
        $listings->run();

        //profile mark category
        $sql = "INSERT category_profile_mark SET
                category = " . quoteSQL($browsing_category) . ", 
                user_id = " . quoteSQL(SESSION_USER_ID) . ",
                date = NOW(),
                ip_address = " . quoteSQL('API');
        runQuery($sql);

        echo json_encode($listings->data);
    }


    public function myCurrent() {
        self::loginRequiredAndEchoJsonError();

        $sql = "SELECT *
                FROM listing l
                JOIN user u on l.user_id = u.user_id
                WHERE l.listing_status IN ('available', 'reserved')
                AND u.user_id = " . quoteSQL(SESSION_USER_ID);

        $listings = new DataWindowHelper("current", $sql, "listing_date", "desc", 10);
        if (paramFromGet("page")) {
            $listings->current_page = paramFromGet("page");
        }
        $listings->run();
        echo json_encode($listings->data);
    }

    public function myPrevious() {
        self::loginRequiredAndEchoJsonError();

        $filter_age = (int)paramFromGet("age", 300);
        $filter_age = in_array($filter_age, array(0, 30, 60, 90)) ? $filter_age : $filter_age;

        $sql = "SELECT *
                FROM listing l
                JOIN user u on l.user_id = u.user_id
                WHERE l.listing_status IN ('gone', 'expired')
                AND u.user_id = " . quoteSQL(SESSION_USER_ID);
        $sql .= empty($filter_age) ? '' : " AND DATEDIFF(CURDATE(), l.listing_date) <= " . (int)$filter_age;

        $listings = new DataWindowHelper("previous", $sql, "listing_date", "desc", 10);
        if (paramFromGet("page")) {
            $listings->current_page = paramFromGet("page");
        }
        $listings->run();
        echo json_encode($listings->data);
    }

    public function myWatchlist() {
        self::loginRequiredAndEchoJsonError();

        $filter_age = (int)paramFromGet("age", 30);
        $filter_age = in_array($filter_age, array(0, 30, 60, 90)) ? $filter_age : $filter_age;

        $sql = "SELECT *
                FROM listing l
                join listing_request lr on lr.listing_id  = l.listing_id and lr.user_id = " . quoteSQL(SESSION_USER_ID);
        $sql .= empty($filter_age) ? '' : " WHERE DATEDIFF(CURDATE(), lr.request_timestamp) <= " . (int)$filter_age;

        $listings = new DataWindowHelper("watch", $sql, "lr.request_timestamp", "desc", 10);
        if (paramFromGet("page")) {
            $listings->current_page = paramFromGet("page");
        }
        $listings->run();
        echo json_encode($listings->data);
    }

    public function byRegion($region) {
        $this->index($region);
    }

    public function toggleWanted() {
        $show_wanted = paramFromGet('show_wanted');

        if (empty($show_wanted)) {
            $_SESSION["listing_filter"] = array('free');
        } else {
            $_SESSION["listing_filter"] = explode(',', $show_wanted);
        }
        exit();
    }


}
