<div class="card card-border-color card-table">
    <div class="row card-header">

        <div class="col-2 text-right">
            <a href="<?= APP_URL ?>/district/add">
                <button class="btn btn-success"><i class="fa fa-plus"></i> Add District</button>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="cr-datatable-wrapper">
            <div class="row cr-datatable-body">
                <div class="col-sm-12">
                    <? foreach (District::getAllNested() as $region => $districts) { ?>
                        <h4><?= $region ?></h4>
                        <?
                        $comma = '';
                        foreach ($districts as $district_id => $district) { ?>
                            <?= $comma ?>
                            <a href="district/edit/<?= $district_id ?>"><?= $district ?></a>

                            <?
                            $comma = ', ';
                        } ?>

                    <? } ?>

                </div>
            </div>

        </div>
    </div>
</div>

