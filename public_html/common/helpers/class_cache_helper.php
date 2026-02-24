<?php

class CacheHelper {
    static protected $_buffering = array();
    static protected $_cache_file = false;

    /**
     * Automatically cache output.
     * Wrap a while statement around the code you want to cache
     * eg while (CacheHelper::notCached('home','1') {
     *    output some stuff
     * }
     *
     *
     * @param $object used to group content together
     * @param $id used to identify individual contentt
     * @param int $seconds no of seconds the cache will be kept for
     * @return bool
     */
   static function getOB($object, $id, $seconds = 1800) {
        $filename = self::_getFileName($object, $id);

        if (isset($_GET["clear_cache"])) {
            self::clearCacheForObject($object);
        }

        if (isset(self::$_buffering[$filename])) {
            //we are currently caching something
            $output = ob_get_flush();
            file_put_contents($filename, $output);
            unset(self::$_buffering[$filename]);
            return false;
        }

        if (is_file($filename)) {
            if (time() - filemtime($filename) < $seconds) {
                //we have a cached version, show it.
                echo file_get_contents($filename);
                return false;
            } else {
                //file is too old
                unlink($filename);
            }
        }

        //start output buffer
        ob_start();
        self::$_buffering[$filename] = true;
        return true;
    }

    static function getPage($object, $id, $seconds = 1800) {
        if (!$object || !$id) {
            return false;
        }
        $filename = self::_getFileName($object, $id);

        if (isset($_GET["clear_cache"])) {
            self::clearCacheForObject($object);
        }
        if (is_file($filename)) {
            if (time() - filemtime($filename) < $seconds) {
                echo file_get_contents($filename);
                exit();
            } else {
                //file is too old
                unlink($filename);
            }
        }

        //start output buffer
        ob_start();
        self::$_cache_file = $filename;
        return true;
    }

    static function setPage() {
        if (self::$_cache_file) {
            $output = ob_get_flush();
            file_put_contents(self::$_cache_file, $output);
            self::$_cache_file = false;
        }

    }


    static function getVariableFromCache($object, $id, $seconds = 1800) {
        $filename = self::_getFileName($object, $id);
        if (isset($_GET["clear_cache"])) {
            self::clearCacheForObject($object);
        }
        if (is_file($filename)) {
            if (time() - filemtime($filename) < $seconds) {
                return unserialize(file_get_contents($filename));
            } else {
                //file is too old
                unlink($filename);
            }
        }
        return false;
    }

    static function putVariableInCache($object,$id,$variable) {
        $filename = self::_getFileName($object, $id);
        file_put_contents($filename, serialize($variable));
    }

    static function clearCache() {
        self::_delTree(FILES_DIR . "/cache_helper");
    }


    static function clearCacheForObject($object) {
        $dir_name = FILES_DIR . "/cache_helper/" . $object;
        if (is_dir($dir_name)) {
            self::_delTree($dir_name);
        }
        mkdir($dir_name);
    }

    static function clearCacheForIndividualFile($object, $id) {
        $filename = self::_getFileName($object, $id);
        unlink($filename);
    }

    protected static function _getFileName($object, $id) {
        $file_name = FILES_DIR . "/cache_helper";
        if (!is_dir($file_name)) {
            mkdir($file_name);
        }
        $file_name = $file_name . "/$object";
        if (!is_dir($file_name)) {
            mkdir($file_name);
        }
        return "$file_name/$id";
    }

    protected static function _delTree($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::_delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}