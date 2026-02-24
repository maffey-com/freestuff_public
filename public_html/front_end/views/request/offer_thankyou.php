<div class="container">
    <?
    TemplateHandler::echoPageTitle('Your offer was sent to the lister');
    ?>
    <div class="row fs-block">
        <div class="col">
            <h4>What happens next?</h4>
            <p><a href="<?=(seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title))?>">You can see responses from the lister here</a>.</p>
        </div>
    </div>


</div>

