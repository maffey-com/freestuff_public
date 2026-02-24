<!DOCTYPE html>
<html lang="en">
<head>
    <?
    PageHelper::setMinifyTemplateCssName('standard');
    require_once('templates/common_meta.php');
    ?>
</head>
<body>
<!-- container container -->
<div id="container">
    <?
    require_once("templates/common_sidebar.php");
    ?>
    <!-- Content -->
    <div id="content">
        <!-- Content wrapper -->
        <div class="wrapper">
            <?
            require_once("templates/common_breadcrumbs.php");
            require_once("templates/common_page_header.php");
            require_once("templates/common_site_message.php");
            ?>
            <div class="widget">
                <?
                ob_start();

                foreach (TemplateHandler::getViews() as $view_path) {
                    include($view_path);
                }

                $html_body = ob_get_contents();

                ob_end_clean();

                echo PageHelper::extractAndRemoveInlineJS($html_body);
                ?>
            </div>
        </div>
        <!-- /content wrapper -->
    </div>
    <!-- /content -->
</div>
<!-- /container container -->
<?
PageHelper::echoContentBeforeBodyTagClose();
?>
</body>
</html>
