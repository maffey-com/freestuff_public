<?



/*
 * default call FloodControlHelper::check('grab'.$user_id) limits grabs to 30 in a day
 *
 * other examples
 * FloodControlHelper::check($_SERVER['REMOTE_ADDR'],10,5) limits requests from IP address to 10 in 5 seconds
 */
define('FLOOD_DIR', FILES_DIR . "/flood");
class FloodControlHelper {

    public static function allow($id, $floodpool_limit = 30, $floodpool_duration = 86000) {
        $fp = fopen(FLOOD_DIR . DIRECTORY_SEPARATOR . 'fp_' . basename($id) . ".d" . $floodpool_duration, 'a+');

        $called_at = time();
        fwrite($fp, pack('L', $called_at));
        $data = fseek($fp, -4 * $floodpool_limit, SEEK_END);
        if ($data === -1) {
            return true;
        }
        $time = unpack('L', fread($fp, 4))[1];
        fclose($fp);

        if ($called_at - $time > $floodpool_duration) {
            @self::floodpoolClean();
            return true;
        }
        writeLog($id . ' - ' . $floodpool_duration . ' - flood control engaged');
        return false;
    }

    public static function floodpoolClean() {
        $handle = opendir(FLOOD_DIR);
        while (false !== ($entry = readdir($handle))) {
            $filename = FLOOD_DIR . DIRECTORY_SEPARATOR . $entry;
            $duration = substr($filename,stripos($filename,'.d')+2);
            if (time() - filectime($filename) > $duration && substr($entry, 0, 3) === 'fp_') {
                writeLog('unlinking: ' . $filename);
                unlink($filename);
            }
        }
        closedir($handle);
    }

    public static function mkdir() {
        mkdir(FLOOD_DIR);
    }
}