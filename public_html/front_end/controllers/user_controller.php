<?php

class UserController extends _Controller {
    public function allListings($user_id) {
        $user_id = (int)$user_id;

        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type', 'free');

        $sql = "SELECT l.*,u.firstname
                FROM listing l
                JOIN user u ON l.user_id = u.user_id
                WHERE l.listing_status IN ('available','reserved')
                AND u.user_id = " . quoteSQL($user_id);
        if ($listing_filter->listing_type != 'all') {
          //  $sql .= " AND listing_type =" . quoteSQL($listing_filter->listing_type);
        }

        $listings = new DataWindowHelper("browse", $sql, "listing_date", "desc", 10);
        $listings->run();
        $paging = $listings->getPaging();

        $user = User::instanceFromId($user_id);

        PageHelper::setViews('views/search/banner.php', "views/search/search_results.php");

        BreadcrumbHelper::addBreadcrumbs("Listings in " . District::resolveRegionName($user->district_id), APP_URL.'browse/by-region/' . District::resolveRegionName($user->district_id));
        BreadcrumbHelper::addBreadcrumbs("Listings from " . $user->firstname);

        include("templates/main_layout.php");
    }
//
//
//    public function blockUserModal($other_user_id) {
//        $other_user_id = (int)$other_user_id;
//
//        if (SecurityHelper::isLoggedIn()) {
//            ModalHelper::setViews("views/user_block/block_user_modal.php");
//        } else {
//            ModalHelper::setViews("views/report/form_login_required.php");
//        }
//
//        include("templates/modal_layout.php");
//        exit();
//    }
//
//    public function blockUser($other_user_id) {
//        $other_user_id = (int)$other_user_id;
//
//        $hide_messages = paramFromPost('hide_messages', 'n');
//        if (!in_array($hide_messages, array('y', 'n'))) {
//            $hide_messages = 'n';
//        }
//
//        $user = User::instanceFromId($other_user_id);
//        if (!$user) {
//            return json_encode(['success' => false]);
//        }
//
//        if (UserBlocked::blockUser(SESSION_USER_ID, $other_user_id, $hide_messages)) {
//            $success = true;
//        } else {
//            $success = false;
//        }
//
//        echo json_encode(['success' => $success]);
//    }
//
//    public function unblockUser($other_user_id) {
//        $other_user_id = (int)$other_user_id;
//
//        $user = User::instanceFromId($other_user_id);
//        if (!$user) {
//            return;
//        }
//
//        if (UserBlocked::unblockUser(SESSION_USER_ID, $other_user_id)) {
//            $success = true;
//        } else {
//            $success = false;
//        }
//
//        echo json_encode(['success' => $success]);
//    }

}
