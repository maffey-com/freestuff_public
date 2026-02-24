<?php
$x = $_GET;
$session_name = session_name();

if (!isset($x[$session_name])) {
	exit;
} else {
	session_id($x[$session_name]);
}
include_once("resources/initial.php");

if (!empty($_FILES)) {
	if (isset($_GET['advert_id'])) {

		$temp_img = new FileHelper('advert', $_GET['advert_id']);
		$temp_img->setFileField("file",false);
		$temp_img->processUploadQueue();


	}

	if ($GLOBALS["error"]) {
		echo $GLOBALS["error"];
	} else{
		echo 1;
	}
}

