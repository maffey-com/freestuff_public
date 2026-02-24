<div class="container">
    <div id="page-header">
        &nbsp;
    </div>
    <div class="row" id="error-page">
        <div class="col text-center">
            <div class="row">
                <div class="col">
                    <h1>Whoops, it looks like we might have given this page away already!</h1>
                    <p class="mb-5">But really, we couldn't find the page you have requested, please try one of the options below to find what your looking for.</p>
                    <p>
                        <ul>
                            <? if (SecurityHelper::isLoggedIn()) {?>
                                <li>Click <a href="<?=APP_URL?>list">here</a> to list an item</li>
                                <li>Click <a href="<?=APP_URL?>my_freestuff">here</a> to view My Freestuff</li>
                            <?} else {?>
                                <li>Click <a href="<?=APP_URL?>login">here</a> to login or register</li>
                            <?}?>
                            <li>Click <a href="<?=APP_URL?>home">here</a> to browse current listings</li>
                            <li><a href="page/faq">Frequently Asked Questions</a></li>
                            <li><a href="page/terms">Terms &amp; Conditions</a></li>
                            <li><a href="page/policy">Privacy Policy</a></li>
                        </ul>
                    </p>
                    <img class="mt-5 img-fluid" src="img/hootie.png" />
                </div>
            </div>

        </div>
    </div>
</div>