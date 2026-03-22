<div class="container">
    <?
    TemplateHandler::echoPageTitle('Frequently Asked Questions');
    ?>

    <div id="faq-page">
        <div class="row">
            <div class="col">
                <div class="h4">Is stuff on this site really free?</div>
                <p class="answer">Absolutely. All items listed on this site must be completely free for pickup, with no strings attached.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">Who can list free stuff on this site</div>
                <p class="answer">Anyone, individuals, companies and organisations. All listings must be completely free for pickup, with no strings attached</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">Do I have to give out my phone number?</div>
                <p class="answer">We do not give out your phone number. We provide a private chat feature so users can communicate about listings without the need to disclose
                    phone or email details.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">How long can items be listed for?</div>
                <p class="answer">Items are listed for 2 weeks or until the lister removes the item.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">Is there a limit to how many free things I can get?</div>
                <p class="answer">Everyone loves freestuff, so to keep things fair for all, we limit users to <?=MAX_REQUESTS_PER_MONTH?> requests per month.  <a href="/page/request_credit">Read about request credits here</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">How does feedback work?</div>
                <p class="answer">People giving items away can give feedback on people requesting items.  <a href="/page/feedback">Read more here</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="h4">How do I delete my account?</div>
                <p class="answer">After you log in, go to this page: <a href="https://freestuff.co.nz/account/delete">https://freestuff.co.nz/account/delete</a></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col mb-4">
            <div class="h4">What is a reliability score?</div>
            <p class="answer">Your reliability score is out of 10 and is based on your last 10 requests. Each time a giver marks you as a no-show, your score goes down by 1. A score of 8 or above is green, 5–7 is yellow, and below 5 is red. Turning up when you say you will keeps your score high.</p>
        </div>
    </div>
    <div class="row">
        <div class="col mb-4">
            <div class="h4">What are giver badges?</div>
            <p class="answer">Giver badges recognise users who have given away items on Freestuff. Give away 3 or more items to earn a Bronze Giver badge, 10 or more for Silver, and 25 or more for Gold.</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="h4">What does no-show mean?</div>
            <p class="answer">A no-show is when a requester arranges to pick up an item but doesn't turn up. Givers can mark requesters as no-shows, which affects their reliability score. If you are marked as a no-show by mistake, contact the giver to sort it out.</p>
        </div>
    </div>
</div>
