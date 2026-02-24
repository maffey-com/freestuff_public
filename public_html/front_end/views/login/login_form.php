<div class="container">
    <div class="row">
        <div class="col-12 col-md-6">
            <?
            TemplateHandler::echoPageTitle('Login', 'You need to be logged in to give away and receive free stuff.');
            ?>

            <div class="row">
                <div class="col">
                    <form method="post" action="<?=(APP_URL)?>login/process_login" id="login-form">
                        <input type="hidden" name="listing_id" value="<?=(paramFromGet('listing_id'))?>" />
                        <div class="form-group">
                            <label for="username">Email or Mobile No.</label>
                            <input type="text" class="form-control" name="username" id="username" value="" autofocus />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" value="" />
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-check">
                                    <label class="form-check-label" for="remember">
                                        <input type="checkbox" class="form-check-input" name="remember" id="remember" value="y" />
                                        Remember Me</label>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="d-block d-md-flex">
                                    <button type="submit" class="btn primary btn-mobile" id="form-login-btn">Login</button>
                                    <small class="form-text text-muted ml-md-2 d-md-inline-block"><a href="<?=(APP_URL)?>login/forgotten_password">Forgotten Password</a></small>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 mt-5 mt-md-0">
            <?
            TemplateHandler::echoPageTitle('Register', 'Not a member of Freestuff yet?');
            ?>

            <p>Register now and become a part of the growing Freestuff online family.</p>
            <a href="<?=(APP_URL)?>register" class="btn secondary btn-mobile" id="register-btn">Register Now!</a>
        </div>
    </div>
</div>