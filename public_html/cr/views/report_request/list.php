<form class="form-inline" method='GET' action="<?=(APP_URL)?>report_request/filter_list">
    <div class="well">
        <div class="control-group">
            <input class="input-large" type='text' name='filter_hours' value='<?= ($filter->hours) ?>' placeholder="Listing in the last N hours (e.g. 72)"/>
            <input class="input-large" type='text' name='filter_listing_id' value='<?= ($filter->listing_id) ?>' placeholder="Listing ID"/>

            <button class="btn btn-info" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-bordered table-striped table-hover" id="list-request">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="80"><?= ($dw_listing->sortableColumnHeading("listing_id", "Listing ID")) ?></th>
                <th><?= ($dw_listing->sortableColumnHeading("title", "Item")) ?></th>
                <th width="280"><?= ($dw_listing->sortableColumnHeading("email", "Lister")) ?></th>
                <th width="120"><?= ($dw_listing->sortableColumnHeading("listing_date", "Listing date")) ?></th>
                <th width="100">Action</th>
            </tr>
        </thead>
        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_listing->data as $row) {
            $row_listing_id = $row['listing_id'];
            ?>
            <tr data-listing_id="<?= ($row_listing_id) ?>">
                <td>
                    <?= ($row_listing_id) ?>
                </td>
                <td>
                    <?= ($row['title']) ?>
                </td>
                <td>
                    <?= ($row['email']) ?>
                    <br />
                    [ID: <?=($row['user_id'])?>]
                </td>
                <td>
                    <?= ($row['listing_date']) ?>
                </td>
                <td>
                    <a class="btn" href="<?=(APP_URL)?>report_request/view/<?=($row_listing_id)?>"><i class="ico-eye-open"></i> View (<?=($row['requests'])?>)</a>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
	<?=($dw_listing->displayPaging())?>
</div>