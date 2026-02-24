<div class="container">
    <?
    TemplateHandler::echoPageTitle('Set your closest district');
    ?>
    <div class="row">
        <div class="col">
            <? if (isset($_GET['oneoff'])) {?>
                <p>To improve privacy and simplicity, we have changed how we collect location data.</p>
                <p>Please tell us your nearest district to continue: </p>
            <?}?>
            <p></p>
            <form id='edit_district_form' method='post' action='<?= (APP_URL) ?>account/update_location' autocomplete='off'>

                <div class="form-group">

                    <select class="form-control" name="district_id">
                        <option>Please Select</option>
                        <? foreach (District::getAllNested() as $region_name => $districts) {
                            foreach ($districts as $district_id => $district_name) {
                                echo FormHelper::option(District::display($district_id), $district_id,$user->district_id);
                            }

                        }?>
                    </select>

                </div>

                <div class="form-group">
                    <input type='submit' class='btn primary btn-mobile' value='Update'/>
                </div>
            </form>
        </div>
    </div>
</div>
