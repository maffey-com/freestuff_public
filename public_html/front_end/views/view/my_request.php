<div class="container mt-4">
    <div class="row mb-3" id="contact-manager">
        <div class="col">
            <h1>You have requested this item</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?
            $filter = new FilterHelper();
            $filter->listing_id = $listing->listing_id;

            $sql_conversation = Message::getAllConversationsForUserId(SESSION_USER_ID, $filter);
            $my_conversations = runQueryGetAll($sql_conversation);

            #$my_thumbs = Thumb::getThumbsGiven(ArrayHelper::getColumn($my_conversations, "other_user_id"));
            require('views/message/_common_conversations.php');
            ?>
        </div>
    </div>
</div>
