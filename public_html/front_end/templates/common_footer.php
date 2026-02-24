<?

$page_controller_method = strtolower(_Controller::getControllerName() . '/' . _Controller::getMethodName());
$is_message_conversation = ($page_controller_method == 'message/conversation');

?>


    <div style="background-color: #dddddd" class="pt-4 pb-5 mt-5">
        <div class="container text-center">
            <p class="text-secondary py-2">The Obligatory Advert</p>
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5315463470321244"
                    crossorigin="anonymous"></script>
            <!-- My Responsive -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-5315463470321244"
                 data-ad-slot="6436361523"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>

        </div>
    </div>

    <footer id='footer' class="<?= ($is_message_conversation ? 'd-none' : '') ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4 col-lg-4">
                    <div id="footer-sitemap_container">
                        <h6>Freestuff Website</h6>
                        <ul class="list-group">
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>page/faq">FAQ</a></li>
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>page/how_it_works">How Freestuff
                                    Works</a></li>
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>contact">Contact Us</a></li>
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>list">List An Item</a></li>
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>my_freestuff">My Freestuff</a></li>
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>login">Login</a></li>
                            <li class="list-group-item"><a href="<?= (APP_URL) ?>register">Register</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <?
                    $stats_cache_file = CACHEDIR . "/stats.html";
                    if (file_exists($stats_cache_file) && rand(1, 100) != 1) {
                        include($stats_cache_file);

                    } else {
                        //update statistics
                        $no_of_users = User::countRegisteredUsers();
                        $free_listings = Listing::countActiveFreeListings();
                        $wanted_listings = Listing::countActiveWantedListings();
                        $sql = "SELECT COUNT(*) FROM listing WHERE listing_type = 'free' AND datediff(now(), listing_date) <= 7";
                        $this_week_listings = runQueryGetFirstValue($sql);
                        ob_start();
                        include("views/index/stats.php");
                        $stats_contents = ob_get_flush();
                        file_put_contents($stats_cache_file, $stats_contents);
                    }
                    ?>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <div id="footer-recycle_container">
                        <img src="img/recycle.png" alt="Two people happily exchanging an item"/>
                        <h6>Recycle with Freestuff</h6>
                        <p>Find a new home for your unwanted goods. It's fast, easy &amp; FREE.</p>
                        <a class='btn primary' href="<?= (APP_URL) ?>list">Give Something Away Today!</a>
                    </div>
                </div>

                <div class="col-12" id="footer-copyright">
                    <p>
                        &copy;FREESTUFF NEW ZEALAND LIMITED - <a href="<?= (APP_URL) ?>page/terms">Terms &amp;
                            Conditions</a> - <a href="<?= (APP_URL) ?>page/privacy">Privacy
                            Policy</a>
                    </p>
                    <?
                    switch (_Controller::getControllerName()) {
                        case 'Home':
                            echo "<br/><a href='https://phplab.nz' title='PHP Developer Auckland New Zealand'>PHP</a><a href='https://phplab.nz' title='PHP Auckland'>Lab</a>";
                            break;

                    }
                    ?>
                </div>
            </div>
        </div>
    </footer>

<? if (DEVEL && !$is_message_conversation) { ?>
    <div class="container-fluid">
        <div class="row mb-4 pt-4">
            <div class="col">
                <h5>DEV instant account login:</h5>
                <?
                $test_users = array(
                        '7' => "chris@maffey.com",
                        '81054' => "rmarston@xtra.co.nz",
                        '12424' => "lambchop@maffey.com",
                        '2605' => "maggie@maffey.com",
                        '27' => "kelly@aquafilterproducts.co.nz",
                        '28' => "kez_baker@yahoo.co.nz",
                        '31' => "nicknkelly@xtra.co.nz",
                        '42888' => "an-thor@hotmail.com",
                        '6351' => "fuchser@xnet.co.nz",
                        '46764' => 'max.headroom@hotmail.co.nz'
                );
                foreach ($test_users as $_test_user_id => $test_user_email) { ?>
                    <a class="btn danger m-1"
                       href="test_user_login.php?uid=<?= ($_test_user_id) ?>"><?= ($test_user_email) ?><br/>[User
                        ID: <?= ($_test_user_id) ?>]</a>

                <? } ?>
            </div>
        </div>
    </div>
<? }
