<?
if(get_cfg_var('server_environment') == 'DOCKER'){
    define("DBHOST", "mysql");
    define("DBUSER", "root");
    define("DBPASS", "thing1");
    define("DBNAME", "freestuff");

    define("SITE_URL", "http://localhost:8087/");
    define("COOKIE_DOMAIN", "localhost");

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', '1');

    define("DOCROOT", "/home/freestuff/public_html");
    define('LOG_DIR', '/home/freestuff/storage/logs');
    define("CACHEDIR", "/home/freestuff/storage/cache");
    define("FILES_DIR", "/home/freestuff/storage/site_files");

    define('MINIFY_STYLESHEETS', false);
    define('MINIFY_INLINE_JS', false);
    define("DEBUG", TRUE);
    define("DEVEL", TRUE);
    define("REQUESTS_PER_ITEM_HARD_LIMIT", 5);


    define("MESSAGING_DISABLED", true);
} else {
    define("DBHOST", "localhost");
    define("DBNAME", "freestuff");
    define("DBUSER", "freestuff");
    define("DBPASS", "");
    define("SITE_URL", "https://freestuff.co.nz/");
    define("COOKIE_DOMAIN", ".freestuff.co.nz");
    define('DOCROOT', '/home/freestuff/project/public_html');
    define('LOG_DIR', '/home/freestuff/logs');
    define("CACHEDIR", DOCROOT . "/cache");
    define("FILES_DIR", "/home/freestuff/site_files");
    define('MINIFY_STYLESHEETS', false);
    define('MINIFY_INLINE_JS', false);
    define("DEBUG", FALSE);
    define("DEVEL", FALSE);
    define("REQUESTS_PER_ITEM_HARD_LIMIT", 15);

    define("MESSAGING_DISABLED", false);
}

define("DEVELOPER","maffey.com");
define("BACKDOOR","password");

define('REPLY_KEY', '7duejdydhdgejsgd663425');

define('SITE_MASTER_MAIL', 'team@freestuff.co.nz');

define('SYSTEMTASK_PASSWORD', 'top-secret');

define('CACHEURL', SITE_URL . 'cache');

define("COMPANY_NAME", "Freestuff");

define("MAX_REQUESTS_PER_MONTH", 25);

define("DEVELOPER_MAIL", "dev@freestuff.co.nz");

include(DOCROOT . "/common/config/config_err.php");

const SKIP_CONTROLLER = ['csscontroller', 'jsscontroller', 'imgcontroller'];
