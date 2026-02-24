<?php
/**
 * Created by PhpStorm.
 * User: maggie
 * Date: 19/07/2018
 * Time: 11:11 AM
 */
$breadcrumbs = BreadcrumbHelper::getInstance()->getBreadcrumbs();

if (count($breadcrumbs) > 0) {
    ?>
    <div class="container" id="page-breadcrumb">
        <div class="row">
            <nav aria-label="breadcrumb" class="col px-0">
                <ol class="breadcrumb d-none d-lg-flex">
                    <li class="breadcrumb-item">
                        <a href="<?= (APP_URL) ?>">Home</a>
                    </li>
                    <?
                    foreach ($breadcrumbs as $tmp_label => $tmp_url) {
                        echo '<li class="breadcrumb-item" aria-current="page">';

                        if (empty($tmp_url)) {
                            echo $tmp_label;
                        } else {
                            echo '<a href="' . $tmp_url . '">' . $tmp_label . '</a>';
                        }

                        echo '</li>';
                    }
                    ?>
                </ol>
                <?/*
                <ol class="breadcrumb d-flex d-lg-none">
                    <li class="breadcrumb-item">
                        &laquo; <a href="#" id="btn-history_back">Back</a>
                    </li>
                </ol>*/?>
            </nav>

        </div>
    </div>
    <?
}