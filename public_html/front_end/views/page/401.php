<div class="container">
    <div id="page-header">
        &nbsp;
    </div>
    <div class="row" id="error-page">
        <div class="col-12 col-lg-8">


            <? if (SecurityHelper::isLoggedIn()) { ?>
                <h1>Something is not quite right</h1>
                <br/>
                <p>You are logged in as <?= $_SESSION['session_firstname'] ?>
                    from <?= $_SESSION['session_district']->district ?>.</p>

                <p>If this is you, <a href="my_freestuff">click here to see your account and listings</a>
                </p>
                <p>If this is not you, <a href="account/logout_and_login">click here to login as you</a></p>


            <? } else { ?>
                <h1>You need to log in to see this page</h1>
                <br/>
                <p>Click <a href="<?= APP_URL ?>login">here</a> to login or register</p>
            <? } ?>

        </div>
        <div class="col-12 col-lg-4">
            <img class="mt-5 img-fluid" src="img/hootie.png"/>
        </div>
    </div>

</div>