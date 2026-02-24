<?php
chdir(__DIR__);
require_once("resources/initial.php");

// Only run if called from the commandline and the psw is set correctly.
if (!php_sapi_name() == 'cli' && (paramFromGet('psw') != SYSTEMTASK_PASSWORD)) {
    die("Access denied.");
}
$dir = FILES_DIR."/listing_images";

$listing_ids = runQueryGetAllFirstValues("select listing_id from listing");

$image_dirs = scandir($dir);
$empty_count = 0;
$not_in_db = 0;
$extra_files = 0;

foreach ($image_dirs as $image_dir) {
    if ($image_dir == '.' || $image_dir == '..') {
        continue;
    }
    $image_full_dir = $dir."/".$image_dir;
    if (is_file($image_full_dir)) {
        unlink($image_full_dir);
        continue;
    }

    //remove empty directories
    $image_files = scandir($image_full_dir);
    if (count($image_files) <= 3) {
        if (in_array(".meta", $image_files)) {
            unlink($image_full_dir."/.meta");
        }
        $image_files = scandir($image_full_dir);
        if (count($image_files) <= 2) {
            rmdir($image_full_dir);
            $empty_count++;
            continue;
        } else {
            print_r("Failed to remove ".$image_full_dir.". contains file $image_files[2] \n");
        }
    }
    //remove directories not in listing table
    $listing_id = (int) $image_dir;
    if (!in_array($listing_id, $listing_ids)) {
        $not_in_db++;
        foreach ($image_files as $image_file) {
            if ($image_file == '.' || $image_file == '..') {
                continue;
            }
            $image_full_file = $image_full_dir."/".$image_file;
            if (is_file($image_full_file)) {
                print_r("Removing $image_full_file\n");
                unlink($image_full_file);
            }
        }
        rmdir($image_full_dir);
        continue;
    }
    //remove extra files
    if (count($image_files) >= 5) {

        echo "Removing extra files in $image_full_dir\n";
        $fh = new FileHelper("listing_images",$listing_id);
        $files = $fh->listFiles();
        $latest_upload = $fh->getFileNameFromTag("most_recent_upload");

        if (!$latest_upload) {
            exit("No latest upload found for listing $listing_id\n");
        }
        if (!isset($files[$latest_upload])) {
            exit("Latest upload not found files list for $listing_id\n");
        }
        unset($files[$latest_upload]);

        foreach ($files as $file) {
            $fh->deleteFile($file);
            print_r(" --- Removing $file\n");
            $extra_files++;
        }
    }
}

print_r("Removed ".$empty_count." empty directories\n");
print_r("Found ".$not_in_db." directories not in listing table\n");
print_r("Removed ".$extra_files." extra files\n");


function GetDirectorySize($path){
    $bytestotal = 0;
    $path = realpath($path);
    if($path!==false && $path!='' && file_exists($path)){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}