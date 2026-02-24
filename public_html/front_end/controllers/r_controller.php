<?php

//allows nice short urls

class RController {
    /**
     * Email Validation
     *
     * @param $verify_id
     * @param $code
     */
    function v($verify_id, $code) {
        redirect(SITE_URL . 'user_verify/email_process/' . $verify_id . "?code=" . $code);
    }

    /**
     * View Listing
     *
     * @param $listing_id
     */
    function l($listing_id) {
        $sql = "SELECT title FROM listing WHERE listing_id = " . quoteSQL($listing_id);
        $result = runQueryGetFirstRow($sql);
        if ($result) {
            $url = seoFriendlyURLs($listing_id, 'listing', false, $result['title']);
            redirect(SITE_URL . $url);
        } else {
            redirect(SITE_URL);
        }
    }

    /**
     * My Previous Listings
     */
    function p() {
        redirect(SITE_URL . 'my_freestuff/listings/previous');
    }

    /**
     * Message view
     *
     * @param $request_id
     */
    function m($request_id) {
        redirect(SITE_URL . 'request_message/view/' . $request_id);
    }

    /**
     * Saved Searches
     */
    function s() {
        redirect(SITE_URL . 'search');
    }

    /**
     * Saved Searches
     */
    function t() {
        redirect(SITE_URL . 'search');
    }

    /**
     * Delete all saved searches
     */
    function d() {
        redirect(SITE_URL . 'account/delete_all_saved_searches');
    }

}