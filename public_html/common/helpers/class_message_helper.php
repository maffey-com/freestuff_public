<?php
class MessageHelper {
	protected static function _setSessionMessage($type, $msg) {
		$msg = trim($msg);
		
		if ($msg) {
			$_SESSION[$type] = $msg;
		}
	}
	
	protected static function _getSessionMessage($type) {
		return paramFromHash($type, $_SESSION);
	}
	
	protected static function _unsetSessionMessage($type) {
		if (isset($_SESSION[$type])) {
			unset($_SESSION[$type]);
		}
	}
	
	public static function setSessionSuccessMessage($msg, $insert_as_paragraph = TRUE) {
		if ($insert_as_paragraph) {
			$msg = '<p>' . $msg . '</p>';
		}
		self::_setSessionMessage('success_msg', $msg);
	}
	
	public static function unsetSessionSuccessMessage() {
		self::_unsetSessionMessage('success_msg');
	}
	
	public static function getSessionSuccessMessage() {
		return self::_getSessionMessage('success_msg');
	}
	
	public static function setSessionErrorMessage($msg, $insert_as_paragraph = TRUE) {
		if ($insert_as_paragraph) {
			$msg = '<p>' . $msg . '</p>';
		}
		self::_setSessionMessage('error_msg', $msg);
	}
	
	public static function unsetSessionErrorMessage() {
		self::_unsetSessionMessage('error_msg');
	}
	
	public static function getSessionErrorMessage() {
		return self::_getSessionMessage('error_msg');
	}
}