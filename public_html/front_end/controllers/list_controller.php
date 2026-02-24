<?php

class ListController extends _Controller {
    public function __construct() {
        PageHelper::setMinifyPageCssName('listings');
    }

    public function index() {
        self::loginRequiredAndRedirect();

        $listing = new Listing();
        $listing->createTempListingId();
        if (isset($_SESSION["session_district"]) && is_object($_SESSION["session_district"])) {
            writeLog($_SESSION["session_district"]);
            $listing->district_id = $_SESSION["session_district"]->district_id;
        }


        PageHelper::setMetaTitle("Freestuff NZ - List an item");
        PageHelper::setMetaDescription("Freestuff NZ - List an item");
        PageHelper::addPageJavascriptOnInitial('js/croppie2.6.2/croppie.min.js');
        PageHelper::addPageJavascriptOnInitial('js/croppie2.6.2/exif.js');


        BreadcrumbHelper::addBreadcrumbs('List An Item');

        PageHelper::setViews("views/list/list_form.php");


        $temp_img = new FileHelper('listing_images', $listing->listing_id);
        $image = $temp_img->getImagePathFromTag("most_recent_upload", 240, 240, "thumbnail", false);
        PageHelper::addJsVar('image', '');
        PageHelper::addJsVar('listing_url', '');
        PageHelper::addJsVar('listing_id', '');


        # set default $user object
        include("templates/main_layout.php");
    }

    public function getImage($temp_id) {
        $temp_id = (int)$temp_id;
        $listing_id = (int)paramFromGet('listing_id');

        if (!empty($temp_id)) {
            $temp_img = new FileHelper('temporary_listing_image', "temp_" . $temp_id);
            $thumbnail = $temp_img->getImagePathFromTag("most_recent_upload", 240, 240);
            echo $thumbnail;

        } elseif (!empty($listing_id)) {
            $temp_img = new FileHelper('listing_images', $listing_id);
            $thumbnail = $temp_img->getImagePathFromTag("most_recent_upload", 240, 240);
            echo $thumbnail;
        }
        die();
    }

    public function save() {
        self::loginRequiredAndEchoJsonError();

        $listing = new Listing();
        $listing->buildFromPost();
        $listing->listing_id = (int)$listing->listing_id;

        $is_edit = (!empty($listing->listing_id));

        if ($is_edit) {
            if ($listing->updateFrontEnd()) {
                if (isset($_POST["image_data"])) {
                    $data = StringHelper::base64_decode($_POST["image_data"]);
                    $fh = new FileHelper("listing_images", $listing->listing_id);
                    if (strlen($data) < 100) {
                        $fh->delete();
                    } else {
                        $fh->importData($data, "image.jpeg");
                    }
                }

                MessageHelper::setSessionSuccessMessage('Your listing has been updated.');

                echo $listing->listing_id;

            } else {
                echo json_encode(hasErrors());
            }

            die();

        } else {
            if (!isset($_POST["pickup"])) {
                raiseError("You must agree to allow the item to be picked up for free", "pickup");
            }

            if ($listing->insertListing()) {
                if (isset($_POST["image_data"])) {
                    $data = StringHelper::base64_decode($_POST["image_data"]);
                    $fh = new FileHelper("listing_images", $listing->listing_id);
                    if (strlen($data) < 100) {
                        $fh->delete();
                    } else {
                        $fh->importData($data, "image.jpeg");
                        $listing->hasImage();
                    }
                }

                User::updateRequestsAndListingsCount(SESSION_USER_ID);
                echo $listing->listing_id;

            } else {
                echo json_encode(getErrors());
            }
        }
        die();
    }

    public function success($listing_id) {
        self::loginRequiredAndRedirect();

        $listing_id = (int)$listing_id;

        $listing = Listing::instanceFromId($listing_id);
        self::_canIDoStuffToThisListing($listing);

        PageHelper::setMetaTitle("Freestuff NZ - List an item");
        PageHelper::setMetaDescription("Freestuff NZ - List an item");

        PageHelper::setViews("views/list/success.php");

        BreadcrumbHelper::addBreadcrumbs('List An Item');
        BreadcrumbHelper::addBreadcrumbs('Completed');

        # set default $user object
        include("templates/main_layout.php");
    }

    protected static function _canIDoStuffToThisListing(Listing $listing, $redirect = TRUE) {
        $can_edit = TRUE;

        if (empty($listing->listing_id)) {
            $can_edit = FALSE;

        } elseif ((!isAdmin()) && (!$listing->isMyListing())) {
            $can_edit = FALSE;
        }

        if ($can_edit) {
            return TRUE;
        }

        if ($redirect) {
            MessageHelper::setSessionErrorMessage("You do not have permission to view this listing");
            redirect(APP_URL . 'my_freestuff');
        }
    }

    public function edit($listing_id) {
        self::loginRequiredAndRedirect();

        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);

        self::_canIDoStuffToThisListing($listing);


        PageHelper::setMetaTitle("Freestuff NZ - Edit my listing");
        PageHelper::setMetaDescription("Freestuff NZ - Edit my listing");
        PageHelper::addPageJavascriptOnInitial('js/croppie2.6.2/croppie.min.js');
        PageHelper::addPageJavascriptOnInitial('js/croppie2.6.2/exif.js');


        TemplateHandler::setSelectedMainTab('my_account');
        PageHelper::setViews("views/list/list_form.php");

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Current listings');
        BreadcrumbHelper::addBreadcrumbs($listing->title, seoFriendlyURLs($listing->listing_id, 'listing', NULL, $listing->title));
        BreadcrumbHelper::addBreadcrumbs('Edit');

        $temp_img = new FileHelper('listing_images', $listing->listing_id);
        $image = $temp_img->getImagePathFromTag("most_recent_upload", 240, 240, "thumbnail", false);
        PageHelper::addJsVar('image', $image);
        PageHelper::addJsVar('listing_url', seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title));
        PageHelper::addJsVar('listing_id', $listing->listing_id);

        # set default $user object
        include("templates/main_layout.php");
    }

    public function confirmStatus($listing_id) {
        $listing_id = (int)$listing_id;

        require_once("views/list/confirm_status_form.php");
        die();
    }

    // TODO: Remove from code if nothing break [CS] 17/07/2024
//    public function processDelist($listing_id) {
//        $listing_id = (int)$listing_id;
//
//        $listing = Listing::instanceFromId($listing_id);
//
//        if (self::_canIDoStuffToThisListing($listing, FALSE)) {
//            $listing->delist();
//        } else {
//            raiseError("You do not have permission to remove this listing.");
//        }
//
//        $output = array();
//        $output['success'] = hasErrors() ? '0' : '1';
//        $output['messages'] = getErrors();
//
//        echo json_encode($output);
//        die();
//    }

    public function relist($listing_id) {
        $listing_id = (int)$listing_id;

        $listing = Listing::instanceFromId($listing_id);

        if (self::_canIDoStuffToThisListing($listing, FALSE)) {
            $new_listing_id = $listing->relist();

            MessageHelper::setSessionSuccessMessage("Your listing has been relisted.");
            redirect(APP_URL . seoFriendlyURLs($new_listing_id, "listing", false, $listing->title));

        } else {
            MessageHelper::setSessionErrorMessage("You do not have permission to relist this listing.");
            //redirect(APP_URL . 'my_freestuff/previous_listing');
        }
    }

    // Mark as taken delist the item and open a modal to add a feedback to the user who took it
    public function markAsGoneModal($listing_id) {
        if (SecurityHelper::isLoggedIn()) {
            $listing_id = (int)$listing_id;
            $listing = Listing::instanceFromId($listing_id);
            $requests = ListingRequest::getRequestsForListing($listing_id);

            ModalHelper::setViews("views/list/mark_as_gone_modal.php");
        } else {
            ModalHelper::setViews("views/report/form_login_required.php");
        }

        include("templates/modal_layout.php");
        exit();
    }

    public function markAsGone($listing_id, $user_id) {
        if (!SecurityHelper::isLoggedIn()) {
            exit();
        }

        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);

        if (self::_canIDoStuffToThisListing($listing)) {
            $listing->markAsGone();
            $user_id = (int)$user_id;
            if ($user_id){
                Thumb::updateThumb(SESSION_USER_ID, $user_id, 'u');
            }
        }
        exit();
    }

    public function markAsReservedModal($listing_id) {
        if (SecurityHelper::isLoggedIn()) {
            $listing = Listing::instanceFromId($listing_id);
            ModalHelper::setViews("views/list/mark_as_reserved_modal.php");
        } else {
            ModalHelper::setViews("views/report/form_login_required.php");
        }

        include("templates/modal_layout.php");
        exit();
    }

    public function markAsReserved($listing_id) {
        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);

        if (self::_canIDoStuffToThisListing($listing)) {
            if ($listing->listing_status == 'reserved') {
                $listing->markAsAvailable();
            } else {
                $listing->markAsReserved();
            }
        }
        exit();
    }

    public function deleteModal($listing_id) {
        if (SecurityHelper::isLoggedIn()) {
            ModalHelper::setViews("views/list/delete_modal.php");
        } else {
            ModalHelper::setViews("views/report/form_login_required.php");
        }

        include("templates/modal_layout.php");
        exit();
    }

    public function delete($listing_id) {
        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);

        if (self::_canIDoStuffToThisListing($listing)) {
            $listing->delete();
        } else {
            raiseError("You do not have permission to delete this listing.");
        }

        $output = array();
        $output['success'] = hasErrors() ? '0' : '1';
        $output['messages'] = getErrors();

        echo json_encode($output);
        die();
    }
}
