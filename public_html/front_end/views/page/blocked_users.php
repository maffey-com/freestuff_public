<div class="container">
    <?
    TemplateHandler::echoPageTitle('Blocked Users List');
    ?>

    <div id="faq-page">
        <? foreach ($blocked_users as $blocked_user) { ?>
            <div class="row d-flex align-items-center">
                <div class="col-2">
                    <b><?= $blocked_user->firstname ?></b> of
                    <b><?= clean(District::displayShort($blocked_user->district_id)) ?></b>;
                </div>
                <div class="col-2">
                    <span class="btn btn-danger btn-sm unblock_user" style="font-size: 12px;"
                          data-other_user_id="<?= $blocked_user->user_id ?>">Unblock user
                    </span>
                </div>
            </div>
        <? } ?>
    </div>
</div>
