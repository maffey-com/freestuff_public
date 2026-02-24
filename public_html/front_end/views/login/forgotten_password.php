<div class="container">
    <?
    TemplateHandler::echoPageTitle('Forgotten Password');
    ?>

    <div id="page-forgotten_password">
        <div class="row mb-4">
            <div class="col">
                <p>Please enter either the email address OR the mobile phone number that you used to register with Freestuff.</p>
            </div>
        </div>

        <div class="row align-items-center" id="forgotten-password-page">
            <div class="col-12 col-md">
                <form method='post' action='<?= (APP_URL) ?>login/forgotten_password_email' id="forgotten_password_email_form">
                    <div class="form-group">
                        <h4>Reset using your email address</h4>
                    </div>

                    <div class="form-group">
                        <input type="email" id="email" class="form-control" name="email" placeholder="Email Address"/>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="type" value="email"/>
                        <button type="submit" class="btn primary">Reset via Email</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-2" id="reset-or"/>
            OR
        </div>

        <div class="col-12 col-md">
            <form method='post' action='<?= (APP_URL) ?>login/forgotten_password_sms' id="forgotten_password_mobile_form">
                <input type="hidden" name="type" value="mobile"/>

                <div class="form-group">
                    <h4>Reset using your mobile number</h4>
                </div>
                <div class="form-group">
                    <select name="mobile_prefix" class="form-control d-inline-block w-auto mr-1">
                        <option value="020">020</option>
                        <option value="021">021</option>
                        <option value="022">022</option>
                        <option value="027">027</option>
                        <option value="028">028</option>
                        <option value="029">029</option>
                    </select>
                    <input type="tel" id="mobile" class="form-control d-inline-block w-auto" name="mobile" placeholder="Mobile Number"/>
                </div>

                <div class="form-group ">
                    <button type="submit" class="btn secondary">Reset via Mobile</button>
                </div>
            </form>
        </div>
    </div>
</div>

