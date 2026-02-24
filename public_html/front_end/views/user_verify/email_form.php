<div class="container">
    <?
    TemplateHandler::echoPageTitle('Verify your email address');
    ?>

    <div class="row fs-block">
        <div class="col">
            <p>We have sent you an email containing a six digit code. Please enter this code in the box below and press the Verify Code button.</p>
        </div>
    </div>
    <div class="row" id="verify-email-page">
        <div class="col-12">
            <form class="form-inline" id='edit_email_form' method='get' action='<?= (APP_URL) ?>user_verify/email_process/<?=$uv->verify_id?>'>
                <div class="form-group mb-2 mr-2">
                    <label for="code_field" class="sr-only">Please enter the code we just sent to your email address.</label>
                    <input type="text" name="code" class="form-control" id="code_field" />
                </div>
                <button type='submit' class='btn primary mb-2'>Verify Code</button>
            </form>
            <span class="form-text small text-muted">If you do not receive an email from us, please check your spam/junk folder. Otherwise <a href="<?=APP_URL?>user_verify/resend_email/<?=$uv->verify_id?>">click here</a> to resend the email</span>
        </div>
    </div>
    <? if ($yahoo_warn) {?>
    <div class="row mt-4 text-danger">
        <div class="col-12">
            <h5>Note to Yahoo email users</h5>
            <p>We are having issues with Yahoo email addresses not receiving our messages. It's not just us though, in 2016 Spark moved all Xtra customer emails away from Yahoo following 9 years of security hassles, spam, phishing attacks and confusion.<br /><cite>(source: <a target="_blank" href="https://www.nbr.co.nz/article/spark-finally-ditches-yahoo-moves-xtra-mail-new-zealand-company-smx-ck-194220">https://www.nbr.co.nz/article/spark-finally-ditches-yahoo-moves-xtra-mail-new-zealand-company-smx-ck-194220</a>)</cite><br /><br />
               If you are having trouble receiving our emails we recommend that you try signing up with another email address. If you don't have another email address consider signing up for a free <a target="_blank" href="https://mail.google.com/mail/signup">Google email here</a> or a <a target="_blank" href="https://signup.live.com/signup">Microsoft email here</a>.
            </p>
        </div>
    </div>
    <?}?>
</div>
