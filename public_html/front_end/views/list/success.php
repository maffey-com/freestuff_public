<div class="container">
    <?
    TemplateHandler::echoPageTitle('Listing Created!');
    ?>
    <div class="row">
        <div class="col">
            <h4>
                You've made a listing! What happens now?
            </h4>

            <p>People <?=($listing->isWanted() ? "offering" : "requesting")?> your item contact you within this site.</p>
            <p>Use the <a href="<?=(APP_URL)?>view/?listing_id=<?=($listing->listing_id)?>">view link</a> to view your listing and to communicate with people who are interested in it.</p>

            <div class="form-group">
                <a class="btn btn-mobile" href="<?=(APP_URL)?>view/?listing_id=<?=($listing->listing_id)?>">View Your Listing</a>
                <a class='btn primary btn-mobile' href="<?=(APP_URL)?>my_freestuff">Manage Your Listings</a>
                <a class='btn secondary btn-mobile' href="<?=(APP_URL)?>list" id="listing_success_list_another">List Another Item</a>
            </div>
        </div>
    </div>
</div>