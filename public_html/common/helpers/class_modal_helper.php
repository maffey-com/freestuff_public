<?php

class ModalHelper {
    protected $_title = false;
    protected $_inline_js_blocks = array();

    protected $_javascript_vars = array();

    protected $_main_view_paths = array();

    protected function __construct() {
    }

    /**
     * Factory
     */
    public static function &getInstance() {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }


    /**
     * Expects multiple arguments
     **/
    public static function setViews() {
        $instance = self::getInstance();
        $instance->_main_view_paths = array_merge($instance->_main_view_paths, func_get_args());
    }

    public static function  getViews() {
        $instance = self::getInstance();
        return $instance->_main_view_paths;
    }

    /**
     * Setting the title forces the template to wrap title in modal-content
     *
     *
     * @param $title
     * @return ModalHelper
     */

    public static function setTitle($title) {
        $instance = self::getInstance();
        $instance->_title = $title;
        return $instance;
    }

    public static function getTitle() {
        $instance = self::getInstance();
        return $instance->_title;
    }


    /**
     * echo code behind js code
     */
    public static function echoJS() {

        $tmp = self::getInstance();

        $tmp->echoJsVariables();

        foreach(self::getViews() as $view_path) {
            $js_file = str_ireplace('.php', '.js', $view_path);
            if (file_exists($js_file)) {
                echo '<script type="text/javascript">'.file_get_contents($js_file).'</script>';
            }
        }
    }


    /**
     * Returns the list of variables that will passed to JavaScript
     *
     * @return array
     */
    public static function getJsVars() {
        $instance = self::getInstance();
        return $instance->_javascript_vars;
    }

    public static function echoJsVariables() {
        $instance = self::getInstance();
        echo '<script type="text/javascript" id="page-vars">';
        echo "var \$php = JSON.parse('".json_encode($instance->_javascript_vars)."');";
        echo '</script>';
    }

    /**
     * Add an array of variables that need to be available to the JavaScript code
     *
     * @param array $javascript_vars
     */
    public static function addJsVars($javascript_vars) {
        $instance = self::getInstance();
        $instance->_javascript_vars = array_merge(array(), $instance->_javascript_vars, $javascript_vars);
    }

    /**
     * Add a single PHP variable that needs to be available to the JavaScript code
     * @param $key
     * @param $value
     */
    public static function addJsVar($key, $value) {
        $instance = self::getInstance();
        $instance->_javascript_vars[$key] = $value;
    }


}