<?php
class HomeController extends _Controller {
    public function index() {
        $sql = "SELECT l.* 
                FROM listing l 
                JOIN user u ON l.user_id = u.user_id 
                WHERE  listing_status = 'available' AND has_image = 'y' AND listing_type = 'free' 
                ORDER BY rand() LIMIT 30";
        $random_approved_items = runQueryGetAll($sql);


        TemplateHandler::setSelectedMainTab("home");

        PageHelper::setMinifyPageCssName('home.css');
        PageHelper::addPageStylesheetFile('css/home.css', 'all', array('../img/' => '../../img/'));

        PageHelper::setMinifyPageCssName('index');
        PageHelper::setViews(
            "views/index/banner.php",
            "views/index/list.php"
          //  "views/index/the_end.php"
        );

        include("templates/main_layout.php");
    }

    public function test() {
        echo StringHelper::mask("liftupasuperfrog12");
        echo "<br/>";
        echo StringHelper::mask("7778 9229 2029 3091");
        echo "<br/>";
        echo StringHelper::mask("cow shed");
        echo "<br/>";
    }
}

