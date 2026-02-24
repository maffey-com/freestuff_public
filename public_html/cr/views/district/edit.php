

<form class="form-horizontal" method='POST' action='<?=APP_URL?>/district/save' class='standard_form' id='district_form'>
	<input type='hidden' name='district_id' value='<?=($district->district_id)?>' />

    <div class="card card-border-color card-table">
        <div class="card-header">
        </div>
        <div class="card-body">
            			<div class="form-group row mt-2">
				<label class="col-3 col-lg-2 col-form-label text-right">District</label>
				<div class="col-auto"><input class='form-control' maxlength='45' type='text' name='district' <?=FormHelper::textValue($district->district)?> /></div>
			</div>
			<div class="form-group row mt-2">
				<label class="col-3 col-lg-2 col-form-label text-right">Region</label>
                <select class="form-control" name="region">
                <? foreach (District::$regions as $region) {
                    echo FormHelper::option($region,$region,$district->region);
                }?>
                </select>
			</div>

        </div>
    </div>
   <div class="cr-page-footer">
        <div class="container-fluid">
            <div class="form-actions text-right">
                <a href="user" class="btn btn-danger" id="Cancel">Cancel</a>
                <button class="btn btn-info" type="submit">Save</button>
            </div>
        </div>
    </div>
</form>
<script>

    $(document).ready(function(){
        $('#district_form').formTools2({
            onStart: function () {

            },
            onComplete: function (msg) {

            },
            onSuccess: function () {
                document.location = 'district';
            }
        });
    });

</script>
