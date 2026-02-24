<?php
if (count($paging['links'])) {
    $max_pages = 5;

    $current_page = (int)paramFromGet("dw_pg_browse");
    $current_page = empty($current_page) ? 1 : $current_page;

    $total_pages = max(array_keys($paging['links']));

    $start_page = max(ceil($current_page - ($max_pages / 2)), 1);
    $end_page = min(max(floor($current_page + ($max_pages / 2)), 1), $total_pages);

    if ($end_page - $start_page < $max_pages) {
        if ($end_page < $max_pages) {
            $end_page = min($start_page + ($max_pages - 1), $total_pages);
        } else {
            $start_page = max($end_page - ($max_pages - 1), 1);
        }
    }

    $out = array();
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $out[$i] = "";
        } else {
            $out[$i] = $paging['links'][$i];
        }
    }
    ?>
    <div class="row" id='paging'>
        <div class="col">
            <div class="paging-container">
                <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= $current_page === 1 ? 'disabled' : '' ?>" title="Go to first page">
                        <a class="page-link" href="<?= $paging['first'] ?>" aria-label="first">
                            <span aria-hidden="true" class="fa fa-angle-double-left"></span>
                            <span class="sr-only">First</span>
                        </a>
                    </li>
                    <li class="page-item <?= $current_page === 1 ? 'disabled' : '' ?>" title="Go to previous page">
                        <a class="page-link" href="<?= $paging['prev'] ?>" aria-label="Previous">
                            <span aria-hidden="true" class="fa fa-angle-left"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <? if (count($out) > 0) {
                    $first = array_keys($out)[0];
                    $last = array_keys($out)[count($out) - 1];
                    foreach ($out

                    as $page_no => $paging_url) {
                    if ($page_no > 1 && $page_no === $first) {
                        echo '<li class="page-item"><a class="page-link ellipsis"><span class="fa fa-ellipsis-h"></span></a></li>';
                    } ?>
                    <li class="page-item <?= $page_no === $current_page ? 'active' : '' ?>"><a class="page-link"
                                                                                               <?= $paging_url ? 'href="' . $paging_url . '"' : '' ?>class="<?= $current_page == $page_no ? 'active' : '' ?>"><?= $page_no ?><?= $page_no === $current_page ? '<span class="sr-only">(current)</span>' : '' ?></a>
                        <? if ($page_no < $total_pages && $page_no === $last) {
                            echo '<li class="page-item"><a class="page-link ellipsis"><span class="fa fa-ellipsis-h"></span></a></li>';
                        }
                        }
                        } ?>
                    <li class="page-item <?= $current_page == $paging['total_pages'] ? 'disabled' : '' ?>" title="Go to next page">
                        <a class="page-link" href="<?= $paging['next'] ?>" aria-label="Next">
                            <span aria-hidden="true" class="fa fa-angle-right"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                    <li class="page-item <?= $current_page == $paging['total_pages'] ? 'disabled' : '' ?>" title="Go to last page">
                        <a class="page-link" href="<?= $paging['last'] ?>" aria-label="Last">
                            <span aria-hidden="true" class="fa fa-angle-double-right"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
            </div>
        </div>
    </div>
    <?
}