<?php
class ListImageController extends _Controller {
    public function upload($tmp_id) {
        if (!empty($_FILES)) {
            $tmp_id = (int)$tmp_id;

            if (!empty($tmp_id)) {
                $temp_img = new FileHelper('temporary_listing_image', "temp_" . $tmp_id);
                $temp_img->setFileField("file", FALSE);
                $temp_img->processUploadQueue();
            }

            if (hasErrors()) {
                echo paramFromHash(0, getErrors());
            } else{
                echo 1;
            }
        }
        die();
    }
}