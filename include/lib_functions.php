<?php

/* =========================================================================

  Misc functions

  ========================================================================= */

use Classes\MyGlobal;

/**
 * Check $key in $array
 *
 * @param string $key Key
 * @param array $array Array
 *
 * @return boolean true if key in array
 */
function check_key($key, $array) {
    if(isset($array) && array_key_exists($key, $array)) {
        return $array[$key];
    } else {
        return false;
    }
}

/**
 * Check user access
 *
 * @param string $flag Flag title
 *
 * @return boolean true if user have access
 */
function have_flag($flag) {
    global $_SESSION;
    if (!strlen($flag)) {
        return true;
    }
    if(!check_key('FLAGS',$_SESSION)) {
        return false;
    }
    return strstr($_SESSION['FLAGS'], $flag);
}

/**
 * Add .?? to summ
 *
 * @param float $summ Input summ
 *
 * @return string Output string
 */
function add_zero($summ) {
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
function my_cleanstring($string, $do_remove_spaces = false) {
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
function cut_string($string, $lenght) {
    $b_chars = array(" ", ",", ".", ";");
    $string = strip_tags($string);
    if (strlen($string) <= $lenght){
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
function convert_bytes($in) {
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
function translit($string) {
    $cyr = array(
        "Щ", "Ш", "Ч", "Ц", "Ю", "Я", "Ж", "А", "Б", "В",
        "Г", "Д", "Е", "Ё", "З", "И", "Й", "К", "Л", "М", "Н",
        "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ь", "Ы", "Ъ",
        "Э", "Є", "Ї", "І",
        "щ", "ш", "ч", "ц", "ю", "я", "ж", "а", "б", "в",
        "г", "д", "е", "ё", "з", "и", "й", "к", "л", "м", "н",
        "о", "п", "р", "с", "т", "у", "ф", "х", "ь", "ы", "ъ",
        "э", "є", "ї", "і"
    );
    $lat = array(
        "Shch", "Sh", "Ch", "C", "Yu", "Ya", "J", "A", "B", "V",
        "G", "D", "e", "e", "Z", "I", "y", "K", "L", "M", "N",
        "O", "P", "R", "S", "T", "U", "F", "H", "",
        "Y", "", "E", "E", "Yi", "I",
        "shch", "sh", "ch", "c", "yu", "ya", "j", "a", "b", "v",
        "g", "d", "e", "e", "z", "i", "y", "k", "l", "m", "n",
        "o", "p", "r", "s", "t", "u", "f", "h",
        "", "y", "", "e", "e", "yi", "i"
    );
    for ($i = 0; $i < count($cyr); $i++) {
        $c_cyr = $cyr[$i];
        $c_lat = $lat[$i];
        $string = str_replace($c_cyr, $c_lat, $string);
    }
    $string = preg_replace(
            "/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/", "\${1}e", $string);
    $string = preg_replace(
            "/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/", "\${1}'", $string);
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
function encodestring($string) {
    $string = str_replace(array(" ", "\"", "&", "<", ">"), array(" "), $string);
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
$validImageTypes = array("image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/x-png");

/**
 * Move uploaded image with resize if needed
 *
 * @param array $src_file Input file strcture
 * @param string $dst_file Destination file name
 * @param integer $max_width Maximum width to resize
 *
 * @return boolean True if success
 */
function move_uploaded_image($src_file, $dst_file, $max_width = 0, $max_height = 0, $fix_width = 0, $fix_height = 0) {
    global $settings, $validImageTypes;
    if (!is_file($src_file["tmp_name"])) {
        print_error("Файл отсутствует !");
        return false;
    }
    if (!in_array($src_file['type'], $validImageTypes)) {
        print_error("Неверный тип файла !");
        return false;
    }
//	print_array($src_file);
    unset($src);
    if (($src_file["type"] == 'image/jpeg') || ($src_file["type"] == 'image/pjpeg')) {
        $src = imagecreatefromjpeg($src_file["tmp_name"]);
        $ftype = 'jpeg';
    } elseif (($src_file["type"] == 'image/png') || ($src_file["type"] == 'image/x-png')) {
        $src = imagecreatefrompng($src_file['tmp_name']);
        $ftype = 'png';
    }

    if ($src && $fix_width && $fix_height) {
        list($src_width, $src_height) = getimagesize($src_file['tmp_name']);
        if (($src_width !== $fix_width) || ($src_height !== $fix_height)) {
            $dst = imagecreatetruecolor($fix_width, $fix_height);
            if ($ftype == 'png') {
                $alpha = imagecolorallocatealpha($src, 255, 255, 255, 127);
                if ($alpha) {
                    imagecolortransparent($dst, $alpha);
                    imagefill($dst, 0, 0, $alpha);
                }
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $fix_width, $fix_height, $src_width, $src_height);
            if ($ftype == 'jpeg') {
                imagejpeg($dst, $dst_file, 100);
            } else {
                imagepng($dst, $dst_file, 0);
            }
            return is_file($dst_file);
        }
    } elseif (($src && $max_width) || ($src && $max_height)) {
        list($src_width, $src_height) = getimagesize($src_file['tmp_name']);
        $do_resize = false;
        if (($max_width > 0) && (!$max_height > 0) && (($src_width > $max_width) || ($src_height > $max_width))) {
            $width = $max_width;
            $height = $max_width;
            if ($src_width < $src_height) {
                $width = ($max_width / $src_height) * $src_width;
            } else {
                $height = ($max_width / $src_width) * $src_height;
            }
            $do_resize = true;
        } else if ($max_height && $src_height > $max_height) {
            $height = $max_height;
            $width = ($max_height / $src_height) * $src_width;
            $do_resize = true;
        }
        if ($do_resize) {
            $dst = imagecreatetruecolor($width, $height);
            if ($ftype == 'png') {
                $alpha = imagecolorallocatealpha($src, 255, 255, 255, 127);
                if ($alpha) {
                    imagecolortransparent($dst, $alpha);
                    imagefill($dst, 0, 0, $alpha);
                }
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
            if ($ftype == 'jpeg') {
                imagejpeg($dst, $dst_file, 100);
            } else {
                imagepng($dst, $dst_file, 0);
            }
            return is_file($dst_file);
        }
    }
    return move_uploaded_file($src_file['tmp_name'], $dst_file);
}

/**
 * Print calendar
 *
 * @param integer $month Month number
 * @param callable $show_day_func Function for customize day output
 *
 */
function show_month($month, $show_day_func = null) {
    $month_names = array(1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель', 5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь');
    $day_names = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
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
            if(is_callable($show_day_func)) {
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
 * Generate salt for user account
 *
 * @return string Generated salt
 */
function user_generate_salt() {
    $salt = '';
    for ($i = 0; $i < 22; $i++) {
        do {
            $chr = rand(48, 122);
        } while (in_array($chr, range(58, 64)) or in_array($chr, range(91, 96)));

        $salt .= chr($chr);
    }
    return $salt;
}

/**
 * Get salt for user account
 *
 * @param integer $uid User ID
 *
 * @return string User's salt
 */
function user_get_salt($uid) {
    list($salt) = my_select_row("SELECT salt FROM users WHERE id='{$uid}'");
    return $salt;
}

/**
 * Encrypt input password with salt
 *
 * @param string $passwd Input password
 * @param string $salt Input salt
 *
 * @return string Generated hash
 */
function user_encrypt_password($passwd, $salt) {
    if (mb_strlen($salt) === 22) {
        return crypt($passwd, '$2a$13$' . $salt);
    } else {
        return md5($passwd);
    }
}

/**
 * Return CSRF token value from session. 
 *
 * @return string Output string
 */
function get_csrf_token() {
    global $_SESSION;
    if(!check_key('CSRF_Token',$_SESSION)) {
        $token = user_encrypt_password(user_generate_salt(), user_generate_salt());
        $_SESSION['CSRF_Token'] = $token;
    }
    return $_SESSION['CSRF_Token'];
}

/**
 * Compare CSRF token from session and input. 
 *
 * @return string Output string
 */
function check_csrf_token() {
    global $input, $_SESSION;
    return $input['CSRF_Token'] === $_SESSION['CSRF_Token'];
}


/**
 * Return block content (for Twig templates)
 *
 * @param string $name Block name
 *
 * @return string Output string
 */
function get_block($name) {
    return MyGlobal::get('Blocks')->content($name);
}

function include_php($file_name) {
    $DIR =  MyGlobal::get('DIR');
    if(is_file($DIR . $file_name)) {
        ob_start();
        include_once($DIR . $file_name);
        $content = ob_get_clean();
    } else {
        $content = my_msg('error', [], 'Файл ' . $file_name . ' не найден !');
    }
    return $content;
}

/**
 * Return value from MyGlobal object (for Twig templates)
 *
 * @param string $key Value key
 *
 * @return string Value
 */
function myglobal($key) {
    return MyGlobal::get($key);
}


/**
 * Recursively delete FS tree
 *
 * @param string $dir Directory to remove
 *
 * @return bool True if complete
 */
function del_tree($dir) {
    if(!file_exists($dir)) {
        return true;
    }
    $files = array_diff(scandir($dir), array('.', '..'));
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
function clear_cache_dir($subdir = '') {
    global $DIR;
    if (strlen($subdir)) {
        return del_tree($DIR . 'var/cache/' . $subdir );
    } else {
        return del_tree($DIR . 'var/cache');
    }
}

/**
 * Decode JSON to array.
 *
 * @param string $json Input string
 *
 * @return mixed Array if complete, false if error.
 */
function my_json_decode($json) {
    if(strlen($json)) {
        $result = json_decode($json, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            add_to_debug( 'JSON decode error: ' . json_last_error_msg() . ' JSON: ' . $json);
            return false;
        }
    } else {
        return false;
    }
    return $result;
}




