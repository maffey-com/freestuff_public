<?php
class Recaptcha {
    public static function checkCaptcha() {
        //recapture
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $url .= "?secret=".RECAPTCHA_SITE_SECRET;
        $url .= "&response=" . paramFromPost("g-recaptcha-response");
        $url .= "&remoteip=" . $_SERVER["REMOTE_ADDR"];
        $result = file_get_contents($url);
        $result = json_decode($result);
        $success = $result->success;
        if ($success != "true") {
            ErrHelper::raise("You need to tick the 'I'm not a robot' box.", 99,"captcha");
        }
    }

    public static function insertCaptcha() {
        echo '<div class="g-recaptcha" data-sitekey="'.RECAPTCHA_SITE_KEY.'"></div>';
        echo (fieldHasError("captcha")?'<div class="captcha-error">'.fieldHasError("captcha").'</div>':'');
    }
}