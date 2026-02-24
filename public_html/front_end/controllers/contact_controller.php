<?php
class ContactController extends _Controller {
    public function index() {
        PageHelper::setMetaTitle("Freestuff NZ - Contact Us");
        PageHelper::setMetaDescription("Freestuff NZ - Contact Us");
        PageHelper::addPageJavascriptAfterPageLoaded('https://www.google.com/recaptcha/api.js');

        TemplateHandler::setSelectedMainTab("contact");

        PageHelper::setViews("views/contact/form.php");
        BreadcrumbHelper::addBreadcrumbs('Contact Us');

        include("templates/main_layout.php");
    }

    public function advertisement() {
        PageHelper::setMetaTitle("Freestuff NZ - Contact Us");
        PageHelper::setMetaDescription("Freestuff NZ - Contact Us");
        PageHelper::addPageJavascriptAfterPageLoaded('https://www.google.com/recaptcha/api.js');

        TemplateHandler::setSelectedMainTab("contact");

        PageHelper::setViews("views/contact/advertise_here_form.php");
        BreadcrumbHelper::addBreadcrumbs('Contact Us');

        include("templates/main_layout.php");
    }

    public function processSubmit($ad = false) {
        $contact = new Contact();
        $contact->buildFromPost();

        if($ad){
            $enquiry = 'ADVERTISEMENT ENQUIRY: <br/><br/>';
            $enquiry .= '<br/>Company: ' . paramFromPost('company_name');
            $enquiry .= '<br/>Industry / Nature of Business: ' . paramFromPost('industry');
            $enquiry .= '<br/><br/>Enquiry:<br/>';

            $contact->enquiry = $enquiry . $contact->enquiry.'<br/>';
        }

        if ($contact->insert()) {
            MessageHelper::setSessionSuccessMessage('<div class=""><p><b>Your message has been sent</b></p><p>We will look at your query soon and get back to you if appropriate.</p></div>');
            echo 1;
        } else {
            echo json_encode(ErrHelper::getErrorsFormtoolsHash());
        }
    }

    public function thankYou() {
        TemplateHandler::setSelectedMainTab("contact");
        PageHelper::setViews("views/contact/thank_you.php");

        BreadcrumbHelper::addBreadcrumbs('Contact Us');

        include("templates/main_layout.php");
    }
}