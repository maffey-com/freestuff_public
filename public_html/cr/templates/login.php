<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    PageHelper::setMinifyTemplateCssName('login');
    require_once('templates/common_meta.php');
    ?>
</head>

<body class="no-background">
<!-- Login block -->
<div class="login">
    <? require_once("templates/common_site_message.php");
    foreach (TemplateHandler::getViews() as $view) {
        include($view);

    }
    ?>
</div>
<!-- /login block -->

</body>
</html>

