<?php
$regions = Listing::getRegionsWithCount();
$browsing_category = TemplateHandler::getBrowseCategoryName();
?>
<header id="header" class="container d-none d-lg-block">
    <div class="row no-gutters">

        <div class="col-4 d-md-flex" id="header-container-logo">
            <a href="/"><img src="img/logo.png" alt="Freestuff mascot next to the website name and slogan"/></a>
        </div>

        <div class="col-md-8">
            <div class="container-fluid" id="actions-container">
                <div class="row d-md-flex mb-2">

                    <? if (SecurityHelper::isLoggedIn()) { ?>
                        <div class="col-md-12 text-md-right" id="welcome-message">
                            <a href="my_freestuff"><i class="fa fa-user"></i> <?= (paramFromSession("session_firstname")) ?></a> |
                            <a href="message/inbox">Inbox <span class="html-count_inbox_unread"></span></a> |
                            <a id="btn-logout" href="account/logout">Logout</a>
                        </div>
                    <? } else { ?>
                        <div class="col-md-12 text-md-right">
                            <a class="btn none" href="page/how_it_works" id="about">About</a>
                            <a class="btn none" href="register" id="register">Register</a>
                            <a class="btn secondary" href="login" id="login">Login</a>
                        </div>
                    <? } ?>
                </div>

                <div class="row no-gutters" id="header-container-buttons">
                    <div class="col-md-7 col-lg-8">
                        <div class="dropdown d-inline-block mr-2 mb-2 mb-md-0 header-browse">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" style="color: #333 !important">Browse Listings
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <?
                                foreach (District::$regions as $region_name) {
                                    $count = (int)paramFromHash($region_name, $regions);

                                    $row_style = ($browsing_category == $region_name) ? ' active-region' : '';
                                    ?>
                                    <li class="list-group-item">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center <?= ($row_style) ?>"
                                           href="browse/by-region/<?= ($region_name) ?>"><span
                                                    class=""><?= ($region_name) ?></span>&nbsp;<span
                                                    class="badge badge-pill badge-fs"><?= ($count) ?></span></a>
                                    </li>
                                    <?
                                } ?>
                            </ul>
                        </div>
                        <a id="link-give_something_away" class="btn btn-primary d-inline-block" href="list">List
                            Something</a>
                    </div>

                    <div class="col-md-5 col-lg-4" id="search-form">
                        <form action='search/search' method='get'>
                            <div class="input-group input-group mt-1">

                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg"
                                       placeholder="Search for freestuff"
                                       name="q"
                                       value="<?= (htmlentities(TemplateHandler::getSearchText())) ?>"
                                >
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text" id="inputGroup-sizing-lg"><i class="fa fa-search"></i></button>
                                </div>
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="header-mobile" class="d-block d-lg-none container-fluid mb-2">
    <div class="row" id="header-mobile_top">
        <a class="icon_link mr-auto " href="/"><img src="img/logo.png"
                                                    alt="Freestuff maskot next to the website name and slogan"/></a>

        <div class="d-flex align-self-center header-row_icons">
            <span class="mobile_link" title="Search" data-toggle="collapse" href="#html-mobile_search" role="button"
                  aria-expanded="false" aria-controls="html-mobile_search"><i
                        class="fa fa-search"></i><span>Search</span></span>
            <?
            if (SecurityHelper::isLoggedIn()) {
                ?>
                <a class="mobile_link" href="my_freestuff" title="My Freestuff"><i class="fa fa-user"></i><span>My Freestuff</span></a>
                <a class="mobile_link" href="message/inbox" title="Inbox"><i
                            class="fa fa-envelope"></i><span>Inbox <span
                                class="d-inline html-count_inbox_unread"></span></span></a>
                <a class="mobile_link" href="account/logout" title="Log out"><i
                            class="fa fa-sign-out"></i><span>Logout</span></a>
                <?
            } else {
                ?>
                <a class="mobile_link" href="login" title="Login"><i class="fa fa-user"></i><span>Login</span></a>
                <?
            }
            ?>
        </div>
    </div>

    <div class="row" id="header-mobile_bottom">
        <div class="col-12 d-flex">
            <div class="flex-fill header-browse">
                <div class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                     id="browseListingsMobile">Browse Listings
                </div>
                <ul class="dropdown-menu" aria-labelledby="browseListingsMobile">
                    <?
                    foreach (District::$regions as $region_name) {
                        $count = (int)paramFromHash($region_name, $regions);

                        $row_style = ($browsing_category == $region_name) ? ' active-region' : '';
                        ?>
                        <li class="list-group-item">
                            <a class="dropdown-item d-flex justify-content-between align-items-center <?= ($row_style) ?>"
                               href="browse/by-region/<?= ($region_name) ?>"><span class=""><?= ($region_name) ?></span>&nbsp;<span
                                        class="badge badge-pill badge-fs"><?= ($count) ?></span></a>
                        </li>
                    <? } ?>
                </ul>
            </div>
            <a class="align-self-center" href="list">Give something away</a>
        </div>
    </div>
</div>

<div id="html-mobile_search" class="collapse">
    <form action='search/search' method='get' id="form-mobile_search">
        <div class="form-group">
            <label class="text-muted d-flex">
                Search all of Freestuff
                <i class="fa fa-times ml-auto" data-toggle="collapse" href="#html-mobile_search" role="button"
                   aria-expanded="false" aria-controls="html-mobile_search"></i>
            </label>
            <div class="d-flex">
                <input class="form-control" type='text' name='q' placeholder="Search for Freestuff"
                       value="<?= (htmlentities(TemplateHandler::getSearchText())) ?>"/>
                <button type="submit" class="btn primary" aria-label="Search"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
</div>
