<?php
BreadcrumbHelper::addBreadcrumbs('What Is Freestuff');

require('templates/common_breadcrumbs.php');
?>

<div class="container" id="how-it-works-page">
    <?
    TemplateHandler::echoPageTitle('How Freestuff Works!');
    ?>
    <div class="row section">
        <div class="col-12 col-md-6 section-text">
            <h4>Give Away Unwanted Items</h4>
            <p>Freestuff is SUPER easy and free to list items you no longer want or use.</p>
            <p>Your first step is to <a href="register">create an account</a>. Then listing an item is a fast process. Up to 9 people can request your item, you select who gets the item and let them know the pick up details.</p>
            <br />
            <a href="list" class="btn primary">Give Something Away Today!</a>
        </div>
        <div class="col-12 col-md-6 section-image">
            <img src="img/give.png" class="img-fluid" />
        </div>
    </div>
    <div class="row section">
        <div class="col-12 col-md-6 section-image">
            <img src="img/request.png" class="img-fluid" />
        </div>
        <div class="col-12 col-md-6 section-text">
            <h4>Requesting Items You Like</h4>
            <p>You must be a <a href="login">Freestuff member</a> to request an item. If you see something you would like then click the <strong>request this item</strong> button.</p>
            <p>Then you wait and see if your request was accepted. You can request a maximum of <?=MAX_REQUESTS_PER_MONTH?> items a month.</p>
            <p>Still have a question? Please visit our <a href="page/faq">FAQs</a></p>
            <br />
            <a href="page/faq" class="btn secondary">Freestuff FAQs</a>
        </div>
    </div>
    <div class="row section">
        <div class="col-12 col-md-6 section-text">
            <h4>Keep Freestuff AWESOME!</h4>
            <p>There's only a few basic rules. Items you give away must be a physical item. So that means NO services, NO events, NO off-site links and NO ebooks.</p>
            <p>Please try to list reasonable quality items.</p>
            <p>Freestuff has the right to remove listings.</p>
            <p>We encourage you to use our member feedback system and keep our Freestuff community AWESOME.</p>
            <p><strong>Thanks for sharing, caring and being great Freestuff people!</strong></p>
        </div>
        <div class="col-12 col-md-6 section-image">
            <img src="img/awesome.png" class="img-fluid" />
        </div>
    </div>
</div>