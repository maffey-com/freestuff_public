<div class="container">
    <?
    TemplateHandler::echoPageTitle('You have used up all your request credits for the month');
    ?>
    <div class="row fs-block">
        <div class="col my-3">
			<p class="answer">Everyone loves freestuff.  To make sure there is enough freestuff to go around, you can only request <?= MAX_REQUESTS_PER_MONTH ?> items per month.</p>
            <p class="answer"><?=Thumb::refreshDueStatement($user)?></p>
        </div>
    </div>
</div>
