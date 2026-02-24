<div class="modal fade " id="modal-box" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <? if (ModalHelper::getTitle()) { ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle"><?= ModalHelper::getTitle() ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?
                foreach (ModalHelper::getViews() as $view_path) {
                    include($view_path);
                }
                ?>
            </div>
        <? } else {
            foreach (ModalHelper::getViews() as $view_path) {
                include($view_path);
            }
        }
        ?>
    </div>
    <? ModalHelper::echoJS(); ?>
</div>

