<?php
class CRModel {
	protected $_table_name = '';
	protected $_primary_key = '';
	protected $_field_hints = array();
	public static $boolean_yes_values = array('y', '1','on','yes');

	/**
	 * Check required fields
	 */
	protected function _validateRequiredField($field, $label) {
		if (empty($this->$field)) {
			raiseError($label . ' cannot be blank.', $field);
		}
	}

	public static function listColumnsX($table) {
		$result = runQuery("describe " . $table);
		while ($row = fetchSQL($result)) {
			echo 'public $' . $row['Field'] . ";<br />";
			#echo "'" . $row['Field'] . "', ";
		}
		die();
	}

	private function _setHint($arr_fields, $type) {
		foreach ($arr_fields as $field) {
			$this->_field_hints[$field] = $type;
		}
	}

	protected static function _prepare_field_value(&$in_str, $dictionary) {
		if (is_array($dictionary)) {
			foreach ($dictionary as $symbol => $text_str) {
				$in_str = str_replace($symbol, $text_str, $in_str);
			}
		}
	}

	protected function _setHintBoolean() {
		$arg_list = func_get_args();
		$this->_setHint($arg_list, 'boolean');
	}

	protected function _setHintDate($fields) {
		$arg_list = func_get_args();
		$this->_setHint($arg_list, 'date');
	}

	protected function _setHintNumeric($fields) {
		$arg_list = func_get_args();
		$this->_setHint($arg_list, 'numeric');
	}

	protected function _setHintFloat($fields) {
		$arg_list = func_get_args();
		$this->_setHint($arg_list, 'float');
	}

	protected function _setHintInt($fields) {
		$arg_list = func_get_args();
		$this->_setHint($arg_list, 'int');
	}

	protected function _retrieveRecord(array $where, $table_name = '') {
		$table_name = empty($table_name) ? $this->_table_name : $table_name;
		$delimiter = ' WHERE ';

		$sql = "SELECT *
				FROM " . $table_name;
		foreach ($where as $field_name => $value) {
			$sql .= $this->_sqlValueHelper($field_name, $value, $delimiter);
			$delimiter = ' AND ';
		}
		$row = runQueryGetFirstRow($sql);
		if ($row) {
			$this->_populateFromArray($row);
		}
	}

	protected function _deleteRecord(array $where, $table_name = '') {
		$table_name = empty($table_name) ? $this->_table_name : $table_name;
		$delimiter = ' WHERE ';

		$sql = "DELETE
				FROM " . $table_name;
		foreach ($where as $field_name => $value) {
			$sql .= $this->_sqlValueHelper($field_name, $value, $delimiter);
			$delimiter = ' AND ';
		}
		runQuery($sql);
	}


	protected function _sqlValueHelper($field_name, $value, $delimiter = '') {
		$field_hint_type = isset($this->_field_hints[$field_name]) ? $this->_field_hints[$field_name] : '';
		$output = '';

		switch ($field_hint_type) {
			case 'boolean':
				$output = $delimiter . $field_name . ' = ' . quoteSQL(yn($value));
				break;

			case 'date':
				$output = $delimiter . $field_name . ' = ' . DateHelper::db($value);
				break;

			case 'float':
				$output = $delimiter . $field_name . ' = ' . (float)$value;
				break;

			case 'int':
				$output = $delimiter . $field_name . ' = ' . (int)$value;
				break;

			case 'numeric':
			default:
				$output = $delimiter . $field_name . ' = ' . quoteSQL($value);
				break;
		}
		return $output;
	}

	/**
	 * Check if field value is within list
	 * @param string $field
	 * @param string $label
	 * @param array $value_list
	 */
	protected function _validateValueInList($field, $label, $value_list) {
		if (!in_array($this->$field, $value_list)) {
			raiseError('Invalid ' . $label . '.', $field);
		}
	}

	/**
	 * Check if field is an integer
	 * @param string $field
	 * @param string $label
	 */
	protected function _validateIntField($field, $label) {
		if (preg_match('/[^0-9]/', $this->$field??'')) {
			raiseError($label . ' requires an integer value.', $field);
		}
	}

	/**
	 * Check if field is an float
	 * @param string $field
	 * @param string $label
	 */
	protected function _validateFloatField($field, $label) {
		if (preg_match('/[^0-9\.]/', $this->$field)) {
			raiseError($label . ' requires a decimal value.', $field);
		}
	}

	/**
	 * Check if field is numeric
	 * @param string $field
	 * @param string $label
	 */
	protected function _validateNumericField($field, $label) {
		if (preg_match('/[^0-9\.\-]/', $this->$field)) {
			raiseError($label . ' requires a numeric value.', $field);
		}
	}

	protected function _validateEmail($field, $label) {
		if (!filter_var($this->$field, FILTER_VALIDATE_EMAIL)) {
			raiseError('Invalid ' . $label . '.', $field);
		}
	}

	protected function _validateAlphanumeric($field, $label) {
		# clean username
		$pattern  = '/\W/';

		if (preg_match($pattern, $this->$field)) {
			raiseError($label . ' must be alphanumeric or underscore.', $field);
		}
	}

	/**
	 * set object field value from array (only set those field that exists in the object)
	 * @param array $value_list
	 */
	protected function _populateFromArray($value_list) {
		$object_vars = get_object_vars($this);

		foreach ($value_list as $field => $value) {
			if (array_key_exists($field, $object_vars)) {

				if (is_string($value)) {
					$value = trim($value);
				}

				$this->$field = $value;

				if (array_key_exists($field, $this->_field_hints) && ('boolean' == $this->_field_hints[$field])) {
					$this->$field = (in_array(strtolower($value), self::$boolean_yes_values)) ? true : false;
				}
			}
		}
	}

	public function save() {
		$tmp = $this->{$this->_primary_key};
		//$insert_primary_key_values = array('', 'new');
		if (!$tmp) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}

	protected function _sqlSetHelper() {
		$arg_list = func_get_args();
		$output = '';
		$delimiter = '';

		for ($i = 0; $i < func_num_args(); $i++) {
			$field_name = $arg_list[$i];

			$output .= $this->_sqlValueHelper($field_name, $this->$field_name, $delimiter);
			$delimiter = ', ';
		}
		return $output;
	}

	protected function _sqlSetHelperInt() {
		$arg_list = func_get_args();
		$bar = "";
		$delimiter = '';
		for ($i = 0; $i < func_num_args(); $i++) {
			$field_name = $arg_list[$i];
			$value = (int)$this->$field_name;
			$bar .= $delimiter . $field_name . ' = ' . quoteSQL($value, false);
			$delimiter = ', ';
		}

		return $bar;
	}

	protected function _sqlSetHelperDate() {
		$arg_list = func_get_args();
		$bar = "";
		$delimiter = '';
		for ($i = 0; $i < func_num_args(); $i++) {
			$field_name = $arg_list[$i];
			$bar .= $delimiter . $field_name . ' = ' . DateHelper::db($this->$field_name);
			$delimiter = ', ';
		}

		return $bar;
	}

    /**
     * @param $id
     *
     * @return static
     */
	public static function instanceFromId($id) {
        $class = get_called_class();

        $instance = new $class();
        $instance->retrieveFromId($id);
        return $instance;
    }
}
