<?php
$requests = ListingRequest::getRequestsForListing($listing->listing_id);
?>
<div class="container mt-4">
    <div class="row mb-3" id="contact-manager">
        <div class="col">
            <?
            if (!$listing->request_count) {?>
                <p>There are no <?= (($listing->isWanted()) ? 'offers' : 'requests') ?> for this item yet</p>
                <?
            } else {
                $action_type = $listing->isWanted() ? 'offer' : 'request'; ?>
                <h1>You have <?= (StringHelper::singularOfPlural($listing->request_count, $action_type, $action_type . "s")) ?> for this item</h1>

                <?
                if ($listing->hasReachedMaxRequestLimit()) {
                    ?>
                    <div class="text-danger my-3">You will not receive any further requests for this item as the maximum number of requests has been reached.</div>
                    <?
                }
            }?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?
            $filter = new FilterHelper();
            $filter->listing_id = $listing->listing_id;

            $sql_conversation = Message::getAllConversationsForUserId(SESSION_USER_ID, $filter);
            $my_conversations = runQueryGetAll($sql_conversation);

            $my_thumbs = Thumb::getThumbsGiven(ArrayHelper::getColumn($my_conversations, "other_user_id"));
            require('views/message/_common_conversations.php');
            ?>
        </div>
    </div>
</div>
