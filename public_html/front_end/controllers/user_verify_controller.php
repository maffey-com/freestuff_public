<?php

class UserVerifyController extends _Controller {
    public function emailForm($verify_id) {
        $uv = UserVerify::instanceFromId($verify_id);

        // Check if they used a Yahoo email so we can warn about non delivery
        $yahoo_warn = strpos($uv->data, '@yahoo') > -1;


        #BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Verify Email Address');

        if ($uv->chechHasExpired() || !$uv->verify_id) {

            PageHelper::setViews("views/user_verify/email_expired.php");
        } else {
            PageHelper::setViews("views/user_verify/email_form.php");
        }

        PageHelper::setMetaTitle('Freestuff NZ - Verify Email Address');
        PageHelper::setMetaDescription('Freestuff NZ - Verify Email Address');

        include("templates/main_layout.php");
    }

    public function emailProcess($verify_id) {
        if (!FloodControlHelper::allow('uv_attempt'.$_SERVER["REMOTE_ADDR"],50, 2614400 )) {
            MessageHelper::setSessionErrorMessage('To many attempts');
            redirect(APP_URL."user_verify/email_form/".$verify_id);
        }

        $code = paramFromGet("code");

        $uv = UserVerify::instanceFromId($verify_id);

        if ($uv->verify_id) {
            if ($uv->checkCode($code)) {
                redirect(APP_URL . UserVerify::$verify_types[$uv->verify_type] . "/" . $verify_id . "/" . $code);
            }
        }

        MessageHelper::setSessionErrorMessage('Invalid Code');
        redirect(APP_URL."user_verify/email_form/".$verify_id);

    }

    public function mobileForm($verifiy_id) {
        $uv = UserVerify::instanceFromId($verifiy_id);

        #BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Verify Mobile Number');

        if ($uv->chechHasExpired() || !$uv->verify_id) {
            PageHelper::setViews("views/user_verify/mobile_expired.php");

        } else {
            PageHelper::setViews("views/user_verify/mobile_form.php");
        }

        PageHelper::setMetaTitle('Freestuff NZ - Verify Mobile Number');
        PageHelper::setMetaDescription('Freestuff NZ - Verify Mobile Number');


        include("templates/main_layout.php");
    }

    public function mobileProcess($verify_id) {
        if (!FloodControlHelper::allow('uv_attempt'.$_SERVER["REMOTE_ADDR"],50, 2614400 )) {
            MessageHelper::setSessionErrorMessage('To many attempts');
            redirect(APP_URL."user_verify/mobile_form/".$verify_id);
        }
        $code = paramFromGet("code");

        $uv = UserVerify::instanceFromId($verify_id);

        if ($uv->verify_id) {
            if ($uv->checkCode($code)) {
                redirect(APP_URL . UserVerify::$verify_types[$uv->verify_type] . "/" . $verify_id . "/" . $code);
            }
        }

        MessageHelper::setSessionErrorMessage('Invalid Code');
        redirect(APP_URL."user_verify/mobile_form/".$verify_id);

    }

    public function landlineForm($verifiy_id) {
        $uv = UserVerify::instanceFromId($verifiy_id);

        #BreadcrumbHelper::addBreadcrumbs('My Account', APP_URL . 'my_freestuff');
        BreadcrumbHelper::addBreadcrumbs('Verify Phone Number');

        if ($uv->chechHasExpired() || !$uv->verify_id) {
            PageHelper::setViews("views/user_verify/landline_expired.php");
        } else {
            PageHelper::setViews("views/user_verify/landline_form.php");
        }

        PageHelper::setMetaTitle('Freestuff NZ - Verify Landline Number');
        PageHelper::setMetaDescription('Freestuff NZ - Verify Landline Number');

        include("templates/main_layout.php");
    }

    public function landlineProcess($verify_id) {
        if (!FloodControlHelper::allow('uv_attempt'.$_SERVER["REMOTE_ADDR"],50, 2614400 )) {
            MessageHelper::setSessionErrorMessage('To many attempts');
            redirect(APP_URL."user_verify/landline_form/".$verify_id);
        }
        $code = paramFromGet("code");

        $uv = UserVerify::instanceFromId($verify_id);

        if ($uv->verify_id) {
            if ($uv->checkCode($code)) {
                redirect(APP_URL . UserVerify::$verify_types[$uv->verify_type] . "/" . $verify_id . "/" . $code);
            }
        }

        MessageHelper::setSessionErrorMessage('Invalid Code');
        redirect(APP_URL."user_verify/landline_form/".$verify_id);

    }

    public function expired() {

    }

    public function resendEmail($verify_id) {
        $uv = UserVerify::instanceFromId($verify_id);
        $uv->sendEmail();
        if (ErrHelper::hasErrors()) {
            $error = ErrHelper::getFirstError();
            MessageHelper::setSessionErrorMessage($error->message);
        } else {
            MessageHelper::setSessionSuccessMessage('Email Resent');
        }
        redirect(APP_URL."user_verify/email_form/".$verify_id);
    }

    public function resendSMS($verify_id) {
        $uv = UserVerify::instanceFromId($verify_id);
        $uv->sendSMS();
        if (ErrHelper::hasErrors()) {
            $error = ErrHelper::getFirstError();
            MessageHelper::setSessionErrorMessage($error->message);
        } else {
            MessageHelper::setSessionSuccessMessage('Txt Message Resent');
        }

        redirect(APP_URL."user_verify/mobile_form/".$verify_id);
    }
}