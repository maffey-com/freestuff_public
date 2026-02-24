<?php
class FileHelper {
    public $object_id;
    public $object_type;
    public $directory;
    public $cache_directory;

    protected $_max_file_size;
    protected $_max_image_width;
    protected $_max_image_height;
    protected $_image_quality;
    protected $_enforce_image_dimension;    // if set to yes, resize image as it is being uploaded
    protected $_resize_mode;

    protected $_file_upload_tags;
    protected $_file_upload_categories;
    protected $_file_upload_misc;

    protected $_meta_info;

    protected $_upload_queue;
    protected $_upload_completed_list;
    protected $_upload_failed_list;

    const DS = "/";
    const DEFAULT_MAX_FILE_SIZE = 10000000;
    const DEFAULT_IMAGE = 'image_not_available.jpg';

    const DEFAULT_IMG_HEIGHT = 1000;
    const DEFAULT_IMG_WIDTH = 1000;
    const DEFAULT_IMG_QUALITY = 100;
    const DEFAULT_IMG_FULL_PATH = '';
    const DEFAULT_ENFORCE_IMAGE_DIMENSION = true;
    const DEFAULT_DISPLAY_IMAGE_URL = "display_image.php";
    const DEFAULT_DISPLAY_FILE_URL = "display_file.php";

    static $_ignore_files = array('.', '..');
    static $_image_types = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
    static $_allowable_file_types = array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'txt', 'pdf', 'xlsx', 'xls', 'csv', 'doc', 'docx');

    static $error_codes = array(
        UPLOAD_ERR_INI_SIZE => "File size is too big (set in html form)",
        UPLOAD_ERR_FORM_SIZE => "File only partially uploaded",
        UPLOAD_ERR_PARTIAL => "No file was uploaded",
        UPLOAD_ERR_NO_FILE => "No file was uploaded",
        UPLOAD_ERR_NO_TMP_DIR => "Temporary upload dir not available",
        UPLOAD_ERR_CANT_WRITE => "Could not write to disk",
        UPLOAD_ERR_EXTENSION => "File extension prevented upload"
    );

    private string $cache_url;
    private string $_display_file_url;
    private string $_display_image_url;

    /**
     *
     * Constructor
     * @param string $object_type
     * @param int $object_id
     */
    public function __construct($object_type, $object_id) {
        $this->object_type = self::sanitizeAlphanumeric($object_type);
        $this->object_id = trim($object_id??'');
        $this->directory = FILES_DIR . self::DS . $this->object_type . self::DS . $this->object_id;
        $this->cache_directory = CACHEDIR . self::DS . $this->object_type . self::DS . $this->object_id;
        $this->cache_url = CACHEURL . self::DS . $this->object_type . self::DS . $this->object_id;

        $this->_createObjectDirectory();
        $this->_loadMetaInfo();

        $this->_max_file_size = self::DEFAULT_MAX_FILE_SIZE;
        $this->_max_image_height = self::DEFAULT_IMG_HEIGHT;
        $this->_max_image_width = self::DEFAULT_IMG_WIDTH;
        $this->_image_quality = self::DEFAULT_IMG_QUALITY;
        $this->_enforce_image_dimension = self::DEFAULT_ENFORCE_IMAGE_DIMENSION;

        $this->_display_image_url = defined("DISPLAY_IMAGE_URL") ? DISPLAY_IMAGE_URL : self::DEFAULT_DISPLAY_IMAGE_URL;
        $this->_display_file_url = defined("DISPLAY_FILE_URL") ? DISPLAY_FILE_URL : self::DEFAULT_DISPLAY_FILE_URL;

        $this->resetUploadTags();
        $this->resetUploadCategories();
        $this->resetUploadMisc();
        $this->resetUploadQueue(true);
    }

    /**
     * Reset upload queue and completed list
     */
    public function resetUploadQueue($all_list = false) {
        $this->_upload_queue = array();

        if ($all_list) {
            $this->_upload_completed_list = array();
            $this->_upload_failed_list = array();
        }
    }

    /**
     *
     * Remove a tmp_name from the upload queue and shift it to the upload completed queue
     * @param string $tmp_name
     */
    protected function _markUploadAsCompleted($tmp_name) {
        unset($this->_upload_queue[$tmp_name]);
        $this->_upload_completed_list[] = $tmp_name;
    }

    /**
     *
     * Remove a tmp_name from the upload queue and shift it to the upload completed queue
     * @param string $tmp_name
     */
    protected function _markUploadAsFailed($tmp_name) {
        unset($this->_upload_queue[$tmp_name]);
        $this->_upload_failed_list[] = $tmp_name;
    }

    /**
     * Set max file size for upload
     * @param int $value
     */
    public function setMaxFileSize($value) {
        $this->_max_file_size = (int)$value;
    }

    /**
     * Set max image width for upload - (resizing)
     * @param int $value
     */
    public function setMaxImageWidth($value) {
        $this->_max_image_width = (int)$value;
    }

    /**
     * Set max image height for upload - (resizing)
     * @param int $value
     */
    public function setMaxImageHeight($value) {
        $this->_max_image_height = (int)$value;
    }

    /**
     * Set image quality for upload - (resizing)
     * @param int $value
     */
    public function setImageQuality($value) {
        $this->_image_quality = (int)$value;
    }

    /**
     *
     * Set whether to resize image by default
     * @param boolean $value
     */
    public function setEnforceImageDimension($value) {
        $this->_enforce_image_dimension = (boolean)$value;
    }

    /**
     * Clear all upload tags for future upload
     */
    public function resetUploadTags() {
        $this->_file_upload_tags = array('most_recent_upload');
    }

    /**
     * Clear all upload categories for future upload
     */
    public function resetUploadCategories() {
        $this->_file_upload_categories = array();
    }

    /**
     * Clear all upload categories for future upload
     */
    public function resetUploadMisc() {
        $this->_file_upload_misc = array();
    }

    /**
     *
     * Set tag for future upload
     * use addTagToFile() to update existing files
     * @param string $tag
     * @param boolean $reset_existing_tags
     */
    public function addTag($tag, $reset_existing_tags = false) {
        if ($reset_existing_tags) {
            $this->resetUploadTags();
        }
        if (!empty($tag)) {
            $this->_file_upload_tags[] = $tag;
        }
    }

    /**
     *
     * Set catgories for future upload
     * @param string $category
     * @param boolean $reset_existing_categories
     */
    public function addCategory($category, $reset_existing_categories = false) {
        if ($reset_existing_categories) {
            $this->resetUploadCategories();
        }
        $this->_file_upload_categories[] = $category;
    }

    /**
     *
     * Set misc attributes for future upload
     * @param string $category
     * @param boolean $reset_existing_categories
     */
    public function addMisc($attribute, $value, $reset_existing_misc = false) {
        if ($reset_existing_misc) {
            $this->resetUploadMisc();
        }
        $this->_file_upload_misc[$attribute] = $value;
    }

    /**
     * Replace all no alphanumeric character with a dash
     * @param $input
     */
    public static function sanitizeAlphanumeric($input) {
        $input = preg_replace('/[^a-zA-Z0-9]/', ' ', $input);
        return preg_replace('/(\s)+/', '_', $input);
    }

    /**
     * Get file extension exclude the '.'
     * @param string $filename
     */
    public static function getExtension($filename) {
        return strtolower(substr(strrchr($filename, '.'), 1));
    }

    /**
     * is image file
     */
    public static function isImageFile($extension) {
        return in_array($extension, self::$_image_types);
    }

    /**
     * Create object directory if it does not exists
     */
    protected function _createObjectDirectory() {
        if (!file_exists($this->directory)) {
            if (!mkdir($this->directory, 0777, true)) {
                return raiseError('Fail to load object directory: ' . $this->directory);
            }
        }
    }

    protected function _createCacheDirectory() {
        if (!file_exists($this->cache_directory)) {
            if (!mkdir($this->cache_directory, 0777, true)) {
                return raiseError('Fail to load object directory: ' . $this->cache_directory);
            }
        }
    }

    /**
     *
     * List all files in the directory of a given object
     * @param boolean $include_meta_file
     */
    public function listFiles($include_meta_file = false) {
        return $this->_getValidFiles(self::listAllFilesForDirectory($this->directory, $include_meta_file, $this->_meta_info));
    }

    /**
     * List all files in the directory
     * @param boolean $include_meta_file
     */
    public static function listAllFilesForDirectory($path, $include_meta_file = false, $meta_info = null) {
        $directory_files = array();
        if (!file_exists($path)) {
            return $directory_files;
        }

        $files = scandir($path);
        if (count($files)) {
            foreach ($files as $filename) {
                if (!in_array($filename, self::$_ignore_files)) {
                    if (($filename != '.meta') || ($include_meta_file)) {
                        $full_path = $path . self::DS . $filename;

                        if (!(is_dir($full_path))) {
                            if (is_null($meta_info)) {
                                $directory_files[] = $filename;
                            } else {
                                $sort_order = $meta_info->getSortOrder($filename);
                                if ($sort_order && !isset($directory_files[$sort_order])) {
                                    $directory_files[$sort_order] = $filename;
                                } else {
                                    $directory_files[] = $filename;
                                }

                            }

                        }
                    }
                }
            }
        }
        if (!is_null($meta_info)) {
            ksort($directory_files);
        }
        return $directory_files;
    }

    /**
     * Delete the entire object directory (original and cached)
     */
    public function delete() {
        $directories = array($this->directory, $this->cache_directory);

        foreach ($directories as $dir) {
            if (!self::deleteDirectory($dir)) {
                return false;
            }
        }
    }

    /**
     * Delete an entire directory
     * @param string $directory
     */
    public static function deleteDirectory($directory) {
        $directory_files = self::listAllFilesForDirectory($directory, true);

        foreach ($directory_files as $filename) {
            $full_path = $directory . self::DS . $filename;
            if (!unlink($full_path)) {
                return raiseError('Fail to remove file');
            }
        }

        if ((file_exists($directory)) && (!rmdir($directory))) {
            return raiseError('Fail to remove directory: "' . $directory . '"');
        }

        return true;
    }

    /**
     * Load meta info from .meta
     */
    protected function _loadMetaInfo() {
        $this->_meta_info = new MetaDataHelper($this->directory);
    }

    /**
     * full path to file for a given tag
     * @param string $tag
     */
    public function getFileNameFromTag($tag, $show_full_path = false) {
        $file = $this->_meta_info->getFileFromTag($tag);
        if ($file) {
            $full_path = $this->directory . self::DS . $file;

            if (file_exists($full_path)) {
                if ($show_full_path) {
                    return $full_path;
                } else {
                    return $file;
                }
            }
        }
        return false;

    }

    public function getFullPath($filename) {
        $full_path = $this->directory . self::DS . $filename;

        if (file_exists($full_path)) {
            return $full_path;
        }
    }


    /**
     * Rename a directory
     * @param string $object_type
     * @param int $object_id
     */
    public function renameDirectory($object_type, $object_id) {
        $object_type = self::sanitizeAlphanumeric($object_type);

        $target_path = FILES_DIR . self::DS . $object_type . self::DS . $object_id;

        if (file_exists($target_path)) {
            return raiseError('Target path already exists');
        }

        if (rename($this->directory, $target_path)) {
            $this->directory = $target_path;
            $this->_loadMetaInfo();
        }
    }

    /**
     * Copy all files and metadata contents from an existing fileHelper
     * @param string $target_object_type
     * @param int $target_object_id
     */
    public function cloneFileHelper($target_object_type, $target_object_id) {
        $target_file_helper = new FileHelper($target_object_type, $target_object_id);

        # if target directory already exist, rename existing directory with timestamp
        if (file_exists($target_file_helper->directory)) {
            $backup_object_id = $target_object_id . '_' . date('Y-m-d-Gis');

            $target_file_helper->renameDirectory($target_object_type, $backup_object_id);

            # create a directory for the target filehelper
            $target_file_helper = new FileHelper($target_object_type, $target_object_id);
        }

        $source_directory_files = self::listAllFilesForDirectory($this->directory, true);
        foreach ($source_directory_files as $file) {
            $source_file = $this->directory . self::DS . $file;
            $target_file = $target_file_helper->directory . self::DS . $file;

            if (!copy($source_file, $target_file)) {
                return raiseError('Fail to copy "' . $file . '" from source directory');
            }
        }
    }

    /**
     * Validate a single upload field
     * @param array|null $file
     * @param boolean $is_required
     */
    protected function _validateUpload($file, $is_required = true) {
        // check if file exists
        if (is_null($file)) {
            if ($is_required) {
                return raiseError(self::$error_codes[UPLOAD_ERR_NO_FILE]);
            }
            return;
        }

        // check for upload error code
        $upload_error_code = (int)$file['error'];
        if ($upload_error_code != UPLOAD_ERR_OK) {
            return raiseError(self::$error_codes[$upload_error_code]);
        }

        // check if file size is less than max allowable file size
        if ($file['size'] > $this->_max_file_size) {
            return raiseError(self::$error_codes[UPLOAD_ERR_INI_SIZE]);
        }

        // check that temporary upload name exists
        $tmp_file_name = $file['tmp_name'];

        if (empty($tmp_file_name)) {
            return raiseError(self::$error_codes[UPLOAD_ERR_PARTIAL]);
        }

        // check that the file was uploaded
        if (!is_uploaded_file($tmp_file_name)) {
            return raiseError(self::$error_codes[UPLOAD_ERR_PARTIAL]);
        }

        // check that the file extension is in the allowable file types
        $original_file_name = $file['name'];
        $extension = self::getExtension($original_file_name);

        if (!in_array($extension, self::$_allowable_file_types)) {
            return raiseError(self::$error_codes[UPLOAD_ERR_EXTENSION]);
        }

        $this->_upload_queue[$tmp_file_name] = $file;
    }

    /**
     *
     * Given the file field name, upload single/multiple file
     * @param string $input_field_name
     * @param boolean $is_required
     */
    public function setFileField($input_field_name, $is_required = true) {
        $files = isset($_FILES[$input_field_name]) ? $_FILES[$input_field_name] : null;

        if (is_null($files)) {
            $this->_validateUpload($files, $is_required);

        } elseif (is_array($files['name'])) {
            $total_upload = count($files['name']);
            $fields = array('name', 'type', 'tmp_name', 'error', 'size');

            for ($pos = 0; $pos < $total_upload; $pos++) {
                $file = array();
                foreach ($fields as $field) {
                    $file[$field] = $_FILES[$input_field_name][$field][$pos];
                }
                $this->_validateUpload($file, $is_required);
            }
        } else {
            $this->_validateUpload($files, $is_required);
        }
    }


    /**
     * go through the list of photos waiting to be uploaded and upload them one by one
     */
    public function processUploadQueue() {
        foreach ($this->_upload_queue as $tmp_name => $file) {
            $source_file_name = $file['name'];
            $target_file_name = self::getUniqueFileName($source_file_name, $this->directory);

            $source_path = $file['tmp_name'];
            $target_path = $this->directory . self::DS . $target_file_name;

            $result = move_uploaded_file($source_path, $target_path);

            if (($this->_enforce_image_dimension) && (self::isImageFile(self::getExtension($source_file_name)))) {
                ImageHelper::staticResize($target_path, $target_path, $this->_max_image_width, $this->_max_image_height, 'inside');
            }

            if (!$result) {
                $this->_markUploadAsFailed($tmp_name);
                raiseError('Fail to upload file: "' . $source_file_name . '"');
            } else {
                $this->_markUploadAsCompleted($tmp_name);
                $this->_setMetaData($target_file_name);
            }
        }
        $this->resetUploadQueue();
    }

    /**
     *
     * gets a file from somewhere PHP can read it, and saves it into file storage
     * @param string $source_path
     * @param boolean $delete_source
     */
    public function importFile($source_path, $delete_source = false) {
        //get file name from path
        $source_file_name = basename($source_path);
        $target_file_name = self::getUniqueFileName($source_file_name, $this->directory);

        $target_path = $this->directory . self::DS . $target_file_name;

        // copy file from source to target
        if (is_file($source_path)) {
            if (copy($source_path, $target_path)) {
                $this->_setMetaData($target_file_name);

                if ($delete_source) {
                    unlink($source_path);
                }
            }
        }

    }

    /**
     *
     * import files from a directory, set meta info based on the importDirectory caller.
     * @param string $source_path
     */
    public function importDirectory($source_path) {
        if (!file_exists($source_path)) {
            return raiseError("Import directory does not exists.");
        }

        $source_files = self::listAllFilesForDirectory($source_path);

        foreach ($source_files as $file) {
            if (!in_array($file, array('.meta'))) {
                $this->importFile($source_path . self::DS . $file);
            }
        }
    }

    /**
     * Create a file from raw binary or ascii data
     * @param $data
     * @param string $filename
     */
    public function importData($data, $filename = 'file.dat') {
        $target_file_name = self::getUniqueFileName($filename, $this->directory);

        $target_path = $this->directory . self::DS . $target_file_name;

        // copy file from source to target
        file_put_contents($target_path, $data);
        $this->_setMetaData($target_file_name);

    }

    /**
     *
     * Update metadata info for a single file
     * @param string $filename
     */
    public function _setMetaData($filename) {
        $this->_meta_info->addFile($filename);

        // set tags for file
        foreach ($this->_file_upload_tags as $tag) {
            $this->_meta_info->addTag($filename, $tag);
        }

        // set categories for file
        foreach ($this->_file_upload_categories as $category) {
            $this->_meta_info->addCategory($filename, $category);
        }

        // set misc attributes for file
        foreach ($this->_file_upload_misc as $attribute => $value) {
            $this->_meta_info->setMisc($filename, $attribute, $value);
        }

        $next_sort_order = $this->_meta_info->getNextSortOrder();
        $this->setFileSortOrder($filename, $next_sort_order);
    }


    public function setFileSortOrder($filename, $new_order) {
        $new_order = (int)$new_order;
        $this->_meta_info->setSortOrder($filename, $new_order);
    }

    public function getFileSortOrder($filename) {
        return $this->_meta_info->getSortOrder($filename);
    }

    /**
     *
     * Get basename from filename
     * @param string $filename
     */
    public static function getBaseName($filename) {
        return strtolower(substr($filename, 0, strrpos($filename, '.')));
    }

    /**
     * Clean and generate unique filename
     * @param string $string
     * @param string $directory
     */
    public static function getUniqueFileName($string, $directory) {
        $extension = self::getExtension($string);
        $base_name = self::getBaseName($string);

        $base_name = self::sanitizeAlphanumeric($base_name);

        $base_name = empty($base_name) ? 'temp' : $base_name;
        $rtn_value = $base_name . '.' . $extension;
        $i = 1;

        while (file_exists($directory . self::DS . $rtn_value)) {
            $rtn_value = $base_name . '_' . $i . '.' . $extension;
            $i++;
        }
        return $rtn_value;
    }

    /**
     * Return a list of images
     */
    public function getAllImages($show_full_path = false) {
        static $all_images;

        if (is_null($all_images)) {
            $metadata_files = $this->_meta_info->getAllImages();
            $directory_files = array();
            foreach ($metadata_files as $filename) {
                $sort_order = $this->_meta_info->getSortOrder($filename);
                if ($sort_order && !isset($directory_files[$sort_order])) {
                    $directory_files[$sort_order] = $filename;
                } else {
                    $directory_files[] = $filename;
                }
            }
            if (!is_null($this->_meta_info)) {
                ksort($directory_files);
            }

            $all_images = $this->_getValidFiles($directory_files, $show_full_path);
        }


        return $all_images;
    }

    /**
     * Return a list of files in the given category
     * @param string $category
     */
    public function getFilesFromCategory($category, $show_full_path = false) {
        static $rtn_value;

        if ((!is_array($rtn_value)) || (!array_key_exists($category, $rtn_value))) {
            $rtn_value[$category] = array();

            $metadata_files = $this->_meta_info->getFilesFromCategory($category);
            $rtn_value[$category] = $this->_getValidFiles($metadata_files, $show_full_path);
        }
        return $rtn_value[$category];
    }

    /**
     * Loop through a list of filenames, return an array of filenames that exists in the directory
     * @param array $metadata_files
     */
    protected function _getValidFiles($metadata_files, $show_full_path = false) {
        $rtn_value = array();

        foreach ($metadata_files as $file) {
            $full_path = $this->directory . self::DS . $file;

            if (file_exists($full_path)) {

                if ($show_full_path) {
                    $rtn_value[$file] = $full_path;

                } else {
                    $rtn_value[$file] = $file;
                }
            }
        }

        return $rtn_value;
    }

    /**
     * Delete file with a given name
     * @param string $filename
     */
    public function deleteFile($filename) {
        if (empty($filename)) {
            return raiseError('Invalid file name');
        }

        if (in_array($filename, self::$_ignore_files)) {
            return raiseError('You do not have permission to delete this file');
        }

        if (!empty($filename)) {
            $full_path = $this->directory . self::DS . $filename;

            if (file_exists($full_path)) {
                if (unlink($full_path)) {
                    $this->_meta_info->deleteFile($filename);
                }
            }
        }
    }

    public function showMeta() {
        $this->_meta_info->display();
    }

    /**
     * Add a tag to the file associated to a file
     * @param $file
     * @param $tag
     */
    public function addTagToFile($filename, $tag) {
        $this->_meta_info->addTag($filename, $tag);
    }


    /**
     * Add the file  to a category
     * @param string $filename
     * @param string $category
     */
    public function addCategoryToFile($filename, $category) {
        $this->_meta_info->addCategory($filename, $category);
    }

    public function getOriginalImagePathFromTag($tag) {
        $filename = $this->_meta_info->getFileFromTag($tag);

        if (empty($filename)) {
            return '';
        } else {
            $source_path = $this->directory . self::DS . $filename;
        }

        if (file_exists($source_path)) {
            $target_path = $this->cache_directory . self::DS . $filename;

            if (!file_exists($target_path)) {
                copy($source_path, $target_path);
            }
            return $this->cache_url . self::DS . $filename;
        } else {
            return '';
        }
    }

    /**
     * REmove the file  from a category
     * @param string $filename
     * @param string $category
     */
    public function removeCategoryFromFile($filename, $category) {
        $this->_meta_info->removeFileFromCategory($filename, $category);
    }

    /**
     * Returns a cached image url
     * @param $image_helper
     *
     */

    public function cacheImageFromImageHelper($image_helper) {
        $target_path = $this->cache_directory . self::DS . $image_helper->getTargetFileName();

        if (file_exists($target_path)) {
            if (filectime($image_helper->filename) > filectime($target_path)) {
                unlink($target_path);
            }
        }
        if (!file_exists($target_path)) {
            $this->_createCacheDirectory();
            $image_helper->resize();
            $image_helper->saveFile($target_path);
        }
        return $this->cache_url . self::DS . $image_helper->getTargetFileName();
    }

    /**
     * given file name return file location
     * @param string $tag
     * @param string $tag
     * @param int $width
     * @param int $height
     * @param string [e|r|c] $mode
     */
    public function getImagePathFromFilename($filename, $width = null, $height = null, $constrain = 'thumbnail', $use_default_image = true) {

        $width = (int)$width;
        $height = (int)$height;

        $width = empty($width) ? self::DEFAULT_IMG_WIDTH : $width;
        $height = empty($height) ? self::DEFAULT_IMG_HEIGHT : $height;

//        $zoom = true;

        if (empty($filename)) {
            if ($use_default_image) {
                $filename = self::DEFAULT_IMAGE;
                $source_path = DOCROOT . '/front_end/img/' . self::DEFAULT_IMAGE;
//                $zoom = false;
            } else {
                return '';
            }
        } else {
            $source_path = $this->directory . self::DS . $filename;
        }


        $ih = new ImageHelper($source_path);
//        $ih->setZoom($zoom);

        $ih->setTargetWidthAndHeight($width, $height, $constrain);
        $ih->setPadding(true, 'WHITE');
        return $this->cacheImageFromImageHelper($ih);


    }

    public static function moveTemporaryUploadedFile($type, $id, $new_type, $new_id) {
        $temp_fh = new FileHelper($type, $id);
        writeLog($temp_fh->getFileNameFromTag('most_recent_upload'));
        writeLog($temp_fh->getFullPath($temp_fh->getFileNameFromTag('most_recent_upload')));
        $filename = $temp_fh->getFullPath($temp_fh->getFileNameFromTag('most_recent_upload'));

        if (!empty($filename)) {
            $new_fh = new FileHelper($new_type, $new_id);

            $new_fh->addTag('most_recent_upload');
            $new_fh->importFile($filename);
            $new_fh->deleteUntaggedFiles();
            $new_fh->clearCache();
        }
    }

    /**
     * given a tag return file location
     * @param string $tag
     * @param string $tag
     * @param int $width
     * @param int $height
     * @param mode [e|r|c] $mode
     */
    public function getImagePathFromTag($tag, $width = null, $height = null, $constrain = 'thumbnail', $use_default_image = true) {
        $filename = $this->getFileNameFromTag($tag);
        return $this->getImagePathFromFilename($filename, $width, $height, $constrain, $use_default_image);
    }

    /**
     * Giving file code, return a get_image.php that returns the image
     * @param string $tag
     * @param int $width
     * @param int $height
     * @param strin [r|e] $use_exact_mode
     */
    public function imageLinkFromFilename($filename, $width, $height, $constrain = 'width') {
        $width = ($width);
        $height = ($height);

        $params = array();
        $params['t'] = $this->object_type;
        $params['id'] = $this->object_id;
        $params['f'] = $filename;
        $params['w'] = empty($width) ? self::DEFAULT_IMG_WIDTH : $width;
        $params['h'] = empty($height) ? self::DEFAULT_IMG_HEIGHT : $height;
        $params['c'] = $constrain;

        return SITE_URL . self::DS . $this->_display_image_url . '?' . http_build_query($params);
    }

    public function imageLinkFromTag($tag, $width, $height, $constrain = 'width') {
        $filename = $this->getFileNameFromTag($tag);
        return $this->imageLinkFromFilename($filename, $width, $height, $constrain);

    }

    public function getImageHelperFromFilename($filename, $use_default_image = true) {

        if (empty($filename)) {
            if ($use_default_image) {
                $filename = self::DEFAULT_IMAGE;
                $source_path = DOCROOT . '/front_end/img/' . self::DEFAULT_IMAGE;
            } else {
                return '';
            }
        } else {
            $source_path = $this->directory . self::DS . $filename;
        }


        $ih = new ImageHelper($source_path);
        $ih->target_path = $this->cache_directory;

        return $ih;
    }

    public function getImageHelperFromTag($tag, $use_default_image = true) {
        $filename = $this->getFileNameFromTag($tag);
        return $this->getImageHelperFromFilename($filename, $use_default_image);
    }

    public function getLinkFromFilename($filename) {
        $params = array();
        $params['t'] = $this->object_type;
        $params['id'] = $this->object_id;
        $params['f'] = $filename;

        return SITE_URL . self::DS . $this->_display_file_url . '?' . http_build_query($params);
    }

    public function getLinkFromTag($tag) {
        $filename = $this->getFileNameFromTag($tag);
        if ($filename) {
            return $this->getLinkFromFilename($filename);
        } else {
            return false;
        }
    }

    /**
     * Get all the tags that is associated to the file_name
     * @param string $filename
     */
    public function getTagsForFilename($filename) {
        return $this->_meta_info->getTagsForFile($filename);
    }

    public function getMiscFromFilename($filename, $attribute) {
        return $this->_meta_info->getMisc($filename, $attribute);
    }

    public function getMiscFromTag($tag, $attribute) {
        $filename = $this->getFileNameFromTag($tag);
        return $this->_meta_info->getMisc($filename, $attribute);
    }

    public function setMiscForFilename($filename, $attribute, $value) {
        $this->_meta_info->setMisc($filename, $attribute, $value);
    }

    /**
     * Get all the categories that is associated to the file file_name
     * @param string $filename
     */
    public function getCategoriesForFilename($filename) {
        return $this->_meta_info->getCategoriesForFile($filename);
    }

    public function getFileUploadedDate($filename) {
        return date('Y-m-d H:i:s', filectime($this->directory . self::DS . $filename));
    }

    /**
     * return all categories and file names in file storage
     */
    public function listAllCategories() {
        return $this->_meta_info->data->categories;
    }

    public function listAllTags() {
        return $this->_meta_info->data->tags;
    }

    public function removeTag($tag) {
        $this->_meta_info->removeTag($tag);
    }


    public function removeCategory($category, $auto_save = false) {
        $this->_meta_info->removeCategory($category, $auto_save);

        var_dump($this->_meta_info->data);
    }

    public function deleteUntaggedFiles() {
        $files = self::listAllFilesForDirectory($this->directory);
        foreach ($files as $filename) {
            if (!$this->_meta_info->getTagsForFile($filename) && !$this->_meta_info->getCategoriesForFile($filename)) {
                $full_path = $this->directory . self::DS . $filename;

                if (!unlink($full_path)) {
                    return raiseError('Fail to remove file');
                }
            }

        }
    }

    public static function issueTemporaryFileId() {
        if (!file_exists(FILES_DIR . "/temporary_file_id.txt")) {
            file_put_contents(FILES_DIR . "/temporary_file_id.txt", "0");
        }
        $fp = fopen(FILES_DIR . "/temporary_file_id.txt", "r+");
        flock($fp, LOCK_EX);
        $number = preg_replace('/[^\d\s]/', '', fgets($fp));
        $number++;

        ftruncate($fp, 0);
        rewind($fp);
        fputs($fp, $number);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $number;
    }

    static function copyFiles($old_object_id, $old_object_type, $new_object_id, $new_object_type) {

        $new_dir = FILES_DIR . "/" . $new_object_type . "/" . $new_object_id . "/";
        FileHelper::createDirectories($new_dir);

        $old_dir = FILES_DIR . "/" . $old_object_type . "/" . $old_object_id . "/";

        if (is_dir($old_dir)) {
            $files = scandir($old_dir);
            foreach ($files as $v) {
                if (!in_array($v, array('.', '..', '.DS_Store'))) {
                    if (file_exists($old_dir . "/" . $v)) {
                        copy($old_dir . "/" . $v, $new_dir . "/" . $v);
                    }
                }
            }
        }
    }

    static private function createDirectories($dir) {

        //this function ensures required directories exist before we try to write to them
        if (substr($dir, -1) == "/") {
            $dir = substr($dir, 0, -1);
        }

        $dir_array = explode("/", $dir);
        $dirname = "";
        foreach ($dir_array as $piece) {
            if (!$piece) {
                continue;
            }
            $dirname .= "/";

            $dirname .= $piece;

            if (!is_dir($dirname)) {
                writeLog($dirname);
                mkdir($dirname);
            }
        }
    }

    public function clearCache() {
        self::deleteDirectory($this->cache_directory);
    }

    public function rotate($filename) {
        $path = $this->getFullPath($filename);
        writeLog($path);
        ImageHelper::staticRotate($path, $path, 90);
    }

    public function getFirstPageOfPDFAsJpeg($filename) {
        $this->_createCacheDirectory();
        $path = $this->getFullPath($filename);
        $extension = $this->getExtension($filename);
        writeLog($extension);
        if ($extension != "pdf") {
            return false;
        }
        $target_path = CACHE_DIR . self::DS . $filename . ".jpg";
        $im = new imagick($path . '[0]');
        $im->setImageCompression(Imagick::COMPRESSION_JPEG); #Imagick::INTERLACE_PNG , Imagick::INTERLACE_GIF
        $im->setImageCompressionQuality(90);
        $im->stripImage();
        $im->writeImage($target_path);
        $im->destroy();
        $im->clear();
        return CACHE_URL . self::DS . $filename . ".jpg";
    }


}

