<div id="footer-site_stats_container">
    <h6 class="text-md-left">Freestuff Site Stats</h6>

    <ul class="list-group mb-3">
        <li class="list-group-item">
            <div class="d-flex">
                Registered Users:
                <span class="ml-auto"><?= (number_format($no_of_users, 0)) ?></span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                Free Listings:
                <span class="ml-auto"><?= ($free_listings) ?></span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                Wanted Listings:
                <span class="ml-auto"><?= ($wanted_listings) ?></span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                New Listings This Week:
                <span class="ml-auto"><?= ($this_week_listings) ?></span>
            </div>
        </li>
    </ul>
    <img class="d-none d-md-inline-flex" src="img/megaphone.png" alt="Freestuff maskot talking into a megaphone" />
</div>