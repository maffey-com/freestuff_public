<div class="container">
    <?  TemplateHandler::echoPageTitle('Contact Us', 'Use the form below to contact us.')?>

    <div id="contact-page">
        <div class="row">
            <div class="col">
                <p>If your query relates to a specific item, please use the "Report this item" link to send us a message about that item</p>

                <form name="contact" method="POST" action="<?=(APP_URL)?>contact/process_submit" id="contact_form">
                    <h2>Online Enquiries</h2>

                    <input type="text" name="fs_fax" class="d-none" />

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" class="form-control" name="name" value="<?= (paramFromPost('name', paramFromSession('session_firstname'))) ?>" required="required" maxlength="40"/>
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