<div class="container">
    <?
    TemplateHandler::echoPageTitle('Your request was sent to the lister');
    ?>
    <div class="row fs-block">
        <div class="col">
            <h4>What happens next?</h4>
            <p>Keep an eye on the listing here: <a href="<?=(seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title))?>"><?=($listing->title)?></a></p>
            <p>If the lister accepts your request you'll receive an email and if you login to the site you’ll be able to view any messages you have from the lister</p>
            <p>You can communicate directly with the person giving away the item using this free messaging feature.</p>
        </div>
    </div>


</div>
