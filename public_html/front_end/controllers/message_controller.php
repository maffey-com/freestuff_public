<?php
class MessageController extends _Controller {
    public function countUnread() {
        if (!SecurityHelper::isLoggedIn()) {
            return FALSE;
        }

        $my_unread_messages = Message::getAllUnreadConversationKeys(SESSION_USER_ID);

        $output = array(
            'success' => !hasErrors(),
            'count_unread' => count($my_unread_messages),
            'conversation_keys' => $my_unread_messages
        );

        echo json_encode($output);
        die();
    }

    public function inbox() {
        $filter = new FilterHelper('');

        if (!SecurityHelper::isLoggedIn()) {
            MessageHelper::setSessionErrorMessage("You need to login or register to use this site.");

            redirect(APP_URL . 'login');
        }

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('messages');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('My Inbox');

        $sql_my_conversation = Message::getAllConversationsForUserId(SESSION_USER_ID, $filter);
        $dw_my_conversation = new DataWindowHelper("browse", $sql_my_conversation,"message_id", "desc", 10);
        $dw_my_conversation->run();

        $paging = $dw_my_conversation->getPaging();
        $my_conversations = $dw_my_conversation->data;

        PageHelper::setMinifyPageCssName('request_message');
        PageHelper::setViews("views/message/list_conversations.php");

        include("templates/main_layout.php");
    }

    public function send($conversation_key) {
        self::_validateConversationKey($conversation_key);

        $message = paramFromPost("message");

        if (empty($message)) {
            raiseError('Message cannot be blank');
        }

        if (!hasErrors()) {
            Message::send($conversation_key);
        }

        $output = array(
            'success' => !hasErrors(),
            'messages' => getErrors()
        );

        echo json_encode($output);
        die();
    }

    /***
     * Messages for multiple requests
     * Ajax
     */
    public function loadMessages($conversation_key) {
        self::loginRequiredAndEchoJsonError();

        self::_validateConversationKey($conversation_key);

        $unread_only = paramFromGet('unread_only', 'false');
        $last_fetched_message_id = (int)paramFromGet('last_fetched_message_id');
        $unread_only = ($unread_only == 'false') ? FALSE : TRUE;

        $conversation_messages = Message::getAllFromConversationKey($conversation_key, SESSION_USER_ID, $unread_only, $last_fetched_message_id);
        $changed = FALSE;

        if (count($conversation_messages)) {
            $changed = TRUE;
            Message::updateReceiverViewedDateForConversationKey($conversation_key, SESSION_USER_ID);
        }

        $output = array(
            'changed' => $changed,
            'messages' => $conversation_messages,
        );

        echo json_encode($output);
        exit();
    }

    private static function _validateConversationKey($conversation_key)
    {
        if ((!SecurityHelper::isLoggedIn()) || (!Message::checkAccessToConversationKey($conversation_key, SESSION_USER_ID))) {
            raiseError('You do not have permission to view this page');

            if (FormHelper::isAjaxRequest()) {
                $output = array(
                    'success' => false,
                    'messages' => getErrors()
                );
                echo json_encode($output);

            } else {
                PageHelper::setViews("views/page/401.php");
                include("templates/main_layout.php");
            }
            die();
        }

        return TRUE;
    }

    /**
     * VIew conversation (message) with another user
     * @param $other_user_id
     * @return void
     */
    public function conversation($other_user_id) {
        if (UserBlocked::isUserMessagesBlocked(SESSION_USER_ID, $other_user_id)) { // skip blocked users
            MessageHelper::setSessionErrorMessage("Message not found.");
            redirect(APP_URL . 'message/inbox');
        }
        $other_user_id = (int)$other_user_id;
        $conversation_key = Message::buildConversationKey(SESSION_USER_ID, $other_user_id);

        self::_validateConversationKey($conversation_key);

        TemplateHandler::setSelectedMainTab('my_account');
        TemplateHandler::setSelectedDashboardMenu('messages');

        BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('My Inbox', APP_URL . 'message/inbox');

        BreadcrumbHelper::addBreadcrumbs('Conversation');

        PageHelper::setMinifyPageCssName('inbox_conversation');

        $other_user = User::instanceFromId($other_user_id);
        $two_way_requests = Message::getAllTwoWayRequestsWithUserB(SESSION_USER_ID, $other_user_id);

        PageHelper::setViews("views/message/view_conversation.php");

        include("templates/main_layout.php");

    }
}
