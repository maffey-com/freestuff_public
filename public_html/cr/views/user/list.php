<form class="form-inline" method='GET' action="<?= (APP_URL) ?>user/filter_list">
    <div class="well">
        <div class="control-group">
            <input class="input-large" type='text' name='filter_name' value='<?= ($filter->name) ?>'
                   placeholder="Search (e.g. Firstname, Email, ID, mobile #)"/>
            <button class="btn btn-info" type="submit">Filter</button>
        </div>
    </div>
</form>

<? /*<div class="navbar">
    <div class="navbar-inner">
        <h6>User list</h6>
        <div class="nav pull-right">
            <a href="#" class="dropdown-toggle navbar-icon" data-toggle="dropdown"><i class="icon-cogs"></i></a>
            <ul class="dropdown-menu pull-right">
                <li><a href="<?=SITE_URL?>/cr/user/add"><i class="icon-plus"></i>Add a new user</a></li>
            </ul>
        </div>
    </div>
</div>*/ ?>
<div class="table-bordered table-striped table-hover" id="list-users">
    <table class="table table-striped">
        <thead>
        <tr role="row">
            <th width="50"><?= ($dw_user->sortableColumnHeading("user_id", "User id")) ?></th>
            <th><?= ($dw_user->sortableColumnHeading("firstname", "Firstname")) ?></th>
            <th><?= ($dw_user->sortableColumnHeading("email", "Email")) ?></th>
            <th>District</th>
            <th width="120"><?= ($dw_user->sortableColumnHeading("created_on", "Created on")) ?></th>
            <th width="120"><?= ($dw_user->sortableColumnHeading("last_login", "Last login")) ?></th>
            <th width="120"><?= ($dw_user->sortableColumnHeading("email_bounced_date", "Email bounced date")) ?></th>
            <th><?= ($dw_user->sortableColumnHeading("user_listing_count", "Give")) ?></th>
            <th><?= ($dw_user->sortableColumnHeading("user_request_count", "Req")) ?></th>
            <th><?= ($dw_user->sortableColumnHeading("thumbs_up", "<i class=\"ico-thumbs-up\"></i>")) ?></th>
            <th><?= ($dw_user->sortableColumnHeading("thumbs_down", "<i class=\"ico-thumbs-down\"></i>")) ?></th>
            <th width="320">Action</th>
        </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_user->data as $row) {
            $row_user_id = $row['user_id'];
            ?>
            <tr class="user" data-user_id="<?= ($row_user_id) ?>">
                <td>
                    <?= ($row_user_id) ?>
                </td>
                <td>
                    <?= ($row['firstname']) ?>
                </td>
                <td>
                    <?= ($row['email']) ?>
                    <br/>
                    <?= ($row['mobile']) ?>
                    <br/>
                    (<a target="_blank" href="<?= (APP_URL) ?>user/history/<?= ($row_user_id) ?>"
                        class="dynamic-bootbox">View history</a>)
                </td>
                <td>
                    <?= District::display($row['district_id']) ?>
                </td>
                <td>
                    <?= ($row['created_on']) ?>
                </td>
                <td>
                    <?= ($row['last_login']) ?>
                </td>
                <td>
                    <?= ($row['email_bounced_date']) ?>
                </td>
                <td><?= ($row['user_listing_count']) ?></td>
                <td><?= ($row['user_request_count']) ?></td>
                <td><?= ($row['thumbs_up']) ?></td>
                <td><?= ($row['thumbs_down']) ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= (APP_URL) ?>user/edit/<?= ($row_user_id) ?>"><i
                                class="ico-pencil"></i> Edit</a>
                    <a class="btn btn-primary dynamic-bootbox"
                       href="<?= (APP_URL) ?>user/change_other_password/<?= ($row_user_id) ?>"><i class="ico-lock"></i>
                        Password</a>
                    <a class="btn btn-danger action-delete" href="<?= (APP_URL) ?>user/delete/<?= ($row_user_id) ?>"><i
                                class="ico-ban-circle"></i> Delete</a>

                    <button class="btn action-ban"><i class="ico-eject"></i> Ban</button>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
    <?= ($dw_user->displayPaging()) ?>
</div>

<script type='text/javascript'>
    $(document).ready(function () {
        // delete action
        $("#list-users .action-delete").click(function (e) {
            e.preventDefault();

            var el_url = $(this).attr('href');
            var el_tr = $(this).closest(".user");
            var user_id = $(el_tr).data("user_id");

            bootbox.setIcons({
                "CANCEL": "icon-ban-circle",
                "CONFIRM": "icon-ok-sign icon-white"
            });

            bootbox.confirm("Are you sure you want to delete this user?", function (result) {
                if (result) {
                    document.location = el_url;
                }
            });
        });

        // ban action
        $("#list-users .action-ban").click(function (e) {
            var el_tr = $(this).closest(".user");
            var user_id = $(el_tr).data("user_id");

            bootbox.prompt("Reason to ban this user?", function (reason) {
                if (reason === null) {

                } else {
                    document.location = '<?=(APP_URL)?>user/ban/' + user_id + "?reason=" + reason;
                }
            });
        });
    });
</script>



