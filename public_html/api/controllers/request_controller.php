<?php
    class RequestController extends _Controller {
        public function process($listing_id) {
            self::loginRequiredAndEchoJsonError();
            $listing_id = (int)$listing_id;
            $request_comment = paramFromPost('request_comment');

            $listing = Listing::instanceFromId($listing_id);

            if (!$listing->isWanted()) {
                if (!isset($_POST["confirm_collect"]) || !$_POST["confirm_collect"]) {
                    raiseError("You must confirm you are able to collect them item", "confirm_collect");
                }
            }

            if (empty($request_comment)) {
                raiseError("You must enter a comment for the lister", "message_to_lister");
            }

            if ($listing->haveIRequestedThisItem()) {
                raiseError("You have already requested this item", "message_to_lister");
            }

            $user = new User();
            $user->retrieveFromId(SESSION_USER_ID);
            if ($user->request_credit < 1 && !$listing->isWanted()) {
                raiseError("You have no request credits left. " . Thumb::refreshDueStatement($user), "request_comment");
            }

            $listing_request = new ListingRequest();
            $listing_request->listing_id = $listing_id;
            $listing_request->buildFromPost();
            $listing_request->buildCurrentUserDetails();

            if ($listing->request_count > REQUESTS_PER_ITEM_HARD_LIMIT) {
                raiseError("This item has been requested more than " . REQUESTS_PER_ITEM_HARD_LIMIT . " times.  We are not accepting any more requests","confirm_collect");
            }

            if (!hasErrors()) {
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
                        Thumb::spendCredit($listing_request->user_id);
                    }
                    $request_credit = Thumb::myRequestCredit($listing_request->user_id);
                    self::echoJsonSuccessAndExit(array("request_id" => $listing_request->request_id,"request_credit"=>$request_credit));

                }
            }
            if (hasErrors()) {
                self::echoJsonErrorsAndExit();
            }

            exit();
        }
    }
