<?php
class MetaDataHelper {
	protected $_directory;
	protected $_meta_file;

	public $data;

	protected $_data_types = array('tags', 'categories', 'images', 'sort_order', 'misc');

	public function __construct($directory) {
		$this->_directory = $directory;
		$this->_meta_file = $this->_directory . FileHelper::DS . '.meta';

        $this->data = new StdClass;
		if (!file_exists($this->_meta_file)) {
			$this->_initializeData();
		}

		$this->_loadData();
	}

	/**
	 * return the next sort order
	 */
	public function getNextSortOrder() {
		$key = array_keys($this->data->sort_order);
		if (count($key)) {
			return max($key)+1;
		} else {
			return 1;
		}
	}

	/**
	 * Create a fresh copy of .meta
	 */
	protected function _initializeData() {
		foreach ($this->_data_types as $type) {
			$this->data->$type = array();
		}

		$this->_saveMeta();
	}

	/**
	 * Load .meta data into object
	 */
	protected function _loadData() {
		$tmp = unserialize(file_get_contents($this->_meta_file));

		foreach ($tmp as $type => $info) {
			$this->data->$type = $info;
		}

		// handle new data types
		foreach ($this->_data_types as $type) {
			if (!isset($this->data->$type)) {
				$this->data->$type = array();
				$this->_saveMeta();
			}
		}
	}

	protected function _saveMeta() {
		return file_put_contents($this->_meta_file, serialize($this->data));
	}

	public function display() {
		echo '<pre>';
			print_r($this);
		echo '</pre>';
	}



	/*
	 * function calculates 32-digit hexadecimal md5 hash of some random data
	 */
	public static function generateRandomString($len = 4) {
    	return strtoupper(substr(md5(rand().rand()), 0, $len));
	}

	/**
	 * Clear all meta info related to a given file_name
	 * @param string $file_name
	 */
	public function deleteFile($file_name) {
		// remove tags for filename,
		foreach ($this->data->tags as $tag => &$tmp_name) {
			if ($file_name == $tmp_name) {
				unset($this->data->tags[$tag]);
			}
		}

		// remove file from categories
		foreach ($this->data->categories as $category => &$files) {
			if ($key = array_search($file_name, $files)) {
				unset($this->data->categories[$category][$key]);
			}
		}


		// remove sort order
		$key = array_search($file_name, $this->data->sort_order);
		unset($this->data->sort_order[$key]);

		// remove image
		$this->removeImage($file_name);

		$this->_saveMeta();
	}

	/**
	 * generate unique file_name for file
	 * only create a new file_name if the file doesn't already exists
	 * @param string $file_name
	 */
	public function addFile($file_name) {
		if (!self::hasEmptyValues($file_name)) {
            $file_extension = FileHelper::getExtension($file_name);
            if (in_array($file_extension, FileHelper::$_image_types)) {
                $this->addImage($file_name);
            }
            $this->_saveMeta();
		}
	}

	/**
	 * create a tag for file
	 * @param string $file_name
	 * @param string $tag
	 */
	public function addTag($file_name, $tag) {
		if (!self::hasEmptyValues($file_name, $tag)) {
			$this->data->tags[$tag] = $file_name;
			$this->_saveMeta();
		}
	}

	/**
	 * Remove a tag
	 * @param string $tag
	 */
	public function removeTag($tag) {
		if (array_key_exists($tag, $this->data->tags)) {
			unset($this->data->tags[$tag]);
		}
		$this->_saveMeta();
	}

	/**
	 * Remove a category
	 * @param string $tag
	 */
	public function removeCategory($category, $auto_save = false) {
		if (array_key_exists($category, $this->data->categories)) {
			unset($this->data->categories[$category]);
		}

		if ($auto_save) {
			$this->_saveMeta();
		}
	}



	/**
	 * Get file for a given tag
	 * @param string $tag
	 */
	public function getFileFromTag($tag) {
		if (array_key_exists($tag, $this->data->tags)) {
			return $this->data->tags[$tag];
		}
		return false;
	}

	/**
	 * Add file to a cateogry
	 * @param string $file_name
	 * @param string $category
	 */
	public function addCategory($file_name, $category) {
		if (!self::hasEmptyValues($file_name, $category)) {
			if (!array_key_exists($category, $this->data->categories)) {
				$this->data->categories[$category] = array();
			}

			if (!in_array($file_name, $this->data->categories[$category])) {
				$this->data->categories[$category][] = $file_name;
			}

			$this->_saveMeta();
		}
	}

	/**
	 * Add file to a cateogry
	 * @param string $file_name
	 * @param string $misc
	 */
	public function setMisc($file_name, $attribute,$value) {
		if (!self::hasEmptyValues($file_name, $attribute)) {
            if (empty($this->data->misc[$file_name])) {
                $this->data->misc[$file_name] = array();
            }
			$this->data->misc[$file_name][$attribute] = $value;
			$this->_saveMeta();
		}
	}

    public function getMisc($file_name,$attribute) {
        if (isset($this->data->misc[$file_name]) && is_array($this->data->misc[$file_name])) {
            if (isset($this->data->misc[$file_name][$attribute])) {
                return $this->data->misc[$file_name][$attribute];
            }
        }
    }

	/**
	 * Check if any of the value in the given array is empty
	 */
	public static function hasEmptyValues() {
		$values = func_get_args();

		foreach ($values as $value) {
			if (empty($value)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Add image file
	 * @param string $file_name
	 */
	public function addImage($file_name) {
		if (!self::hasEmptyValues($file_name)) {
			if (!in_array($file_name, $this->data->images)) {
				$this->data->images[] = $file_name;

				$this->_saveMeta();
			}
		}
	}

	public function removeImage($file_name) {
		if ($key = array_search($file_name, $this->data->images)) {
			unset($this->data->images[$key]);

			$this->_saveMeta();
		}
	}

	/**
	 * Remove a file from a category
	 * @param string $file_name
	 * @param string $category
	 */
	public function removeFileFromCategory($file_name, $category) {
		if (isset($this->data->categories[$category])) {
			$key = array_search($file_name, $this->data->categories[$category]);
			if (isset($key)) {
				unset($this->data->categories[$category][$key]);
				$this->_saveMeta();
			}
		}
	}


	/**
	 * Get a list of files in a given category
	 * @param string $category
	 */
	public function getFilesFromCategory($category) {
		if (array_key_exists($category, $this->data->categories)) {
			return $this->data->categories[$category];
		}
		return array();
	}

	/**
	 * get a list of all images
	 */
	public function getAllImages() {
		return $this->data->images;
	}

	/**
	 * Return an array of tags the file is linked to
	 */
	public function getTagsForFile($file_name) {
		$rtn_value = array();

		foreach ($this->data->tags as $tag => $tmp_name) {
			if ($file_name == $tmp_name) {
				$rtn_value[] = $tag;
			}
		}
		return $rtn_value;
	}


	/**
	 *
	 */
	public function getCategoriesForFile($file_name) {
		$rtn_value = array();

		foreach ($this->data->categories as $category => $files) {
			if (array_search($file_name, $files) !== false) {
				$rtn_value[] = $category;
			}
		}
		return $rtn_value;
	}

	public function setSortOrder($new_file_name, $new_index) {
		$new_index = (int)$new_index;

        $out_array = array();
        $i = 1;
        $inserted = false;
        foreach ($this->data->sort_order as $index => $file_name) {
            if ($file_name == $new_file_name) {
                continue;
            }
            if ($i == $new_index) {
                $out_array[$i] = $new_file_name;
                $i++;
                $inserted = true;
            }

            $out_array[$i] = $file_name;
            $i++;
        }

        if (!$inserted) {
            $out_array[$i] = $new_file_name;
        }
        $this->data->sort_order = $out_array;
        $this->_saveMeta();

	}

	public function getSortOrder($file_name) {
		return array_search($file_name, $this->data->sort_order);
	}

}