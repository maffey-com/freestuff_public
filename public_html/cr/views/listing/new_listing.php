<form class="form-inline" method='GET' action="<?=(APP_URL)?>listing/current_list">
    <div class="well">
        <div class="control-group">
            <input class="input-large" type='text' name='filter_search' value='<?=($filter->search)?>' placeholder="Search (e.g. title, description, listing ID)" />
            <button class="btn btn-info" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-bordered table-striped table-hover table-overflow-auto" id="list-listings">
    <table class="table table-striped">
        <thead>
            <tr role="row">
                <th width="120">Image</th>
                <th width="75">Date</th>
                <th width="100">Location</th>
                <th>Info</th>
                <th width="240" style="min-width: 230px">Action</th>
            </tr>
        </thead>

        <tbody role="alert" aria-live="polite" aria-relevant="all">
        <?
        foreach ($dw_listing->data as $listing) {
            $row_listing_id = $listing['listing_id'];

            $temp_img = new FileHelper('listing_images', $row_listing_id);
            $fullsize_img = $temp_img->getImagePathFromTag("most_recent_upload", 120, 120);
            ?>
            <tr data-listing_id="<?=($row_listing_id)?>">
                <td>
                    <img src="<?=($fullsize_img)?>" />
                </td>
                <td>
                    <?=(DateHelper::display($listing["listing_date"]))?>
                </td>
                <td>
                    <?=(District::display2($listing['district_id']))?>
                </td>
                <td>
                    <b>ID: <?= ($row_listing_id) ?></b> (<a target="_blank" href="<?=(SITE_URL)?>view?listing_id=<?=($row_listing_id)?>">Front end view</a>)
                    <br />
                    <b>Listed by: <?=($listing["user_firstname"])?></b> (<a target="_blank" href="<?=(APP_URL)?>user/edit/<?=($listing['user_id'])?>">View user</a>)
                    <br /><br />
                    <b><?= ($listing["title"]) ?></b>
                    <br /><?= trim($listing["description"]) ?>
                </td>
                <td>
                    <a class="btn btn-primary" href="<?=(APP_URL)?>listing/edit/<?=($row_listing_id)?>"><i class="ico-pencil"></i> Edit</a>
                    <? if ($listing["listing_type"] == 'free') { ?>
                        <button class="btn action-wanted"><i class="ico-heart"></i> Wanted</button>
                    <? } ?>
                    <br/>

                    <button class="btn btn-danger action-reject" title="Unauthorise + disapprove listing"><i class="ico-thumbs-down"></i> Reject</button>
                    <button class="btn btn-danger action-delete" title="Delete listing record from database"><i class="ico-trash"></i> Delete</button>
                    <button class="btn btn-danger action-boot" title="Ban this user AND remove all listings"><i class="ico-user"></i> Boot</button>
                    <br/>



                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>

<?
require_once("views/listing/common_list_js.php");


