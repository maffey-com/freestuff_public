<!doctype html>
<html lang="en">
<head>
    <?
    PageHelper::setMinifyCss(MINIFY_STYLESHEETS);
    #PageHelper::setMinifyCss(TRUE);
    PageHelper::setMinifyTemplateCssName("freestuff");

    // Include stylesheets
    PageHelper::addTemplateStylesheetFile('css/font-awesome/css/font-awesome.min.css', 'all', array('../fonts/' => '../../css/font-awesome/fonts/'));
    PageHelper::addTemplateStylesheetFile('css/bootstrap.min.css');
    PageHelper::addTemplateStylesheetFile('css/main-theme.css');
    PageHelper::addTemplateStylesheetFile('css/main2.css', 'all', array('../img/' => '../../img/'));
    PageHelper::addTemplateStylesheetFile('css/header.css', 'all', array('../img/' => '../../img/'));
    PageHelper::addTemplateStylesheetFile('css/breadcrumbs.css', 'all', array('../img/' => '../../img/'));
    PageHelper::addTemplateStylesheetFile('css/footer.css', 'all', array('../img/' => '../../img/'));
    PageHelper::addTemplateStylesheetFile('css/listing_block.css', 'all', array('../img/' => '../../img/'));
    PageHelper::addTemplateStylesheetFile('css/paging.css', 'all', array('../img/' => '../../img/'));
    #PageHelper::addTemplateStylesheetFile('css/responsive.css');
    PageHelper::addTemplateStylesheetFile('css/croppie.css');

    // Include Javascript
    PageHelper::addTemplateJavascriptOnInitial('common/plugins_js/jquery-1.12.4.min.js');
    PageHelper::addTemplateJavascriptOnInitial('common/plugins_js/bootstrap.bundle.min.js');
    PageHelper::addTemplateJavascriptOnInitial('common/js/jquery.plugin.formtools2.js');
    PageHelper::addTemplateJavascriptOnInitial('common/js/jquery.annoy.js');
    PageHelper::addTemplateJavascriptOnInitial('js/site_javascript.js?v6');

    PageHelper::setCanonicalLink(APP_URL . substr($_SERVER['REQUEST_URI'], 1));

    PageHelper::echoMetaBundle();
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="Publisher" content="maffey.com - website development"/>
    <meta name="robots" content="noodp,noydir"/>
    <?
    PageHelper::echoContentBeforeHeadTagClose();
?>
</head>
<body>
<script>
    var APP_URL = '<?=APP_URL?>';
</script>

<?
PageHelper::echoContentAfterBodyTagOpen();

require_once('templates/common_header.php');
require_once('templates/common_breadcrumbs.php');
require_once('templates/common_site_messages.php');
?>

<div class="container-fluid" id="main-container">
    <?
    foreach (PageHelper::getViews() as $view_path) {
        include($view_path);
    }
    ?>
</div>
<?
require_once("templates/common_footer.php");

PageHelper::echoContentBeforeBodyTagClose();
?>
<script type="text/javascript">
    $(function () {
        <?


        if (SecurityHelper::isLoggedIn()) {
            ?>
            showAlert();
            <?
        }
        ?>
    });
</script>

</body>
</html>
