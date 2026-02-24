<?php
/**
 * @var User $other_user
 * @var string $conversation_key ;
 * @var array $two_way_requests ;
 */
?>
<div class="container">
    <div id="message-page">
        <div class="d-sm-flex mb-3 align-items-center" id="html-other_person_info">
            <h5 class="mr-5 mb-0" style="line-height: 1">
                <b><?= ($other_user->firstname) ?></b> of <b> <?= clean(District::displayShort($other_user->district_id)) ?></b>
            </h5>
            <?
            if (ListingRequest::getAllUsersRequestedFromUserA(SESSION_USER_ID, array($other_user->user_id))) {
                $my_thumbs = Thumb::getThumbsGiven(array($other_user->user_id));

                $u_requester = new User();
                $u_requester->user_id = $other_user->user_id;
                $u_requester->user_listing_count = $other_user->user_listing_count;
                $u_requester->user_request_count = $other_user->user_request_count;
                $u_requester->thumbs_up = $other_user->thumbs_up;
                $u_requester->thumbs_down = $other_user->thumbs_down;
                $is_thumb_clickable = TRUE;

                require_once("views/message/_common_thumbs.php");
            }
            ?>
            <? if (!UserBlocked::isUserBlocked(SESSION_USER_ID, $other_user->user_id)) { ?>
                <button class="btn btn-danger ajax-modal"
                        data-href="<?= (APP_URL) ?>user_block/block_user_modal/<?= ($other_user->user_id) ?>">Block user
                </button>
                <?
            } else {
                ?>
                <button class="btn btn-danger unblock_user" data-other_user_id="<?= ($other_user->user_id) ?>">
                    Unblock user
                </button>
                <?
            }
            ?>
        </div>
        <div class="mb-3">
            <?
            ConversationThumbnailHandler::display('html-conversation_listing', $two_way_requests, 10, TRUE, FALSE, 50, 50)
            ?>
        </div>

        <div id="html-conversation" data-conversation_key="<?= ($conversation_key) ?>">
            <div class="html-conversation_messages">
                <div class="message-row mb-0 html-initial_message">Messages will appear here.</div>
            </div>

            <div class="send_message_box">
                <div class="form-group mb-0">
                    <textarea name="message" id="input-your_message" class="rounded-0 text_limit_box form-control"
                              placeholder="Type your message here" maxlength="250" rows="3"></textarea>
                    <button class="btn w-100 rounded-0 secondary send_request_message d-block"
                            data-conversation_key="<?= ($conversation_key) ?>"
                        <?= !UserBlocked::isUserBlocked(SESSION_USER_ID, $other_user->user_id) && !
                        UserBlocked::hasUserBlockedMe(SESSION_USER_ID, $other_user->user_id) ?: 'disabled' ?>>Send
                        Message
                    </button>
                    <div class="text-right">
                        <small class="text-muted text_limit_countdown">250 characters left</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>