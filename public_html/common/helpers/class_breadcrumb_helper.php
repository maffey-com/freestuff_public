<?php
class BreadcrumbHelper {
	protected $_label;
	protected $_breadcrumbs = array();
	
	/** private constructor, only once instance of SiteHandler per application */
	protected function __construct() {
		$this->_label = '';
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
	
	public static function addBreadcrumbs($label, $url = '') {
		$tmp = self::getInstance();
		
		$tmp->_breadcrumbs[$label] = $url;
	}
	
	public static function setLabel($label) {
		$label = trim($label);

		if (!empty($label)) {
			self::getInstance()->_label = $label;
		}
	}
	
	public static function getBreadcrumbs() {
		return self::getInstance()->_breadcrumbs;
	}
	
	public static function echoBreadcrumbs() {
		$output = '';

		$instance = self::getInstance();
		
		if (count($instance->_breadcrumbs) > 1) {
			$output .= '<aside id="breadcrumb">' . $instance->_label . ' ';
			$output .= '<ul>';
		
			foreach ($instance->_breadcrumbs as $tmp_label => $tmp_url) {
				$output .= '<li>';
				
				if (empty($tmp_url)) {
					$output .= $tmp_label;
					
				} else {
					$output .= '<a href="' . $tmp_url . '">' . $tmp_label . '</a>';
				}
				
				$output .= '</li>';
			}
			
			$output .= '</ul>';
			$output .= '</aside>';
		}
		
		echo $output;
	}

	public static function getUrlForBack() {
        $instance = self::getInstance();

        if (count($instance->_breadcrumbs) > 1) {
            end($instance->_breadcrumbs);
            $last_url = prev($instance->_breadcrumbs);
        }
        return $last_url;
    }
	
}