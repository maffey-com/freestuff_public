<?php

class MessagesController extends _Controller {
    public function __construct() {
    }

    public function inbox() {
//        $filter_age =_pageSize (int)paramFromGet("age", 7);
//        $filter_age = in_array($filter_age, array(1, 7, 30, 90, 730)) ? $filter_age : $filter_age;

//        	if ($filter_age == 1) {
//				$messages = ListingRequestMessage::myFilteredMessages(730, true, true);
//			} else {
//				$messages = ListingRequestMessage::myFilteredMessages($filter_age, false, true);
//			}

        $filter = new FilterHelper();
        $filter->q = paramFromGet('q');
        $filter->listing_id = paramFromGet('listing_id');

        $sql_my_conversation = Message::getAllConversationsForUserId(SESSION_USER_ID, $filter, false);
        $dw_my_conversation = new DataWindowHelper("browse", $sql_my_conversation, "message_id", "desc", paramFromRequest('page_size', 10));
        if (paramFromGet("page")) {
            $dw_my_conversation->current_page = paramFromGet("page");
        }
        $dw_my_conversation->run();

        $paging = $dw_my_conversation->getPaging();
        $my_conversations = $dw_my_conversation->data;

        $unread_conversation_keys = [];
        if (!empty($my_conversations)) {
            $unread_conversation_keys = Message::getUnreadFromConversationKeys(ArrayHelper::getColumn($my_conversations,'conversation_key'), SESSION_USER_ID);
        }

        $other_user_ids = ArrayHelper::getColumn($my_conversations,'other_user_id');
        $requester_users = ListingRequest::getAllUsersRequestedFromUserA(SESSION_USER_ID, $other_user_ids);
        $my_thumbs = Thumb::getThumbsGiven($other_user_ids);
        $listings = ListingRequest::recentRequestsBetweenUsers(SESSION_USER_ID, $other_user_ids);
        foreach ($my_conversations as &$conversation) {
            $conversation['unread'] = in_array($conversation['conversation_key'], $unread_conversation_keys);

//            $value['district'] = District::resolveRegionName($value['other_district_id']);
            $conversation['district'] = District::displayShort($conversation['other_district_id'])??'';
            $conversation['message'] = str_replace("<br/>", "\n", $conversation['message']);
            $conversation['message'] = strip_tags($conversation['message']);
            $conversation['message'] = html_entity_decode($conversation['message']);

            $_other_user_id = $conversation['other_user_id'];
            if (array_key_exists($_other_user_id, $requester_users)) {
                $conversation['thumbs_up'] = $requester_users[$_other_user_id]['thumbs_up'];
                $conversation['thumbs_down'] = $requester_users[$_other_user_id]['thumbs_down'];
                $conversation['other_user_listing_count'] = $requester_users[$_other_user_id]['user_listing_count'];
                $conversation['other_user_request_count'] = $requester_users[$_other_user_id]['user_request_count'];
                if (array_key_exists($_other_user_id, $my_thumbs)) {
                    $conversation['my_thumb'] = $my_thumbs[$_other_user_id];
                }
            }

            $conversation['listings'] = paramFromHash($conversation['other_user_id'], $listings, array());
        }

        echo json_encode($my_conversations);
        exit();
    }

    public function conversation($other_user_id) {
        $other_user_id = (int)$other_user_id;
        $conversation_key = Message::buildConversationKey(SESSION_USER_ID, $other_user_id);

        if (Message::checkAccessToConversationKey($conversation_key, SESSION_USER_ID)) {

            Message::updateReceiverViewedDateForConversationKey($conversation_key, SESSION_USER_ID);

            $conversation_messages = array_reverse(Message::getAllFromConversationKey($conversation_key, SESSION_USER_ID));
            foreach ($conversation_messages as &$message) {
                $message['message'] = str_replace("<br/>", "\n", $message['message']);
                $message['message'] = strip_tags($message['message']);
                $message['message'] = html_entity_decode($message['message']);
            }
            // most recent message is at the top to play nicely with flutter ListView reverse: true


            $user_ids = ListingRequest::recentRequestsBetweenUsers($_SESSION['session_user_id'], [$other_user_id], 99);
            $listings = [];
            foreach ($user_ids as $user_id) {
                foreach ($user_id as $listing) {
                    $listings[] = $listing;
                }
            }
            echo json_encode(array('success' => true, 'messages' => json_encode($conversation_messages), 'listings' => json_encode($listings)));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Not involved',));
        }
    }

    public function send() {
        $other_user_id = paramFromPost("other_user_id");
        if (empty($other_user_id)) {
            raiseError('other_user_id cannot be blank');
        }
        $conversation_key = Message::buildConversationKey(SESSION_USER_ID, $other_user_id);

        $message = paramFromPost("message");

        if (empty($message)) {
            raiseError('Message cannot be blank');
        }

        if (!hasErrors()) {
            Message::send($conversation_key);
        }

        $output = array('success' => !hasErrors(), 'messages' => getErrors());

        echo json_encode($output);
        die();
    }

    public function getUnread() {

        self::loginRequiredAndEchoJsonError();

        $my_unread_messages = Message::getAllUnreadConversationKeys(SESSION_USER_ID);

        $output = array('success' => !hasErrors(), 'count_unread' => count($my_unread_messages), 'conversation_keys' => $my_unread_messages);
        echo json_encode($output);
        die();
    }
}
