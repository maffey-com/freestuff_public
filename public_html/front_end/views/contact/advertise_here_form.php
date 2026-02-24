<div class="container">
    <?  TemplateHandler::echoPageTitle('Contact Us', 'Use the form below to contact us.')?>

    <div id="contact-page">
        <div class="row">
            <div class="col">
                <form name="contact" method="POST" action="<?=(APP_URL)?>contact/process_submit/ad" id="contact_form">
                    <?
                    TemplateHandler::echoSubTitle('Online Enquiries');
                    ?>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" class="form-control" name="name" value="<?= (paramFromPost('name', paramFromSession('session_firstname'))) ?>" required="required" maxlength="40"/>
                    </div>
                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input type="text" id="company_name" class="form-control" name="company_name" value="<?= (paramFromPost('company_name', paramFromSession('session_company_name'))) ?>" required="required" maxlength="40"/>
                    </div>
                    <div class="form-group">
                        <label for="industry">Industry / Nature of Business</label>
                        <input type="text" id="industry" class="form-control" name="industry" value="<?= (paramFromPost('industry', paramFromSession('session_industry'))) ?>" required="required" maxlength="40"/>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="text" id="email" class="form-control" name="email" value="<?= (paramFromPost('email', paramFromSession('session_email'))) ?>" required="required" maxlength="50"/>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" class="form-control" name="phone" value="<?= (paramFromPost('phone', paramFromSession('session_mobile'))) ?>" required="required" maxlength="40"/>
                    </div>
                    <div class="form-group">
                        <label for="enquiry">Enquiry</label>
                        <textarea id="enquiry" name="enquiry" class="form-control" required="required" rows="5"><?= (paramFromPost('enquiry')) ?></textarea>
                    </div>
                    <div class="form-group" id="contact-captcha">
                        <? Recaptcha::insertCaptcha(); ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn primary btn-mobile">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>