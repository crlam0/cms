<?php

/* =========================================================================

  Misc functions

  ========================================================================= */

/**
 * Check user access
 *
 * @param string $flag Flsg title
 *
 * @return boolean true if user have access
 */
function have_flag($flag) {
    global $_SESSION;
    if (!strlen($flag))
        return 1;
    return @strstr($_SESSION['FLAGS'], $flag);
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
    if (strlen($kop) == 1)
        $kop = "0" . $kop;
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
function cut_stringing($string, $lenght) {
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
function move_uploaded_image($src_file, $dst_file, $max_width = 0) {
    global $settings, $validImageTypes;
    if (!is_file($src_file["tmp_name"])) {
        print_erroror("Файл отсутствует !");
        return false;
    }
    if (!in_array($src_file['type'], $validImageTypes)) {
        print_erroror("Неверный тип файла !");
        return false;
    }
//	print_arrayay($src_file);
    unset($src);
    if (($src_file["type"] == 'image/jpeg') || ($src_file["type"] == 'image/pjpeg')) {
        $src = imagecreatefromjpeg($src_file["tmp_name"]);
    } elseif (($src_file["type"] == 'image/png') || ($src_file["type"] == 'image/x-png')) {
        $src = imagecreatefrompng($src_file['tmp_name']);
    }
    if ($src && $max_width) {
        list($width_src, $height_src) = getimagesize($src_file["tmp_name"]);
        if (($width_src > $max_width) || ($height_src > $max_width)) {
            $width = $max_width;
            $height = $max_width;
            if ($width_src < $height_src) {
                $width = ($max_width / $height_src) * $width_src;
            } else {
                $height = ($max_width / $width_src) * $height_src;
            }
            $dst = imagecreatetruecolor($width, $height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $width_src, $height_src);
            @imagejpeg($dst, $dst_file, 100);
            return is_file($dst_file);
        }
    }
    return move_uploaded_file($src_file["tmp_name"], $dst_file);
}

/**
 * Redirect to $url
 *
 * @param string $url
 *
 */
function redirect($url) {
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
  header('Status: 301 Moved Permanently', false, 301);

  die($content);
}

/**
 * Print calendar
 *
 * @param integer $month Month number
 * @param integer $service_id Service ID
 *
 */
function show_month($month, $service_id) {
    global $_SESSION, $server;
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
            $minulost = (date('Ymd') >= date('Ymd', $i + 86400)) && !$allow_past;
            echo '<div class="day' . $today . '">';
            $day = date('j', $i);
//             if ((date('Ymd', $i)>date('Ymd')) ) {
//                echo "<a href=\"" . $server["PHP_SELF"] . "?sel_service=" . $service_id . "&sel_day=" . date('Y-m-d', $i) . "\">$day</a>";
//            } else {
            echo date('j', $i);
//            }
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

/* =========================================================================

  Menu functions

  ========================================================================= */

/**
 * Get HREF for article list
 *
 * @param integer $list_id List ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_article_list_href($list_id, $row = array()) {
    if ($row['id']){
        $list_id = $row['id'];
    }
    $query = "SELECT seo_alias FROM article_list WHERE id='{$list_id}'";
    $result = my_query($query, true);
    list($seo_alias) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return 'article/' . $seo_alias . '/';
    } else {
        return 'article/?view_items=' . $list_id;
    }
}

/**
 * Get HREF for article
 *
 * @param integer $article_id Article ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_article_href($article_id, $row = array()) {
    if ($row['id']){
        $article_id = $row['id'];
    }
    $query = "SELECT seo_alias,list_id FROM article_item WHERE id='{$article_id}'";
    $result = my_query($query, true);
    list($seo_alias, $list_id) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return get_article_list_href($list_id) . $seo_alias . '/';
    } else {
        return 'article/?view=' . $article_id;
    }
}

/**
 * Get HREF for media list
 *
 * @param integer $list_id List ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_media_list_href($list_id, $row = array()) {
    if ($row['id']){
        $list_id = $row['id'];
    }
    $query = "SELECT seo_alias FROM media_list WHERE id='{$list_id}'";
    $result = my_query($query, true);
    list($seo_alias) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return 'media/' . $seo_alias . "/";
    } else {
        return 'media/index.php?view_files=1&id=' . $list_id;
    }
}

/**
 * Get previous part array
 *
 * @param integer $prev_id Previous ID
 * @param integer $deep Deep
 * @param array $array Input Array
 *
 * @return array Output array
 */
function cat_prev_part($prev_id, $deep, $array) {
    $query = "SELECT id,title,prev_id,seo_alias FROM cat_part WHERE id='{$prev_id}' order by title asc";
    $result = my_query($query);
    $array[$deep] = $result->fetch_array();
    if ($array[$deep]['prev_id']){
        $array = cat_prev_part($array[$deep]['prev_id'], $deep + 1, $array);
    }
    return $array;
}

/**
 * Get HREF for catalog part
 *
 * @param integer $part_id Part ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_cat_part_href($part_id, $row = array()) {
    if (is_numeric($row['id'])) {
        $part_id = $row['id'];
    }
    $uri = 'catalog/';
    if ($part_id) {
        $array = cat_prev_part($part_id, 0, $array);
        $array = array_reverse($array);
        while (list ($n, $row) = @each($array)) {
            $uri.=$row['seo_alias'] . '/';
        }
    }
    return $uri;
}

/**
 * Get HREF for gallery list
 *
 * @param integer $list_id List ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_gallery_list_href($list_id, $row = array()) {
    if ($row['id']){
        $list_id = $row['id'];
    }
    $query = "SELECT seo_alias FROM gallery_list WHERE id='{$list_id}'";
    $result = my_query($query, true);
    list($seo_alias) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return 'gallery/' . $seo_alias . '/';
    } else {
        return 'gallery/index.php?view_gallery=1&id=' . $list_id;
    }
}

/**
 * Get HREF for blog post
 *
 * @param integer $tmp Unused
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_post_href($tmp, $row) {
    if (strlen($row['seo_alias'])) {
        return "blog/" . $row['seo_alias'] . "/";
    } else {
        return "blog/" . "?view_post=" . $row['id'];
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
function get_menu_href($tmp, $row) {
    switch ($row["target_type"]) {
        case "":
            return $row["href"];
        case "link":
            return $row["href"];
        case "article_list":
            return get_article_list_href($row["target_id"]);
        case "article":
            return get_article_href($row["target_id"]);
        case "media_list":
            return get_media_list_href($row["target_id"]);
        case "cat_part":
            return get_cat_part_href($row["target_id"]);
        case "gallery_list":
            return get_gallery_list_href($row["target_id"]);
    }
}

/**
 * Replace BASE_HREF in content
 *
 * @param string $content Content string
 * @param boolean $direction Direction of replace
 *
 * @return string Output string
 */
function replace_base_href($content, $direction = false) {
    global $server, $SUBDIR;
    if ($direction) {
        return str_replace("http://" . $server["HTTP_HOST"] . $SUBDIR, "[%SUBDIR%]", $content);
    } else {
        return str_replace("[%SUBDIR%]", "http://" . $server["HTTP_HOST"] . $SUBDIR, $content);
    }
}
