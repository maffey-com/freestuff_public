<div class="container">
    <?
    TemplateHandler::echoPageTitle('This email verification has expired');
    ?>
    <div class="row" id="email-verify-expired-page">
        <div class="col">
            <p>Click <a href="<?=APP_URL?>/account/edit_email">here</a> to try again.</p>
        </div>
    </div>
</div>