<?php
class ErrHelper {
    public $message;
    public $code;
    public $field;
    public $data;
    public $location;

    static $errors = array();

    const DEFAULT_ERROR_CODE = 99;

    protected function __construct($message, $code, $field, $data) {
        if (!is_numeric($code)) {
            throw new Exception("Err mas have a numeric code");
        }
        $this->message = $message;
        $this->code = $code;
        $this->field = $field;
        $this->data = $data;
    }

    public static function raise($message = false, $code = self::DEFAULT_ERROR_CODE, $field = false, $data = array()) {
        $error = new ErrHelper($message, $code, $field, $data);

        if (DEVEL) {
            $backtrace = debug_backtrace();
            $error->location = $backtrace[1];
        }

        self::$errors[] = $error;
    }

    public static function hasErrors() {
        return sizeof(self::$errors);
    }

    public static function getErrorsFormtoolsHash() {
        $out = array();
        foreach (self::$errors as $error) {
            $out[$error->field] = $error->message;
        }
        return $out;
    }

    public static function getErrors() {
        return self::$errors;
    }

    public static function getFirstError() {
        if (sizeof(self::$errors)) {
            return reset(self::$errors);
        }
    }

    public static function getErrorsWithCode($code) {
        $out = array();
        foreach (self::$errors as $error) {
            if ($error->code == $code) {
                $out[] = $error;
            }
        }
        if (!count($out)) {
            return false;
        }
        return $out;

    }

    public static function getErrorWithCode($code) {
        $errors = self::getErrorsWithCode($code);
        if (!$errors) {
            return false;
        }
        return reset($errors);
    }

    public static function getAllMessages($seperator = ' ') {
        $out = array();
        foreach (self::$errors as $error) {
            $out[] = $error->message;
        }
        return implode($seperator, $out);
    }

    public static function backwardsCompatibleBuild($old_school_errors, $error_code = self::DEFAULT_ERROR_CODE) {
        foreach ($old_school_errors as $field => $message) {
            self::raise($message, $error_code, $field);
        }

    }

}