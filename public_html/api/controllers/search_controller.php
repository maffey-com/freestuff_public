<?php

class SearchController extends _Controller {
    public function __construct() {
    }


    public function filter() {
        $q = paramFromGet('q');
        $regions = paramFromGet('regions');
        $sql = "SELECT l.listing_id,l.title,d.region,d.district,substring(l.description,1,145)  description,if(l.user_id = ".SESSION_USER_ID.",true,false) is_my_listing, listing_type
                FROM listing l 
                    join district d on d.district_id = l.district_id
                LEFT JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('available', 'reserved')";
        $sql .= empty($q) ? "" : " AND (MATCH(title, description) AGAINST (" . quoteSQL($q) . ") OR listing_id = " . quoteSQL(preg_replace("/[^0-9]/", "", $q)) . ")";
        if (!empty($regions)) {
            $sql .= " AND l.district_id in (select district_id from district where region in " . quoteIN(explode("|",$regions)) .")" ;
        }
        $listings = new DataWindowHelper("search ", $sql, "listing_date", "desc", paramFromGet('page_size', 20));
        if (paramFromGet("page")) {
            $listings->current_page = paramFromGet("page");
        }
        $listings->run();



        echo json_encode($listings->data);
    }

    public function add() {
        self::loginRequiredAndEchoJsonError();

        $saved_search = new SavedSearch();
        $saved_search->search_string = paramFromPost('q');
        $saved_search->setMyDefaults();
        $saved_search->save();

        echo json_encode(array("success" => true));
    }

    public function getSaved() {
        self::loginRequiredAndEchoJsonError();

        $saved_searchs = SavedSearch::mySavedSearches();
        echo json_encode($saved_searchs);
    }

    public function detail($search_id) {
        self::loginRequiredAndEchoJsonError();

        $search_id = (int)$search_id;

        $saved_search = SavedSearch::instanceFromId($search_id);
        if ($saved_search->isMine()) {
            echo json_encode($saved_search);
        } else {
            ErrHelper::raise('Not your search','403');
            self::echoJsonErrorsAndExit();
        }
    }

    public function save() {
        self::loginRequiredAndEchoJsonError();

        $saved_search = new SavedSearch();
        $saved_search->buildFromPost();
        $saved_search->user_id = SESSION_USER_ID;
        $saved_search->regions = explode(',', $saved_search->regions);
        $saved_search->listing_type = explode(',', $saved_search->listing_type);
        $saved_search->save();

        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            echo '1';
        }
    }

    public function delete($search_id) {
        self::loginRequiredAndEchoJsonError();
        $search_id = (int)$search_id;

        $saved_search = SavedSearch::instanceFromId($search_id);
        if ($saved_search->isMine()) {
            $saved_search->expire();
        } else {
            ErrHelper::raise('Not your search','403');

        }

        if (ErrHelper::hasErrors()) {
            self::echoJsonErrorsAndExit();
        } else {
            echo '1';
        }
    }
}
