<?php

class RequestController extends _Controller {

    protected function _redirectIfInvalidListing($listing) {
        if (empty($listing->listing_id)) {
            MessageHelper::setSessionErrorMessage('Invalid listing record.');
            redirect(APP_URL);
        }
    }


    public function request($listing_id) {
        self::loginRequiredAndRedirect();

        $listing_id = (int)$listing_id;

        $listing = Listing::instanceFromId($listing_id);

        $this->_redirectIfInvalidListing($listing);

        BreadcrumbHelper::addBreadcrumbs('Listings in ' . District::resolveRegionName($listing->district_id), APP_URL.'browse/by-region/'.District::resolveRegionName($listing->district_id));
        BreadcrumbHelper::addBreadcrumbs($listing->title, seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title));

        // Check if the user has requested less than the max number of requests for the day.
        $user = new User();
        $user->retrieveFromId(SESSION_USER_ID);

        if ($user->request_credit < 1 && !$listing->isWanted()) {

        	BreadcrumbHelper::addBreadcrumbs('Request item');
        	PageHelper::setViews("views/request/request_limit.php");

        } else {
            if ($listing->isWanted()) {
                BreadcrumbHelper::addBreadcrumbs('Offer item');
                PageHelper::setViews("views/request/offer_banner.php", "views/request/offer_form.php");

            } else {
            	if ($listing->request_count >= REQUESTS_PER_ITEM_HARD_LIMIT){
            		BreadcrumbHelper::addBreadcrumbs('Request item');
					PageHelper::setViews("views/request/requests_max_per_item.php");
				} else {
					BreadcrumbHelper::addBreadcrumbs('Request item');
                	PageHelper::setViews("views/request/request_banner.php", "views/request/request_form.php");
				}
            }
        }

        PageHelper::setMetaTitle($listing->title);
        PageHelper::setMetaDescription($listing->title);
        PageHelper::setMinifyPageCssName('request');

        TemplateHandler::setBrowseCategoryName(District::resolveRegionName($listing->district_id));
        TemplateHandler::setSuppressIslandAds();

        PageHelper::addJsVar('listing_id',$listing_id);

        include("templates/main_layout.php");
    }

    public function processRequest($listing_id) {
        self::loginRequiredAndEchoJsonError();

        $listing_id = (int)$listing_id;
        $request_comment = paramFromPost('request_comment');

        $listing = Listing::instanceFromId($listing_id);

        if (!$listing->isWanted()) {
            if (!isset($_POST["confirm_collect"])) {
                raiseError("You must confirm you are able to collect them item", "request_comment");
            }
        }
        if (!$listing->isWanted()) {
            if (!isset($_POST["confirm_credit"])) {
                raiseError("Please confirm you understand requesting this item will consume 1 request credit.", "confirm_credit");
            }
        }



        if (empty($request_comment)) {
            raiseError("You must enter a comment for the lister", "request_comment");
        }

        if ($listing->haveIRequestedThisItem()) {
            raiseError("You have already requested this item", "request_comment");
        }
        $user = new User();
        $user->retrieveFromId(SESSION_USER_ID);
        if ($user->request_credit < 1 && !$listing->isWanted()) {
            raiseError("You have no request credits left", "request_comment");
        }

        $listing_request = new ListingRequest();
        $listing_request->listing_id = $listing_id;
        $listing_request->buildFromPost();
        $listing_request->buildCurrentUserDetails();

        if ($listing->request_count > REQUESTS_PER_ITEM_HARD_LIMIT) {
            raiseError("This item has been requested more than " . REQUESTS_PER_ITEM_HARD_LIMIT . " times.  We are not accepting any more requests","confirm_collect");
        }

        if (hasErrors()) {
            echo json_encode(getErrors());

        } else {
            if ($listing_request->insert()) {
                $listing->updateRequestCount();

                $message = new Message();
                $message->message =  clean($request_comment);
                $message->receiver_user_id = $listing->user_id;
                $message->sender_user_id = SESSION_USER_ID;
                $message->conversation_key = Message::buildConversationKey(SESSION_USER_ID, $listing->user_id);
                $message->request_id = $listing_request->request_id;
                if ($message->insert()) {
                    $message->notify();
                    User::updateRequestsAndListingsCount($listing_request->user_id);
                }
                Thumb::spendCredit(SESSION_USER_ID);
                echo '1';

            } else {
                echo json_encode(getErrors());
            }
        }
        exit();
    }

	public function submitted($listing_id) {
        self::loginRequiredAndRedirect();

        $listing_id = (int)$listing_id;

        $listing = Listing::instanceFromId($listing_id);

        $this->_redirectIfInvalidListing($listing);

        BreadcrumbHelper::addBreadcrumbs('Listings in ' . District::resolveRegionName($listing->district_id), APP_URL.'browse/by-region/'.District::resolveRegionName($listing->district_id));
        BreadcrumbHelper::addBreadcrumbs($listing->title, seoFriendlyURLs($listing->listing_id, 'listing', FALSE, $listing->title));

        if ($listing->isWanted()) {
            PageHelper::setViews("views/request/offer_thankyou.php");
            BreadcrumbHelper::addBreadcrumbs('Offer Item');

        } else {
            PageHelper::setViews("views/request/request_thankyou.php");

            BreadcrumbHelper::addBreadcrumbs('Request item');
        }

        TemplateHandler::setSuppressIslandAds();

        include("templates/main_layout.php");
    }

    public function saveThumb($requester_user_id, $up_down) {
        if (!in_array($up_down,array('u','d','x'))) {
            return false;
        }

        if (!SecurityHelper::isLoggedIn()) {
            exit();
        }

        $requester_user_id = (int)$requester_user_id;
        if (!ListingRequest::getAllUsersRequestedFromUserA(SESSION_USER_ID, array($requester_user_id))) {
            exit();
        }
        Thumb::updateThumb(SESSION_USER_ID, $requester_user_id, $up_down);
        exit();
    }


}
