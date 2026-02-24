<div class="container">
    <?
    TemplateHandler::echoPageTitle('Request Credit');
    ?>

    <div id="faq-page">
        <div class="row">
            <div class="col">
                <div class="h4">EVERYBODY loves Freestuff.</div>
                <p class="answer">To make sure there is enough Freestuff to go around, we have limited users to <?= MAX_REQUESTS_PER_MONTH ?> requests per month. </p>
                <p class="answer"> Each time you request an item, you will spend 1 credit. Each month, your credits are
                    restored back to <?= MAX_REQUESTS_PER_MONTH ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">Bonus Credits</div>
                <p class="answer">When you receive positive feedback (thumbs up) from the person you requested the item
                    of, you will be given an extra 2 request credits.</p>
                <p class="answer">Negative <a href="/page/feedback">feedback</a> will cost you 2 request credits.</p>
            </div>
        </div>
        <? if (isset($user)) { ?>
            <div class="row">
                <div class="col">
                    <div class="h4">My request Credits</div>
                    <p class="answer">You currently
                        have <?= StringHelper::singularOfPlural($user->request_credit, "credit", "credits") ?>
                        available. </p>
                    <p class="answer"><?= Thumb::refreshDueStatement($user)?></p>
                </div>
            </div>
        <? } ?>
    </div>
</div>
