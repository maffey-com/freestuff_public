<?php

class PageHelper {
    const MINIFIIED_CSS_DIRECTORY = 'cache/css_compressed';
    static protected $_rss_link = '';
    static protected $_base_url = '';
    protected $_meta_title;
    protected $_meta_description;
    protected $_meta_base_url;
    protected $_meta_author;
    protected $_meta_charset;
    protected $_canonical_link;
    protected $_meta_content_type;
    protected $_favicon_url = '';
    protected $_copyright = '';
    protected $_additional_content = array('before_head_close' => array(), 'after_body_open' => array(), 'before_body_close' => array(),);

    protected $_script_files = array();      // load template first and then page
    protected $_css_for_template = array();      // load template first and then page
    protected $_css_for_page = array();
    protected $_js_after_page_loaded_files = array('template' => array(), 'page' => array());
    protected $_js_initial_files = array('template' => array(), 'page' => array());
    protected $_minify_css = FALSE;
    protected $_minified_template_css_version = 1;
    protected $_minified_template_css_name = '';
    protected $_minified_page_css_version = 1;
    protected $_minified_page_css_name = '';
    protected $_inline_js_blocks = array();

    protected $_javascript_vars = array(
        'app_url' => APP_URL
    );

    protected $_main_view_paths = array();

    /** private constructor, only once instance of SiteHandler per application */
    protected function __construct() {
        if (!file_exists(self::MINIFIIED_CSS_DIRECTORY)) {
            mkdir(self::MINIFIIED_CSS_DIRECTORY, 0775, TRUE);
        }
    }

    /**
     * Factory
     */
    public static function &getInstance() {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
            $instance::_setDefaultPageSettings();
        }

        return $instance;
    }

    public static function setMainView($main_view_path) {
        self::setViews($main_view_path);
    }

    /**
     * Expects multiple arguments
     **/
    public static function setViews() {
        $instance = PageHelper::getInstance();
        $instance->_main_view_paths = array_merge($instance->_main_view_paths, func_get_args());
    }

    public static function  getViews() {
        $instance = PageHelper::getInstance();
        return $instance->_main_view_paths;
    }

    /**
     * @param string $css_name
     * @param int    $version
     */
    public static function setMinifyTemplateCssName($css_name, $version = 1) {
        $css_name = trim($css_name);
        $version  = (int)$version;
        $instance                                 = self::getInstance();
        $instance->_minified_template_css_name    = strtolower($css_name);
        $instance->_minified_template_css_version = $version;
    }

    protected static function _setDefaultPageSettings() {
        self::setMetaTitle(COMPANY_NAME);
        self::setMetaDescription(COMPANY_NAME);
        self::setAuthor(COMPANY_NAME);
        self::setBaseUrl(APP_URL);
        self::setMetaCharset("UTF-8");
        self::setMetaContentType("website");
        self::setMinifyCss(MINIFY_STYLESHEETS);
        self::setFavIconUrl('favicon.ico');
        PageHelper::setRssLink(APP_URL . 'rss_feed');
    }

    /**
     * Set meta title
     *
     * @param string $input
     */
    public static function setMetaTitle($input) {
        $input = trim(strip_tags($input));
        if (!empty($input)) {
            self::getInstance()->_meta_title = $input;
        }
    }

    /**
     * Set meta description
     *
     * @param string $input
     */
    public static function setMetaDescription($input) {
        $input = trim(strip_tags($input));
        if (!empty($input)) {
            self::getInstance()->_meta_description = $input;
        }
    }

    /**
     * Set meta author
     *
     * @param string $input
     */
    public static function setAuthor($input) {
        $input = trim($input);
        if (!empty($input)) {
            self::getInstance()->_meta_author = $input;
        }
    }

    public static function setBaseUrl($url) {
        self::$_base_url = $url;
    }

    /**
     * Set meta charset
     *
     * @param string $input
     */
    public static function setMetaCharset($input) {
        self::getInstance()->_meta_charset = $input;
    }

    /**
     * Set meta keywords
     *
     * @param string $input
     */
    public static function setMetaContentType($input) {
        $input = trim(strip_tags($input));
        if (!empty($input)) {
            self::getInstance()->_meta_content_type = $input;
        }
    }

    /**
     * @param boolean $minify_stylesheets        - whether or not to merge multiple stylesheets into 1 stylesheet
     * @param string  $minified_stylehsheet_name - this field only get used if $minify_stylesheets is set to TRUE. it determines the filename of the merged stylesheet
     * @param int     $version
     */
    public static function setMinifyCss($minify_stylesheets) {
        self::getInstance()->_minify_css = (boolean)$minify_stylesheets;
    }

    /**
     * Set favicon url
     *
     * @param string $input
     */
    public static function setFavIconUrl($input) {
        $input = trim($input);
        if (!empty($input)) {
            self::getInstance()->_favicon_url = $input;
        }
    }

    public static function setRssLink($link) {
        self::$_rss_link = $link;
    }

    /**
     * @param string $css_name
     * @param int    $version
     */
    public static function setMinifyPageCssName($css_name, $version = 1) {
        $css_name = trim($css_name);
        $version  = (int)$version;
        $instance                             = self::getInstance();
        $instance->_minified_page_css_name    = strtolower($css_name);
        $instance->_minified_page_css_version = $version;
    }

    /**
     * Add stylesheets to the current template
     * (there are 2 types of stylesheet - template and  page specific)
     * multiple pages can use the same 'template', adding its own page specific stylesheets
     * template stylesheet are always included first to avoid overwriting the page specific stylesheets
     *
     * @param string $file
     * @param string $media               (all|print|screen|speech)
     * @param array  $replace_directories (used if you merge multiple stylesheets into 1 file, to handle directory within the files)
     *
     * E.g. PageHelper::addTemplateStylesheetFile("xxxx.css", 'all', array("../fonts/" => "../../plugins/fontawesome/fonts/"));
     */
    public static function addTemplateStylesheetFile($file, $media = 'all', array $replace_directories = array()) {
        self::_addStylesheetFile('template', $file, $media, $replace_directories);
    }

    /**
     * add stylesheets file in an interal array
     *
     * @param string $type  (template|page)
     * @param string $source_file_name
     * @param string $media (all|print|screen|speech)
     * @param array  $replace_directories
     */
    protected static function _addStylesheetFile($type, $source_file_name, $media, $replace_directories = array()) {
        $source_file_name = trim($source_file_name);
        $media            = empty($media) ? 'all' : $media;
        if ((stripos($source_file_name, 'http') !== FALSE)) {
            die("SYSTEM DOES NOT HANDLE EXTERNAL CSS FILES AT THIS STAGE");
        }
        if (empty($source_file_name)) {
            return;
        }
        $source_full_path = $source_file_name;
        if (!file_exists($source_full_path)) {
            $source_full_path = DOCROOT . '/' . $source_full_path;
            if (!file_exists($source_full_path)) {
                die("FAIL TO LOCATE CSS FILE: " . $source_file_name);
            }
        }
        $instance = self::getInstance();
        if ($type == 'template') {
            $css_list = '_css_for_template';
            if (empty($instance->_minified_template_css_name)) {
                die('Template stylesheet name is not set');
            }
        } else {
            $css_list = '_css_for_page';
            if (empty($instance->_minified_page_css_name)) {
                die('Page specific stylesheet name is not set');
            }
        }
        if (!array_key_exists($media, $instance->{$css_list})) {
            $instance->{$css_list}[ $media ] = array('last_modified_time' => 0, 'files' => array(),);
        }
        $source_last_modified_time = filemtime($source_full_path);
        if ($source_last_modified_time > $instance->{$css_list}[ $media ]['last_modified_time']) {
            $instance->{$css_list}[ $media ]['last_modified_time'] = $source_last_modified_time;
        }
        $instance->{$css_list}[ $media ]['files'][] = array('name' => $source_file_name, 'replace_directories' => $replace_directories);
    }

    /**
     * Add stylesheets to the current page
     * (there are 2 types of stylesheet - template and  page specific)
     * multiple pages can use the same 'template', adding its own page specific stylesheets
     * template stylesheet are always included first to avoid overwriting the page specific stylesheets
     *
     * @param string $file
     * @param string $media               (all|print|screen|speech)
     * @param array  $replace_directories (used if you merge multiple stylesheets into 1 file, to handle directory within the files)
     *
     * E.g. PageHelper::addPageStylesheetFile("xxxx.css", 'all', array("../fonts/" => "../../plugins/fontawesome/fonts/"));
     */
    public static function addPageStylesheetFile($file, $media = 'all', $replace_directories = array()) {
        $instance = self::getInstance();
        self::_addStylesheetFile('page', $file, $media, $replace_directories);
    }

    public static function setCopyright($copyright) {
        self::getInstance()->_copyright = $copyright;
    }

    /**
     * Set canonical link
     *
     * @param string $input
     */
    public static function setCanonicalLink($input) {
        $input = trim($input);
        if ((!empty($input)) && ($input != SITE_URL)) {
            self::getInstance()->_canonical_link = $input;
        }
    }

    /**
     * Get meta title
     * public static function getMetaTitle() {
     * return self::getInstance()->_meta_title;
     * }
     *
     * /**
     * Get meta description
     * public static function getMetaDescription() {
     * return self::getInstance()->_meta_description;
     * }
     */

    /**
     * echo out a whole bunch of meta tags
     */
    public static function echoMetaBundle() {
        self::echoMetaCharset();
        self::echoMetaContentType();
        self::echoMetaTitle();
        self::echoMetaDescription();
        self::echoBaseUrl();
        self::echoCanonicalTag();
        self::echoMetaAuthor();
        self::echoRssLink();
        self::echoCopyright();
        self::echoFavIcon();
        self::echoStyleSheetLinks();
    }

    /**
     * echo meta charset tag
     */
    public static function echoMetaCharset() {
        echo '<meta charset="' . self::getInstance()->_meta_charset . '" />';
        echo "\n";
    }

    /**
     * echo meta content type tag
     */
    public static function echoMetaContentType() {
        echo '<meta property="og:type" content="' . self::getInstance()->_meta_content_type . '" />';
        echo "\n";
        echo '<meta http-equiv="content-language" content="en" />';
        echo "\n";
    }

    /**
     * echo meta title and og:title tags
     */
    public static function echoMetaTitle() {
        $instance = self::getInstance();
        $tmp_title = $instance->_meta_title . ' | ' . COMPANY_NAME;
        echo '<title>' . $tmp_title . '</title>';
        echo "\n";
        echo '<meta property="og:title" content="' . $tmp_title . '" />';
        echo "\n";
    }

    /**
     * echo meta description and og:description tags
     */
    public static function echoMetaDescription() {
        $instance = self::getInstance();
        echo '<meta name="description" content="' . $instance->_meta_description . '" />';
        echo "\n";
        echo '<meta property="og:description" content="' . $instance->_meta_description . '" />';
        echo "\n";
    }

    public static function echoBaseUrl() {
        echo '<base href="' . self::$_base_url . '" />';
        echo "\n";
    }

    /**
     * echo meta canonical link tag
     */
    public static function echoCanonicalTag() {
        echo '<link rel="canonical" href="' . self::getInstance()->_canonical_link . '" />';
        echo "\n";
        echo '<meta property="og:url" content="' . self::getInstance()->_canonical_link . '" />';
        echo "\n";
    }

    /**
     * echo meta author tag
     */
    public static function echoMetaAuthor() {
        echo '<meta name="author" content="' . self::getInstance()->_meta_author . '" />';
        echo "\n";
    }

    public static function echoRssLink() {
        if (!empty(self::$_rss_link)) {
            echo '<link rel="alternate" type="application/rss+xml" title="RSS" href="' . self::$_rss_link . '" />';
            echo "\n";
        }
    }

    public static function echoCopyright() {
        $copyright = self::getInstance()->_copyright;
        if (!empty($copyright)) {
            echo '<meta name="Copyright" content="' . $copyright . '" />';
            echo "\n";
        }
    }

    /**
     * echo favicon tag
     */
    public static function echoFavIcon() {
        $fav_icon = self::getInstance()->_favicon_url;
        if ($fav_icon) {
            echo '<link rel="shortcut icon" href="' . self::getInstance()->_favicon_url . '" type="image/x-icon">';
            echo "\n";
            echo '<link rel="icon" href="' . self::getInstance()->_favicon_url . '" type="image/x-icon">';
            echo "\n";
        }
    }

    /**
     * echo stylesheet <link> tags
     */
    public static function echoStyleSheetLinks() {
        $output = '';
        $instance = self::getInstance();
        echo "<!-- Template specific CSS -->\n";
        $instance->_processStylesheetFiles(TRUE);
        echo "\n";
        echo "<!-- Page specific CSS -->\n";
        $instance->_processStylesheetFiles(FALSE);
        echo "\n\n";
    }

    /**
     * Do the magic with CSS files,
     * either display them as individual <link> tags, OR, merge mulitple css into 1 file
     *
     * @param bool $is_template_css
     */
    protected function _processStylesheetFiles($is_template_css) {
        $stylesheet_files = ($is_template_css) ? $this->_css_for_template : $this->_css_for_page;
        foreach ($stylesheet_files as $tmp_media => $tmp_info) {
            $tmp_media_data = paramFromHash($tmp_media, $stylesheet_files, array());
            $tmp_css_files  = paramFromHash('files', $tmp_media_data, array());
            if (count($tmp_css_files) == 0) {
                continue;
            }
            if ($tmp_media == 'all') {
                $link_append = ' media="all"';
            } else {
                $link_append = ' media="' . $tmp_media . '"';
            }
            if ($this->_minify_css) {
                if ((!$is_template_css) && (empty($this->_minified_page_css_name))) {
                    die("Page CSS name not set");
                }
                $source_last_modified = (int)paramFromHash('last_modified_time', $tmp_media_data);
                if ($is_template_css) {
                    $target_file_name = 'min.' . $this->_minified_template_css_name . '.' . $tmp_media . '.' . $this->_minified_template_css_version . '.css';
                } else {
                    $target_file_name = 'min.' . $this->_minified_page_css_name . '.' . $tmp_media . '.' . $this->_minified_page_css_version . '.css';
                }
                $target_full_path = self::MINIFIIED_CSS_DIRECTORY . '/' . $target_file_name;

                $target_last_modified = file_exists($target_full_path) ? filemtime($target_full_path) : 0;
                if ($source_last_modified > $target_last_modified) {
                    $this->_compressAndSaveStylesheet($tmp_media, $tmp_css_files, $target_full_path);
                }
                echo '<link rel="stylesheet" href="' . $target_full_path . '" ' . $link_append . " />\n";
            } else {
                foreach ($tmp_css_files as $tmp_css) {
                    $tmp_name = $tmp_css["name"];
                    $tmp_name = str_ireplace(DOCROOT, SITE_URL, $tmp_name);
                    echo '<link rel="stylesheet" href="' . $tmp_name . '" ' . $link_append . " />\n";
                }
            }
        }
    }

    /**
     * Get all CSS files for a media type, merge all files into 1 file and save it
     *
     * @param string $media_type
     * @param array  $source_css_files
     * @param string $target_full_path
     */
    protected function _compressAndSaveStylesheet($media_type, $source_css_files, $target_full_path) {
        $buffer = '';
        # template first and the page
        $buffer .= self::_retrieveCssContentFromFileList($source_css_files);
        # Remove comments
        // $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        # Remove space after colons
        // $buffer = str_replace(': ', ':', $buffer);
        # Remove lines or tabs
        // $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
        /*
         * Josh's version
         */
        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // Remove tabs, spaces, and line breaks
        $buffer = preg_replace(array('/\s{2,}/', '/[\t\n]/'), '', $buffer);
        // whitespace around punctuation
        $buffer = preg_replace('/\s*([:;{}])\s*/', '$1', $buffer);
        // final semicolon
        $buffer = preg_replace('/;}/', '}', $buffer);
        file_put_contents($target_full_path, $buffer);
    }

    /**
     * Combine the contents of multiple stylesheet files
     *
     * @param array $css_files
     *
     * @return string
     */
    protected function _retrieveCssContentFromFileList($css_files) {
        $output = '';
        # loop through each CSS file
        foreach ($css_files as $file_info) {
            $source_file_name    = $file_info['name'];
            $replace_directories = $file_info['replace_directories'];
            $file_basename = basename($source_file_name);
            # add the CSS file name
            $output .= "\n/* CSS File: " . $file_basename . "\n";
            $output .= str_repeat("-", 34) . "*/\n";
            if (!file_exists($source_file_name)) {
                $source_file_name = DOCROOT . '/' . $source_file_name;
            }
            # get the content of the file
            if (file_exists($source_file_name)) {
                $css_content = file_get_contents($source_file_name);
            }
            # fix assets directory path
            if (count($replace_directories) > 0) {
                $search  = array_keys($replace_directories);
                $replace = array_values($replace_directories);
                $css_content = str_replace($search, $replace, $css_content);
            }
            $output .= $css_content;
        }

        return $output;
    }

    /**
     * add javascript file to the current template
     * (these fils will be included to the page AFTER page has been loaded)
     *
     * @param string $file
     */
    public static function addTemplateJavascriptAfterPageLoaded($file) {
        self::_addScript($file, 'template', 'after_page_loaded');
    }

    /**
     * add script file in an interal array
     *
     * @param string $file
     * @param string $type (initial | after_page_loaded)
     * @param string $trigger_point
     */
    protected static function _addScript($file, $type = 'template', $trigger_point = 'after_page_loaded') {
        $file  = trim($file);
        $field = ($trigger_point == 'after_page_loaded') ? '_js_after_page_loaded_files' : '_js_initial_files';
        self::getInstance()->{$field}[ $type ][] = $file;
    }

    /**
     * add javascript file to the current page
     * (these fils will be included to the page AFTER page has been loaded)
     *
     * @param string $file
     */
    public static function addPageJavascriptAfterPageLoaded($file) {
        self::_addScript($file, 'page', 'after_page_loaded');
    }

    /**
     * add javascript file to the current template
     * (these fils will be included to the page AS page is loading, at the bottom of the page)
     *
     * @param string $file
     */
    public static function addTemplateJavascriptOnInitial($file) {
        self::_addScript($file, 'template', 'initial');
    }



    /**
     * add javascript file to the current page
     * (these fils will be included to the page AS page is loading, at the bottom of the page)
     *
     * @param string $file
     */
    public static function addPageJavascriptOnInitial($file) {
        self::_addScript($file, 'page', 'initial');
    }

    /**
     * add codes to be added before the <head> closes
     *
     * @param string $input
     */
    public static function addContentBeforeHeadTagClose($input) {
        self::getInstance()->_additional_content['before_head_close'][] = $input;
    }

    /**
     * add codes to be added after the <body> opens
     *
     * @param string $input
     */
    public static function addContentAfterBodyTagOpen($input) {
        self::getInstance()->_additional_content['after_body_open'][] = $input;
    }

    /**
     * add codes to be added before the <body> closes
     *
     * @param string $input
     */
    public static function addContentBeforeBodyTagClose($input) {
        self::getInstance()->_additional_content['before_body_close'][] = $input;
    }

    /**
     * echo codes before the <head> closes
     */
    public static function echoContentBeforeHeadTagClose() {
        $tmp = self::getInstance();
        foreach ($tmp->_additional_content['before_head_close'] as $code) {
            echo $code;
        }
    }

    /**
     * echo codes after the <body> opens
     */
    public static function echoContentAfterBodyTagOpen() {
        $tmp = self::getInstance();
        foreach ($tmp->_additional_content['after_body_open'] as $code) {
            echo $code;
        }
    }

    /**
     * echo codes to after the <body> closes
     */
    public static function echoContentBeforeBodyTagClose() {

        $tmp = self::getInstance();
        self::_echoInitialScriptFiles($tmp->_js_initial_files);
        self::_echoAfterPageLoadedScriptFiles($tmp->_js_after_page_loaded_files);
        $tmp->_echoInlineJavascriptBlocks();

        $tmp->echoJsVariables();

        foreach(PageHelper::getViews() as $view_path) {

            $js_file = str_ireplace('.php', '.js', $view_path);
            if (file_exists($js_file)) {
                $modify_time = filemtime($js_file);
                $js_file = substr_replace($js_file,  'js-include/', 0, strlen('views/'));
                echo '<script type="text/javascript" src="'.$js_file.'?'.$modify_time.'"></script>';
            }
        }

        foreach ($tmp->_additional_content['before_body_close'] as $code) {
            echo $code;
        }
    }

    /**
     * echo out <script> for all initial script files (both template and page)
     * (template script files are loaded before page script files)
     */
    protected static function _echoInitialScriptFiles(array $js_files) {
        foreach ($js_files as $type => $files) {
            foreach ($files as $tmp_file) {
                echo "<script type='text/javascript' src='" . $tmp_file . "'></script>\n";
            }
        }
    }

    /*
     * echo out all stylesheet link tag as is, no compress/ concatenation applied (only used in backend???)
     */

    /**
     * echo function to dynamically loads 'OnLoad' script files (both template and page)
     * (template script files are loaded before page script files)
     *
     * @param array $js_files
     */
    protected static function _echoAfterPageLoadedScriptFiles(array $js_files) {
        echo "
<span id='javascript-insert-point'></span>

<script type='text/javascript'>
function addJS(url){
\tvar insertPoint = document.getElementById('javascript-insert-point');
\tvar element = document.createElement('script');
\telement.src = url;
\tinsertPoint.appendChild(element);
}\n\n";
        echo "function downloadJSAtOnload(){\n";
        foreach ($js_files as $type => $files) {
            foreach ($files as $file) {
                echo "\taddJS('" . $file . "');\n";
            }
        }
        echo "}

if (window.addEventListener) {
\twindow.addEventListener('load', downloadJSAtOnload, false);
} else if (window.attachEvent) {
\twindow.attachEvent('onload', downloadJSAtOnload);
} else {
\twindow.onload = downloadJSAtOnload;
}\n\n";
        echo "</script>\n";
    }

    /**
     * Print out any captured script blocks
     *
     * @param bool $minify_inline
     *
     * @throws Exception
     */
    protected function _echoInlineJavascriptBlocks() {
        $all_js = "";
        foreach ($this->_inline_js_blocks as $block_js) {
            if (MINIFY_INLINE_JS) {
                $all_js .= $block_js;
            } else {
                echo "<script type='text/javascript'>" . $block_js . "</script>\n";
            }
        }
        if (MINIFY_INLINE_JS) {
            $min = JsShrinkHelper::minify($all_js);
            echo "<script type='text/javascript'>" . $min . "</script>";
        }
    }

    public static function echoStylesheetLinkTags() {
        $output       = '';
        $tmp_instance = self::getInstance();
        if (count($tmp_instance->_css_files)) {
            foreach ($tmp_instance->_css_files as $tmp) {
                $tmp_file  = $tmp['file'];
                $tmp_hack  = $tmp['hack'];
                $tmp_media = $tmp['media'];
                if (empty($tmp_hack)) {
                    $output .= '<link rel="stylesheet" href="' . $tmp_file . '" media="' . $tmp_media . '" />';
                } else {
                    $output .= '<!--[if ' . $tmp_hack . ']><link rel="stylesheet" href="' . $tmp_file . '" media="' . $tmp_media . '" /><![endif]-->';
                }
                $output .= "\n";
            }
        }
        echo $output;
    }

    /**
     * strip out inline JS and pop it in the queue to be displayed later.
     *
     *
     * @param string $html_body
     *
     * @return string
     */
    public static function extractAndRemoveInlineJS($html_body = '') {
        $find_pattern         = '/<script\s*(?:(?!src=)[^>])*>(.*?)<\/script>/is';
        $find_replace_pattern = '/(<script\s*(?:(?!src=)[^>])*>)(.*?)(<\/script>)/is';
        preg_match_all($find_pattern, $html_body, $matched_items, PREG_PATTERN_ORDER);
        #$tmp_js_chunks = $matched_items[0];
        $tmp_inline_chunks = $matched_items[1];
        if (count($tmp_inline_chunks) > 0) {
            self::addInlineJavascriptBlocks($tmp_inline_chunks);
            $html_body = preg_replace($find_replace_pattern, '', $html_body);
        }

        return $html_body;
    }

    /**
     * @param string|array $input
     */
    public static function addInlineJavascriptBlocks($input) {
        $instance = self::getInstance();
        if (is_string($input)) {
            $instance->_inline_js_blocks[] = $input;
        } else if (is_array($input)) {
            $instance->_inline_js_blocks = array_merge($instance->_inline_js_blocks, $input);
        }
    }

    /**
     * Returns the list of variables that will passed to JavaScript
     *
     * @return array
     */
    public static function getJsVars() {
        $instance = PageHelper::getInstance();

        return $instance->_javascript_vars;
    }

    public static function echoJsVariables() {
        $instance = PageHelper::getInstance();
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
        $instance = PageHelper::getInstance();
        $instance->_javascript_vars = array_merge(array(), $instance->_javascript_vars, $javascript_vars);
    }

    /**
     * Add a single PHP variable that needs to be available to the JavaScript code
     * @param $key
     * @param $value
     */
    public static function addJsVar($key, $value) {
        $instance = PageHelper::getInstance();
        $instance->_javascript_vars[$key] = $value;
    }


}
