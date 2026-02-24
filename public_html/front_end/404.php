<?php
    require_once("resources/initial.php");

    header("HTTP/1.0 404 Not Found");

    PageHelper::setMinifyCss('404.css');
    PageHelper::addPageStylesheetFile('css/404.css');

    PageHelper::setViews("views/page/404.php");

    include('templates/main_layout.php');