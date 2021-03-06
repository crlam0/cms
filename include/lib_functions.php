<?php

/* =========================================================================

  Misc functions

  ========================================================================= */

use classes\App;
use classes\Image;

/**
 * Check $key in $array
 *
 * @param string $key Key
 * @param array $array Array
 *
 * @return boolean true if key in array
 */
function check_key($key, $array)
{
    if (is_array($array) && array_key_exists($key, $array)) {
        return $array[$key];
    } else {
        return false;
    }
}

/**
 * Print array content
 *
 * @param array $array Input array
 *
 * @return void
 */
function print_array($mixed): void
{
    if (php_sapi_name() !== "cli") {
        echo "<pre>";
    }

    print_r($mixed);

    if (php_sapi_name() !== "cli") {
        echo "</pre>";
    } else {
        echo "\n";
    }
}

/**
 * Add .?? to summ
 *
 * @param float $summ Input summ
 *
 * @return string Output string
 */
function add_zero($summ)
{
    $rub = floor($summ);
    $kop = 100 * round($summ - $rub, 2);
    if (strlen($kop) == 1) {
        $kop = "0" . $kop;
    }
    return $rub . "." . $kop;
}

/**
 * Remove \r and \n from string.
 *
 * @param string $string Input string
 * @param boolean $do_remove_spaces Remove sapces ot not
 *
 * @return string Output string
 */
function my_cleanstring($string, $do_remove_spaces = false)
{
    $string = str_replace("\r", "", $string);
    $string = str_replace("\n", "", $string);
    if ($do_remove_spaces == 1) {
        $string = str_replace(" ", "", $string);
    }
    return $string;
}

/**
 * Cut string to maximum length
 *
 * @param string $string Input string
 * @param integer $lenght Length to cut
 *
 * @return string Output string
 */
function cut_string($string, $lenght)
{
    $b_chars = [" ", ",", ".", ";"];
    $string = strip_tags($string);
    if (strlen($string) <= $lenght) {
        return $string;
    }
    $result = substr($string, 0, $lenght);
    $i = $lenght;
    while (!in_array(substr($string, $i, 1), $b_chars)) {
        $result.=substr($string, $i, 1);
        $i++;
    }
    return $result . " ...";
}

/**
 * Converts bytes to Kb,Mb,Gb
 *
 * @param integer $in Count of bytes
 *
 * @return string Output string
 */
function convert_bytes($in)
{
    $suffix = "";
    if ($in > 1024) {
        $in = $in / 1024;
        $suffix = " Kb";
    }
    if ($in > 1024) {
        $in = $in / 1024;
        $suffix = " Mb";
    }
    if ($in > 1024) {
        $in = $in / 1024;
        $suffix = " Gb";
    }
    if (strlen($suffix)) {
        $in = round($in, 2);
    }
    return($in . $suffix);
}

/**
 * Transliterate string
 *
 * @param string $string Input string
 *
 * @return string Output string
 */
function translit($string)
{
    $cyr = [
        "??", "??", "??", "??", "??", "??", "??", "??", "??", "??",
        "??", "??", "??", "??", "??", "??", "??", "??", "??", "??", "??",
        "??", "??", "??", "??", "??", "??", "??", "??", "??", "??", "??",
        "??", "??", "??", "??",
        "??", "??", "??", "??", "??", "??", "??", "??", "??", "??",
        "??", "??", "??", "??", "??", "??", "??", "??", "??", "??", "??",
        "??", "??", "??", "??", "??", "??", "??", "??", "??", "??", "??",
        "??", "??", "??", "??"
    ];
    $lat = [
        "Shch", "Sh", "Ch", "C", "Yu", "Ya", "J", "A", "B", "V",
        "G", "D", "e", "e", "Z", "I", "y", "K", "L", "M", "N",
        "O", "P", "R", "S", "T", "U", "F", "H", "",
        "Y", "", "E", "E", "Yi", "I",
        "shch", "sh", "ch", "c", "yu", "ya", "j", "a", "b", "v",
        "g", "d", "e", "e", "z", "i", "y", "k", "l", "m", "n",
        "o", "p", "r", "s", "t", "u", "f", "h",
        "", "y", "", "e", "e", "yi", "i"
    ];
    for ($i = 0; $i < count($cyr); $i++) {
        $c_cyr = $cyr[$i];
        $c_lat = $lat[$i];
        $string = str_replace($c_cyr, $c_lat, $string);
    }
    $string = preg_replace(
        "/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/",
        "\${1}e",
        $string
    );
    $string = preg_replace(
        "/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/",
        "\${1}'",
        $string
    );
    $string = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $string);
    $string = preg_replace("/^kh/", "h", $string);
    $string = preg_replace("/^Kh/", "H", $string);
    return $string;
}

/**
 * Encode string for aliases, file names, etc
 *
 * @param string $string Input string
 *
 * @return string Output string
 */
function encodestring($string)
{
    $string = str_replace([" ", "\"", "&", "<", ">"], [" "], $string);
    $string = preg_replace("/[_ \.,?!\[\](){}]+/", "_", $string);
    $string = preg_replace("/-{2,}/", "--", $string);
    $string = preg_replace("/_-+_/", "--", $string);
    $string = preg_replace("/[_\-]+$/", "", $string);
    $string = translit($string);
    $string = strtolower($string);
    $string = preg_replace("/j{2,}/", "j", $string);
    $string = preg_replace("/[^0-9a-z_\-]+/", "", $string);
    return $string;
}

/**
 * @var Array Array of valid MIME types
 */
$validImageTypes = ["image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/x-png"];

/**
 * Move uploaded image with resize if needed
 *
 * @param array $src_file Input file structure
 * @param string $dst_file Destination file name
 * @param integer $max_width Maximum width to resize
 * @param integer $max_height Maximum height to resize
 *
 * @return boolean True if success
 */
function move_uploaded_image($src_file, $dst_file, $max_width = 0, $max_height = 0, $fix_width = 0, $fix_height = 0)
{
    $Image = new Image($src_file['tmp_name'], $src_file['type']);

    if (!$Image->width) {
        App::$message->error('Load image error');
        return false;
    }
    if (!$Image->resize($max_width, $max_height, $fix_width, $fix_height)) {
        return move_uploaded_file($src_file['tmp_name'], $dst_file);
    }
    if (!$Image->save($dst_file)) {
        App::$message->error('Save image error');
        return false;
    }
    return is_file($dst_file);
    //
}

/**
 * Print calendar
 *
 * @param integer $month Month number
 * @param callable $show_day_func Function for customize day output
 *
 * @return void
 */
function show_month($month, $show_day_func = null): void
{
    $month_names = [1 => '????????????', 2 => '??????????????', 3 => '????????', 4 => '????????????', 5 => '??????', 6 => '????????', 7 => '????????', 8 => '????????????', 9 => '????????????????', 10 => '??????????????', 11 => '????????????', 12 => '??????????????'];
    $day_names = ['????', '????', '????', '????', '????', '????', '????'];
    $allow_past = false;
    $date_format = 'd.m.Y';

    $pd = mktime(0, 0, 0, $month, 1, date('Y')); // timestamp of the first day
    $zd = -(date('w', $pd) ? (date('w', $pd) - 1) : 6) + 1; // monday before
    $kd = date('t', $pd); // last day of moth
    echo '
        <div class="month_title">
          <div class="month_name">' . $month_names[date('n', mktime(0, 0, 0, $month, 1, date('Y')))] . ' ' . date('Y', mktime(0, 0, 0, $month, 1, date('Y'))) . '</div>
          <div class="r"></div>
        </div>';
    for ($d = 0; $d < 7; $d++) {
        echo '
        <div class="week_day">' . $day_names[$d] . '</div>';
    }
    echo '
        <div class="r"></div>';
    for ($d = $zd; $d <= $kd; $d++) {
        $i = mktime(0, 0, 0, $month, $d, date('Y'));
        if ($i >= $pd) {
            $today = (date('Ymd') == date('Ymd', $i)) ? '_today' : '';
            // $minulost = (date('Ymd') >= date('Ymd', $i + 86400)) && !$allow_past;
            echo '<div class="day' . $today . '">';
            $day = date('j', $i);
            if (is_callable($show_day_func)) {
                $show_day_func(date('Y-m-d', $i));
            } else {
                echo $day;
            }
            echo '</div>';
        } else {
            echo '
        <div class="no_day">&nbsp;</div>';
        }
        if (date('w', $i) == 0 && $i >= $pd) {
            echo '<div class="r"></div>';
        }
    }
}


/**
 * Return CSRF token value from session.
 *
 * @return string Output string
 */
function get_csrf_token()
{
    if (!App::$session['CSRF_Token']) {
        $token = App::$user->encryptPassword(App::$user->generateSalt(), App::$user->generateSalt());
        App::$session['CSRF_Token'] = $token;
    }
    return App::$session['CSRF_Token'];
}

/**
 * Compare CSRF token from session and input.
 *
 * @return bool Output string
 */
function check_csrf_token(): bool
{
    return App::$input['CSRF_Token'] === App::$session['CSRF_Token'];
}


/**
 * Return block content (for Twig templates)
 *
 * @param string $name Block name
 *
 * @return string Output string
 */
function get_block($name, $allow_cache = false)
{
    return App::get('Blocks')->content($name, $allow_cache);
}

/**
 * Return widget content (for Twig templates)
 *
 * @param string $class_name Class name
 *
 * @return string Output string
 */
function widget($class_name, $allow_cache = false): string
{
    $class_name = str_replace('/', '\\', $class_name);
    if (class_exists($class_name)) {
        $object = new $class_name;
        return $object->run();
    }
    return 'Widget ' . $class_name . ' not found';
}

/**
 * Return result of php script
 *
 * @param string $file_name File name
 *
 * @return string Value
 */
function include_php($file_name)
{
    $DIR = App::$DIR;
    $SUBDIR = App::$SUBDIR;
    if (is_file($DIR . $file_name)) {
        ob_start();
        include_once($DIR . $file_name);
        $content = ob_get_clean();
    } else {
        $content = App::$message->get('file_not_found', ['file_name'=>$file_name]);
    }
    return $content;
}

function get_webpack_asset(string $name)
{
    $file_name = App::$DIR . 'theme/mix-manifest.json';
    if (!file_exists($file_name)) {
        App::error('File mix-manifest.json not exists !');
        return '';
    }
    $json = file_get_contents(App::$DIR . 'theme/mix-manifest.json');
    $assets = my_json_decode($json);
    if (!array_key_exists($name, $assets)) {
        App::error('Key "' . $name . '" not exists in "mix-manifest.json"');
        return '';
    } else {
        return $assets[$name];
    }
}


/**
 * Recursively delete FS tree
 *
 * @param string $dir Directory to remove
 *
 * @return bool True if complete
 */
function del_tree($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? del_tree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

/**
 * Recursively delete cache directory
 *
 * @param string $subdir Subdirectory to remove
 *
 * @return bool True if complete
 */
function clear_cache_dir($subdir = '')
{
    if (strlen($subdir)) {
        return del_tree(App::$DIR . 'var/cache/' . $subdir);
    } else {
        return del_tree(App::$DIR . 'var/cache');
    }
}

/**
 * Decode JSON to array.
 *
 * @param string $json Input string
 *
 * @return mixed Array if complete, false if error.
 */
function my_json_decode($json)
{
    if (strlen($json)) {
        $result = json_decode($json, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            App::debug('JSON decode error: ' . json_last_error_msg() . ' JSON: ' . $json);
            return false;
        }
    } else {
        return false;
    }
    return $result;
}

if (!function_exists('mime_content_type')) {

    function mime_content_type($filename)
    {

        $mime_types = [

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        ];
        $arr=explode('.', $filename);
        $ext = strtolower(array_pop($arr));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }
}


/* =========================================================================

  URL functions

  ========================================================================= */


/**
 * Redirect to $url
 *
 * @param string $url
 *
 * @return void
 */
function redirect($url): void
{
    $content = sprintf('<!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="0;url=%1$s" />

    <title>Redirecting to %1$s</title>
  </head>
  <body>
    Redirecting to <a href="%1$s">%1$s</a>.
  </body>
  </html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

    header('Location: ' . $url);
    header('Status: 302 Found', false, 302);
//    header('Status: 307 Temporary Redirect', false, 307);

    die($content);
}


/**
 * Replace BASE_HREF in content
 *
 * @param string $content Content string
 * @param boolean $direction Direction of replace
 *
 * @return string Output string
 */
function replace_base_href($content, $direction = false)
{
    if ($direction) {
        $content = str_replace(App::$server['REQUEST_SCHEME'] . '://' . App::$server["HTTP_HOST"] . App::$SUBDIR, "[%SUBDIR%]", $content);
        return str_replace(App::$SUBDIR, "[%SUBDIR%]", $content);
    } else {
        // return str_replace("[%SUBDIR%]", $server['REQUEST_SCHEME'] . '://' . $server["HTTP_HOST"] . $SUBDIR, $content);
        return str_replace("[%SUBDIR%]", App::$SUBDIR, $content);
    }
}

/**
 * Return path with subdir
 *
 * @param string $route Route template
 * @param array $params Params to replace in route
 *
 * @return string Output string
 */
function path($route, $params = [])
{
    if (count($params)) {
        foreach ($params as $item => $value) {
            $route = str_replace('{$'.$item.'}', $value, $route);
        }
    }
    return App::$SUBDIR.$route;
}


/**
 * Get item ID by SEO alias
 *
 * @param string $table Table for search
 * @param string $seo_alias SEO alias
 * @param boolean $exit_with_404 Exit with 404 error if SEO alias not found
 *
 * @return integer ID of found item or null
 */
function get_id_by_alias($table, $seo_alias, $exit_with_404 = false)
{
    global $tags;
    list($id) = App::$db->getRow("select id from {$table} where seo_alias = ?", ['seo_alias' => $seo_alias]);
    if ((int)$id > 0) {
        return $id;
    } elseif ($exit_with_404) {
        $tags['Header'] = '???????????????? "' . $seo_alias . '" ???? ??????????????.';
        $content = App::$message->get('error', [], $tags['Header']);
        App::sendResult($content, $tags, 404);
    } else {
        return null;
    }
}


/**
 * Get HREF for menu item
 *
 * @param integer $tmp Unused
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_menu_href($tmp, $row)
{
    return App::$routing->getUrl($row['target_type'], $row['target_id'], $row);
}
