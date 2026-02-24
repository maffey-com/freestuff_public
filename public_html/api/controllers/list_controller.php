<?php

class ListController extends _Controller {
    public function __construct() {
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

    public function getRequests($listing_id) {
        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);
        $requests = ListingRequest::getRequestsForListing($listing_id);

        foreach ($requests as $request_id => &$request) {
            $my_thumbs = Thumb::getThumbsGiven(array($request['user_id']));
            $request['my_thumbs'] = $my_thumbs[$request['user_id']] ?? 'x';
            $request['message_ago'] = DateHelper::ago($request["message_timestamp"]);
        }

        $output = array();
        $output['is_my_listing'] = $listing->isMyListing();
        $output['requests'] = $requests;
        $output['title'] = $listing->title;

        echo json_encode($output);
    }

    public function save() {
        raiseError("We are not accepting any more listings", 'title');
        raiseError("We are not accepting any more listings", 'description');
        self::echoJsonErrorsAndExit();
        self::loginRequiredAndEchoJsonError();

        $listing = new Listing();
        $listing->buildFromPost();
        $listing->listing_id = (int)$listing->listing_id;

        $is_edit = (!empty($listing->listing_id));

        if ($is_edit) {
            if ($listing->updateFrontEnd()) {
                self::echoJsonSuccessAndExit(array("listing_id" => $listing->listing_id));
            } else {
                self::echoJsonErrorsAndExit();
            }
        } else {
            if ($listing->insertListing()) {
                $relist_listing_id = paramFromPost('relist_listing_id', false);
                $copy_relisting_image = paramFromPost('copy_relisting_image', false);

                //
                if ($relist_listing_id && $copy_relisting_image) {
                    $fh = new FileHelper("listing_images", $relist_listing_id);
                    $fh->cloneFileHelper("listing_images", $listing->listing_id);
                }

                User::updateRequestsAndListingsCount(SESSION_USER_ID);
                self::echoJsonSuccessAndExit(array("listing_id" => $listing->listing_id));

            } else {
                self::echoJsonErrorsAndExit();
            }
        }
        die();
    }

    public function uploadImage() {
        self::loginRequiredAndEchoJsonError();
        $listing = new Listing();
        $listing->retrieveFromID(paramFromPost('listing_id'));
        self::_canIDoStuffToThisListing($listing);
        $fh = new FileHelper("listing_images", $listing->listing_id);
        $fh->setFileField('image');
        $fh->processUploadQueue();

        //make sure image is replaced rather than added to
        /*
         *   $data = StringHelper::base64_decode($_POST["image_data"]);
                $fh = new FileHelper("listing_images", $listing->listing_id);
                if (strlen($data) < 100) {
                    $fh->delete();
                } else {
                    $fh->importData($data, "image.jpeg");
                }
         */

    }

    protected static function _canIDoStuffToThisListing(Listing $listing) {
        $can_edit = TRUE;

        if (empty($listing->listing_id)) {
            $can_edit = FALSE;

        } elseif ((!isAdmin()) && (!$listing->isMyListing())) {
            $can_edit = FALSE;
        }

        if ($can_edit) {
            return TRUE;
        }


        ErrHelper::raise('Not your listing');
        self::echoJsonErrorsAndExit();
    }

    public function edit($listing_id) {
        self::loginRequiredAndEchoJsonError();

        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);

        self::_canIDoStuffToThisListing($listing);

        $listing->createTempListingId();
        FileHelper::copyFiles($listing->listing_id, 'listing_images', "temp_" . $listing->temp_id, 'temporary_listing_image');

        PageHelper::setMetaTitle("Freestuff NZ - Edit my listing");
        PageHelper::setMetaDescription("Freestuff NZ - Edit my listing");

        TemplateHandler::setSelectedMainTab('my_account');
        PageHelper::setViews("views/list/list_form.php");

        # set default $user object
        include("templates/main_layout.php");
    }

    public function confirmStatus($listing_id) {
        $listing_id = (int)$listing_id;

        require_once("views/list/confirm_status_form.php");
        die();
    }

    public function remove($listing_id) {
        self::loginRequiredAndEchoJsonError();
        $listing_id = (int)$listing_id;

        $listing = Listing::instanceFromId($listing_id);

        if (self::_canIDoStuffToThisListing($listing, FALSE)) {
            $listing->markAsGone();
        } else {
            raiseError("You do not have permission to remove this listing.");
        }

        $output = array();
        $output['success'] = hasErrors() ? '0' : '1';
        $output['messages'] = getErrors();

        echo json_encode($output);
        die();
    }

    public function relist($listing_id) {
        $listing_id = (int)$listing_id;
        $listing = Listing::instanceFromId($listing_id);

        if (self::_canIDoStuffToThisListing($listing, FALSE)) {
            $listing->relist();

            MessageHelper::setSessionSuccessMessage("Your listing has been relisted.");
            $output['redirect'] = 'my_freestuff/current';

        } else {
            MessageHelper::setSessionErrorMessage("You do not have permission to relist this listing.");
            $output['redirect'] = 'my_freestuff/previous';
        }

        $output['success'] = hasErrors() ? '0' : '1';
        $output['messages'] = getErrors();

        echo json_encode($output);
        die();
    }

}
