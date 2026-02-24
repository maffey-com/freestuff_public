<?php
/**
 * @var array $my_conversations
 */
?>
<div id="view-messages" class="container-fluid">
    <?
    $other_user_ids = ArrayHelper::getColumn($my_conversations, 'other_user_id');
    $requests = ListingRequest::recentRequestsBetweenUsers(SESSION_USER_ID, $other_user_ids);
    $requester_users = ListingRequest::getAllUsersRequestedFromUserA(SESSION_USER_ID, $other_user_ids);

    $my_thumbs = Thumb::getThumbsGiven($other_user_ids);

    #$my_thumb = paramFromHash($other_user->user_id, $my_thumbs, 'x');

    foreach ($my_conversations as $_conversation) {
        $_other_user_id = $_conversation['other_user_id'];
        // skip blocked users conversations
        if (UserBlocked::isUserMessagesBlocked(SESSION_USER_ID, $_other_user_id) ||
            UserBlocked::hasUserBlockedMe(SESSION_USER_ID, $_other_user_id)) {
            continue;
        }

        $_other_district_name = District::displayShort($_conversation['other_district_id']);

        $_listing_requests = paramFromHash($_other_user_id, $requests, array());

        $_conversation_url = 'message/conversation/' . $_other_user_id;
        ?>
        <div data-href="<?= ($_conversation_url) ?>" class="row row-conversation rounded rounded-lg"
             id="row-conversation-<?= ($_conversation['conversation_key']) ?>">
            <div class="col-3 col-md-3 col-lg-2 mb-2">
                <?
                ConversationThumbnailHandler::display('html-inbox_conversations', $_listing_requests, 3, FALSE, TRUE);
                ?>
            </div>
            <div class="col-9 col-md-9 col-lg-10">
                <div class="d-flex h-100">
                    <div class="mr-4 flex-grow-1">
                        <div class="d-md-flex mb-2 mb-md-3 align-items-center">
                            <div class="mr-5 mb-0">
                                <b><?= h(ucfirst($_conversation['other_firstname'])) ?></b>
                                <?
                                if (!empty($_other_district_name)) {
                                    echo ' of <b>' . clean($_other_district_name) . '</b>';
                                }
                                ?>
                            </div>
                            <?
                            if (array_key_exists($_other_user_id, $requester_users)) {
                                $u_requester = new User();
                                $u_requester->user_id = $_other_user_id;
                                $u_requester->user_listing_count = $requester_users[$_other_user_id]['user_listing_count'];
                                $u_requester->user_request_count = $requester_users[$_other_user_id]['user_request_count'];
                                $u_requester->thumbs_up = $requester_users[$_other_user_id]['thumbs_up'];
                                $u_requester->thumbs_down = $requester_users[$_other_user_id]['thumbs_down'];
                                $is_thumb_clickable = FALSE;

                                require("views/message/_common_thumbs.php");
                            }
                            ?>
                        </div>

                        <div class="mb-2">
                            <?
                            if ($_conversation['is_sender'] == 'y') {
                                echo '<b>You: </b>';
                            }
                            h(StringHelper::ellipsis($_conversation['message'], 150));
                            ?>
                        </div>
                        <small class="text-primary "><?= DateHelper::ago($_conversation['date_created']) ?></small>
                    </div>
                    <div class="ml-auto d-flex align-items-center">
                        <span class="unread rounded-circle d-none"></span>
                    </div>
                </div>
            </div>
        </div>
        <?
    }
    ?>
</div>

<style>
    .row-conversation {
        padding-top: .5rem !important;
        padding-bottom: .5rem !important;
        margin-bottom: 1rem !important;
        border: 1px solid #dee2e6 !important;
    }

    .row-conversation:hover {
        background-color: #dee2e6;
        cursor: pointer;
    }
</style>
