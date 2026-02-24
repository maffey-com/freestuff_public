<style>
    .listing_block {
        margin-bottom: 10px;
    }

    .listing_block .title {
        color: green;
        display: inline-block;
        font-weight: bold;
    }

    .message > span,
    .details > span {
        font-weight: bold;
    }

    .message > span:first-of-type {
        margin-left: -30px;
    }

    .messages {
        margin-left: 50px;
    }


    .message {
        width: 350px;
        margin-bottom: 10px;
        padding: 5px;
        border-radius: 10px;
    }

    .requester {
        background-color: #ffab00;
    }

    .lister {
        background-color: #97cb35;
        margin-left: 100px;
    }
</style>
<form method="post" action="requests.php">
    <label>Listing in the last N hours</label> <input name="hours" value="<?= ($hours) ?>"/><br/>
    <label>Listing id</label> <input name="listing_id" value="<?= ($request_listing_id) ?>"/><br/>
    <input type="submit" value="Go"/>
</form>
<h2>Listings: <?= (count($listings)) ?></h2>
<? foreach ($listings as $idx => $listing) {
    $listing_id = $listing['listing_id'];
    ?>
    <div class="listing_block">
        <h3>
            <a href="listing.php?search_listing=<?=$listing_id?>"><?= $listing['title'] ?> (<?=$listing_id?>)</a> - <a href="user.php?search_name=<?=$listing['email']?>"><?=$listing['email']?> (<?= ($listing['user_id']) ?>)</a>
           </h3>
        <? if (isset($requests[$listing_id])) {
            foreach ($requests[$listing_id]['nested_items'] as $request_id => $request) {
                ?>
                <h4><?= DateHelper::display($request['request_timestamp'], true, true) ?>
                    - <a href="user.php?search_name=<?=$request['email']?>"><?=$request['email']?></a></h4>
                <div class="messages">
                    <? foreach ($messages[$request_id]['nested_items'] as $r_id => $message) {
                        if ($message['sender_user_id'] == $request["user_id"]) {
                            $message_class = "requester";
                        } else {
                            $message_class = "lister";
                        } ?>
                        <div class="message <?= $message_class ?>">
                            <?= ($message['message']) ?>
                        </div>
                    <? } ?>
                </div>

            <? }
        } ?>
    </div>
    <?
}
?>

