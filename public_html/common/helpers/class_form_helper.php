<?php
/**
 * Created by PhpStorm.
 * User: maggie
 * Date: 2/09/2016
 * Time: 4:02 PM
 */
class FormHelper {
    public static function optionList($name, $value, $selected_value = NULL) {
		$selected = in_array($value, $selected_value) ? ' selected="selected"' : '';
		return '<option ' . self::optionValue($value, '') . $selected . '>' .  htmlentities($name) . '</option>';
	}

	public static function option($name, $value, $selected_value = NULL) {
		return '<option ' . self::optionValue($value, $selected_value) . '>' .  htmlentities($name) . '</option>';
	}

	public static function optionValue($value, $selected_value) {
		$selected = ($value == $selected_value) ? ' selected="selected"' : '';
		return 'value="' . $value . '" ' . $selected;
	}

	public static function checkValue($value, $selected_value) {
		$checked = ($value == $selected_value) ? ' checked="checked"' : '';
		return 'value="' . $value . '" ' .$checked;
	}

	public static function checkInValues($value, $selected_values) {
		$checked = in_array($value, $selected_values) ? ' checked="checked"' : '';
		return 'value="' . $value . '" ' .$checked;
	}

	public static function checkInMultiValues($value, $selected_values) {
		$checked = FormHelper::in_array_r($value, $selected_values) ? ' checked="checked"' : '';
		return 'value="' . $value . '" ' .$checked;
	}

	public static function tickCheckbox($tick = 'n', $tick_value = 'y') {
		return ($tick == $tick_value) ? ' checked="checked"' : '';
	}

	public static function textValue($value) {
		$value = str_replace('"','&quot;',$value);
		return 'value="' . $value .'"';
	}

    public static function textareaValue($value) {
        $value = str_replace('</textarea>','',$value);
        return 'value="' . $value .'"';
    }

	public static function textInput($name,$value = '',$attributes = '') {
		$out = '<input type="text" name="'.$name.'" '.self::textValue($value). ' ' . $attributes . ' />';
		return $out;
	}

	private function in_array_r($needle, $haystack, $strict = false) {
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && FormHelper::in_array_r($needle, $item, $strict))) {
	            return true;
	        }
	    }
	    return false;
	}

    public static function isAjaxRequest()
    {
        return (strtolower(paramFromHash('HTTP_X_REQUESTED_WITH', $_SERVER)) == 'xmlhttprequest');
    }

}