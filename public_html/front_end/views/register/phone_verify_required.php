<div class="container">
    <?
    TemplateHandler::echoPageTitle('Phone Verification', 'Nearly there. Last step to finish registering');
    ?>

    <div class="row" id="phone-verify-required-page">
        <div class="col">
            <p>Please help us keep our site free from spam by validating your account with a New Zealand phone number. There are two ways to do this:</p>
            <ol>
                <li>Enter your MOBILE number, and we will send your validation code by TXT message (SMS) <br/><br/><b>OR</b><br/><br/></li>
                <li>Enter your LAND LINE number and Robby the robot will phone you with your validation code.</li>
            </ol>
            <input type="button" id="btn_validate_by_mobile" value="Validate by mobile" class="btn secondary"/> &nbsp; <b>OR</b>
            &nbsp;
            <input type="button" id="btn_validate_by_landline" value="Validate by landline" class="btn secondary"/>
        </div>
    </div>
    <div id="mobile_validation_wrapper" class="phone-validation-wrapper" style="display:none;">
        <h4>Please enter your mobile number below:</h4>
        <form class="form-inline" method="post" id="form_mobile_validation" action="register/send_code_via_sms">
            <input type="hidden" name="user_id" value="<?= ($user_id) ?>"/>
            <select name="mobile_prefix" class="form-control m-2">
                <?= option("020", "020") ?>
                <?= option("021", "021") ?>
                <?= option("022", "022") ?>
                <?= option("026", "026") ?>
                <?= option("027", "027") ?>
                <?= option("028", "028") ?>
                <?= option("029", "029") ?>
            </select>
            <input type="text"  class="form-control m-2" name="mobile" value="" />
            <button type="submit" class="btn primary m-2">Send my code</button>
        </form>
    </div>
    <div id="landline_validation_wrapper" class="phone-validation-wrapper" style="display:none;">
        <h4>Please enter your Land Line number below:</h4>
        <form class="form-inline" method="post" id="form_landline_validation" action="register/send_code_via_landline">
            <input type="hidden" name="user_id" value="<?= ($user_id) ?>"/>

            <? if (UserVerify::validLandlineCallingTime()) { ?>
                <select name="landline_prefix" class="form-control ">
                    <?= option("09", "09") ?>
                    <?= option("07", "07") ?>
                    <?= option("06", "06") ?>
                    <?= option("04", "04") ?>
                    <?= option("03", "03") ?>
                </select>
                <input type="text" name="landline" class="form-control m-2"/>
                <button type="submit" class="btn primary">Dial me now</button>
            <? } ?>
            <small class="form-text text-muted">(We know how much you hate a random phone call in the middle of the night. This facility is only available between 9am - 9pm)</small>
        </form>
    </div>
</div>
