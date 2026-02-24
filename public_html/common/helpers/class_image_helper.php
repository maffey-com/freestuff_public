<?php


class ImageHelper {

    public $image_magic_object;
    public $source_height;
    public $source_width;
    public $target_height;
    public $target_width;
    public $target_path;
    public $constrain;
    public $filename;
    public $file_extention;
    public $compression_quality = 100;
    public $pad_if_too_small = false;
    public $crop_if_too_big = false;
    public $enable_zoom = false;
    public $enable_watermark = false;
    public $options;

    function __construct($filename) {
        $this->filename = $filename;
        $this->options = array(
            "padding" => array(),
            "cropping" => array(),
            "zooming" => array(),
            "watermarking" => array()
        );
        if (file_exists($filename)) {
            try {
                $this->image_magic_object = new Imagick($filename);
            } catch (Exception $e) {
                $this->image_magic_object = new Imagick();
                $this->image_magic_object->newImage(100, 100, new ImagickPixel('white'));
                $this->image_magic_object->setImageFormat('jpeg');
            }
            $this->file_extention = strtolower(substr(strrchr($filename, '.'), 1));
            $this->setSourceWidthAndHeight();
        }
    }

    protected function setSourceWidthAndHeight() {
        $tmp_source_info = $this->image_magic_object->getImageGeometry();
        $this->source_width = (int)paramFromHash('width', $tmp_source_info);
        $this->source_height = (int)paramFromHash('height', $tmp_source_info);
    }

    function setPadding($pad_if_too_small, $background_colour = 'transparent', $horizontal_alignment = 'c', $vertical_alignment = 'm') {
        $this->pad_if_too_small = $pad_if_too_small;
        if ($pad_if_too_small) {
            $this->options["padding"]["horizontal_alignment"] = strtolower($horizontal_alignment);
            $this->options["padding"]["vertical_alignment"] = strtolower($vertical_alignment);
            $this->options["padding"]["background_colour"] = strtolower($background_colour);
        }

    }

    function setCropping($crop_if_too_big, $horizontal_alignment = 'c', $vertical_alignment = 'm') {
        $this->crop_if_too_big = $crop_if_too_big;
        if ($crop_if_too_big) {
            $this->options["cropping"]["horizontal_alignment"] = strtolower($horizontal_alignment);
            $this->options["cropping"]["vertical_alignment"] = strtolower($vertical_alignment);
        }
    }

    function setZoom($enable_zoom, $percentage = 8) {
        $this->enable_zoom = $enable_zoom;
        if ($enable_zoom) {
            $this->options["zooming"]["percentage"] = $percentage;
        }
    }

    function setWatermarking($enable_watermark,$file_path,$shrink_ratio = 0.5,$align_horizontal = 'l',$align_vertical = 't',$opacity = 0.8) {
        $this->enable_watermark = $enable_watermark;
        if ($this->enable_watermark) {
            $this->options["watermarking"]["file_path"] = $file_path;
            $this->options["watermarking"]["shrink_ratio"] = $shrink_ratio;
            $this->options["watermarking"]["align_horizontal"] = $align_horizontal;
            $this->options["watermarking"]["align_vertical"] = $align_vertical;
            $this->options["watermarking"]["opacity"] = $opacity;
        }
    }

    /*
    * @param int width.
    * @param int height
    * @param char constrain
    * All modes maintain aspect ratio of the image.  Image is never stretched or skewed.
    * width.  Resizes to the specified width.  Adjusts height to maintain aspect ratio
    * height. Resizes to the specified height.  Adjusts height to maintain aspect ratio
    * inside.  Returns an image that fits inside the box
    *          ++ Usually you will want to pad this
    * thumbnail. Uses the dimension that needs the least resizing. Gives the biggest possible picture.
    *          ++ Usually one dimension of the image will hang out of the box and you will want to crop.
     *          Cropping is turned on by default with thumbnail contrain
        */
    function setTargetWidthAndHeight($width, $height, $constrain = 'width') {
        $this->target_width = (int)$width;
        $this->target_height = (int)$height;
        $constrain = strtolower($constrain);
        if (!in_array($constrain, array('width', 'height', 'inside', 'thumbnail'))) {
            $constrain = 'width';
        }
        $this->constrain = $constrain;
        if ($this->constrain == 'width' && empty($this->target_width)) {
            die("No width specified");
        }
        if ($this->constrain == 'height' && empty($this->target_height)) {
            die("No height specified");
        }

        if (empty($this->target_width) && !empty($this->target_height)) {
            $this->constrain = 'height';
        }
        if (empty($this->target_height) && !empty($this->target_width)) {
            $this->constrain = 'width';
        }

        if ($this->constrain == 'thumbnail') {
            $this->setCropping(true);
        }
        if ($this->constrain == 'inside') {
            $this->setPadding(true);
        }
    }


    static function staticResize($source_file,$target_file, $width, $height, $constrain = 'thumbnail') {
        $instance = new self($source_file);
        $instance->setTargetWidthAndHeight($width, $height, $constrain);
        $instance->setPadding(false);
        $instance->resize();
        $instance->saveFile($target_file);
    }

    static function staticRotate($source_file, $target_file, $degrees) {
        $instance = new self($source_file);
        $instance->rotate($degrees);
        $instance->saveFile($target_file);
    }

    function rotate($degrees) {
        $this->image_magic_object->rotateImage(new ImagickPixel('#00000000'),$degrees);
    }

    /*
     * Does 5 things in the following order
     * Zooms image
     * Resizes image
     * pads if necessary
     * crops if necessary
     * adds watermark
     *
     */
    function resize() {
        //bypass resizing, just do the cache stuff
        if (empty($this->target_width) && empty($this->target_height)) {
            return TRUE;
        }

        if ($this->enable_zoom) {
            $this->zoom();
        }
        $this->coreResize();


        //pad if required
        if ($this->pad_if_too_small) {
            if ($this->source_height < $this->target_height || $this->source_width < $this->target_width) {
                $this->pad();
            }
        }


        //crop if required
        if ($this->crop_if_too_big) {
            if ($this->source_height > $this->target_height || $this->source_width > $this->target_width) {
                $this->crop();
            }
        }

        if ($this->enable_watermark) {
            $this->watermark();
        }
    }


    protected function coreResize() {
        $dimensions = $this->getOptimalDimensions();
        $this->image_magic_object->scaleImage((int)$dimensions["optimal_width"], (int)$dimensions["optimal_height"], false);
        //$this->image_magic_object->scaleImage(200, 166, false);
        $this->setSourceWidthAndHeight();
    }

    public function getOptimalDimensions() {
        $target_width = min($this->target_width, $this->source_width);
        $target_height = min($this->target_height, $this->source_height);

        if (!$target_width) {
            $target_width = $this->source_width;
        }

        if (!$target_height) {
            $target_height = $this->source_height;
        }
        switch ($this->constrain) {
            case 'height':
                $optimal_height = $target_height;
                $optimal_width = $this->getWidthByFixedHeight($optimal_height);

                break;
            case 'thumbnail':
                $height_ratio = $this->source_height / $target_height;
                $width_ratio = $this->source_width / $target_width;
                if ($height_ratio < $width_ratio) {
                    $optimal_width = $this->source_width / $height_ratio;
                    $optimal_height = $target_height;

                } else {
                    $optimal_width = $target_width;
                    $optimal_height = $this->source_height / $width_ratio;
                }
                break;
            case 'inside':

                $height_ratio = $this->source_height / $target_height;
                $width_ratio = $this->source_width / $target_width;

                if ($height_ratio > $width_ratio) {
                    $optimal_height = $target_height;
                    $optimal_width = $this->source_width / $height_ratio;
                } else {
                    $optimal_width = $target_width;
                    $optimal_height = $this->source_height / $width_ratio;
                }
                break;
            case 'width':
            default:
                $optimal_width = $target_width;
                $optimal_height = $this->getHeightByFixedWidth($optimal_width);
                break;

        }
        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }

    private function getWidthByFixedHeight($height) {
        $ratio = $this->source_width / $this->source_height;
        $width = $height * $ratio;
        return $width;
    }

    private function getHeightByFixedWidth($width) {
        $ratio = $this->source_height / $this->source_width;
        $height = $width * $ratio;
        return $height;
    }

    public function crop() {

        if ($this->target_width) {
            $amount_x_to_crop = $this->source_width - $this->target_width;
            $crop_x_offset = self::getOffset($this->options["cropping"]["horizontal_alignment"], $amount_x_to_crop);
            $target_width2 = $this->target_width;
        } else {
            $target_width2 = $this->source_width;
            $crop_x_offset = 0;
        }

        if ($this->target_height) {
            $amount_y_to_crop = $this->source_height - $this->target_height;
            $crop_y_offset = self::getOffset($this->options["cropping"]["vertical_alignment"], $amount_y_to_crop);
            $target_height2 = $this->target_height;
        } else {
            $target_height2 = $this->source_height;
            $crop_y_offset = 0;
        }




        $this->image_magic_object->cropImage($target_width2, $target_height2, $crop_x_offset, $crop_y_offset);


        $this->setSourceWidthAndHeight();

    }

    public static function getOffset($alignment, $amount) {
        $alignment = strtolower(substr($alignment, 0, 1));
        switch ($alignment) {
            case 'b':
            case 'r':
                $offset = $amount;
                break;
            case 't':
            case 'l':
                $offset = 0;
                break;
            default:
                $offset = ceil($amount / 2);
                break;
        }

        return $offset;
    }

    public function pad() {
        //account for 0
        $target_width = max($this->target_width, $this->source_width);
        $target_height = max($this->target_height, $this->source_height);


        $background_color = ($this->options["padding"]["background_colour"] == 'transparent') ? new ImagickPixel('transparent') : $this->options["padding"]["background_colour"];


        //calculate offsets
        $horizontal_offset = self::getOffset($this->options["padding"]["horizontal_alignment"], $target_width - $this->source_width);
        $vertical_offset = self::getOffset($this->options["padding"]["vertical_alignment"], $target_height - $this->source_height);

        //prepare undelying pad image
        $pad_image = new Imagick();
        $pad_image->newImage($target_width, $target_height, $background_color, $this->file_extention);

        //join images
        $pad_image->compositeImage($this->image_magic_object, Imagick::COMPOSITE_DEFAULT, $horizontal_offset, $vertical_offset);

        $this->image_magic_object = $pad_image;
        $this->setSourceWidthAndHeight();
    }

    public function zoom() {
        if ($this->enable_zoom) {
            $percentage = (int)$this->options["zooming"]["percentage"];
            $percentage = min($percentage, 100);
            $percentage = max($percentage, 1);

            //max zoom check;
            $max_zoom_width = ($this->target_width / $this->source_width) * 100 ;
            $max_zoom_height = ($this->target_height / $this->source_height) * 100 ;
            $percentage = min($percentage,$max_zoom_width,$max_zoom_height);

            $target_width = $this->source_width * (1 - ($percentage / 100));
            $target_height = $this->source_height * (1 - ($percentage / 100));

            $crop_x_offset = ceil(($this->source_width - $target_width) / 2);
            $crop_y_offset = ceil(($this->source_height - $target_height) / 2);

            $this->image_magic_object->cropImage($target_width, $target_height,$crop_x_offset,$crop_y_offset);
            // $this->image_magic_object->cropThumbnailImage($target_width, $target_height);
            //$this->image_magic_object->cropImage(510, 425, 0, 0);
            $tmp_source_info = $this->image_magic_object->getImageGeometry();
            $this->image_magic_object->setImagePage(0,0,0,0);
            $this->setSourceWidthAndHeight();
        }
    }

    public function watermark() {
        if ($this->enable_watermark) {
            $watermark_object = new Imagick($this->options["watermarking"]["file_path"]);
            $watermark_object->setImageOpacity($this->options["watermarking"]["opacity"]);

            writeLog($this->options["watermarking"]["file_path"]);
            $watermark_max_width = floor($this->source_width * $this->options["watermarking"]["shrink_ratio"]);
            $watermark_max_height = floor($this->source_height * $this->options["watermarking"]["shrink_ratio"]);

            $watermark_width = $watermark_object->getImageWidth();
            $watermark_height = $watermark_object->getImageHeight();

            if (($watermark_width > $watermark_max_width) || ($watermark_height > $watermark_max_height)) {
                $watermark_object->scaleImage($watermark_max_width, $watermark_max_height, TRUE);
            }

            $final_watermark_width = $watermark_object->getImageWidth();
            $final_watermark_height = $watermark_object->getImageHeight();

            $horizontal_offset = self::getOffset($this->options["watermarking"]["align_horizontal"], $this->source_width - $final_watermark_width);
            $vertical_offset = self::getOffset($this->options["watermarking"]["align_vertical"], $this->source_height - $final_watermark_height);

            $this->image_magic_object->compositeImage($watermark_object, Imagick::COMPOSITE_ATOP, $horizontal_offset, $vertical_offset);
        }
    }

    public function saveFile($target_path = '') {
        if (!$target_path) {
            //overwrite the source file
            if ($this->target_path) {
                $target_path = $this->target_path;
            } else {
                $target_path = $this->filename;
            }
        }
        if (is_dir($target_path)) {
            $target_path = $target_path . "/" . $this->getTargetFileName();
        }
        $image_object = $this->image_magic_object;
        $image_object->setImageCompression(Imagick::COMPRESSION_JPEG); #Imagick::INTERLACE_PNG , Imagick::INTERLACE_GIF

        # Set compression level (1 lowest quality, 100 highest quality)
        $image_object->setImageCompressionQuality($this->compression_quality);

        # Strip out unneeded meta data
        $image_object->stripImage();
        #Writes resultant image to output directory

        if (is_file($target_path)) {
            unlink($target_path);
        }
        $image_object->writeImage($target_path);

        # Destroys Imagick object, freeing allocated resources in the process
        $image_object->destroy();
        $image_object->clear();
    }

    public function getTargetFileName() {

        $base_name = pathinfo($this->filename, PATHINFO_FILENAME);
        $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

        $filename_append = '_w' . $this->target_width . "_h" . $this->target_height . "_c" . substr($this->constrain, 0, 1);

        if ($this->enable_zoom) {
            $filename_append .= "_z" . $this->options["zooming"]["percentage"];
        }
        if ($this->pad_if_too_small) {
            $filename_append .= "_p" . $this->options["padding"]["horizontal_alignment"] . $this->options["padding"]["vertical_alignment"] . $this->options["padding"]["background_colour"];
        }

        if ($this->crop_if_too_big) {
            $filename_append .= "_c" . $this->options["cropping"]["horizontal_alignment"] . $this->options["cropping"]["vertical_alignment"];
        }
        if ($this->enable_watermark) {
            $filename_append .= "_m" . $this->options["watermarking"]["shrink_ratio"] . $this->options["watermarking"]["align_horizontal"] . $this->options["watermarking"]["align_vertical"] . $this->options["watermarking"]["opacity"];
        }

        return $base_name . $filename_append . '.' . $extension;
    }

}
