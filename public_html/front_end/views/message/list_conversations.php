<?php
/**
 * @var array $my_conversations
 * @var FilterHelper $filter
 */
?>
<div class="container">
    <div class="row">
        <div class="col">
            <?
            TemplateHandler::echoPageTitle('Messages');
            ?>
        </div>
        <div class="col-12 col-md mb-2 mb-md-0 justify-content-end">
            <div class="d-flex">
            <input placeholder="Search Messages" id="input-search_conversation" class="form-control flex-grow-1" value="<?(h($filter->q))?>" />
            <input type="button" class="btn" value="Search" id="btn-search_conversation" />
            </div>
        </div>
    </div>

    <div id="message-page">
        <?
        if (count($my_conversations) == 0) {
            ?>
            <div class="p-3 bg-light fs-block">
                <p>You have no conversation with other users</p>
            </div>

            <p>
                <a href="<?= APP_URL ?>my_freestuff">Click here</a> to see you current listings
            </p>
            <?

        } else {
            require('views/message/_common_conversations.php');
        }
        ?>
    </div>
    <?
    require('templates/common_pager_bottom.php');
    ?>
</div>