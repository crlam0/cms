<?php

/*
  Template usage and parsing library.

  to tpl_parse_tag:
  [%title%] - replace with tag "title"
  [%func(title,param)%] - replace with result of function title(param)
  [%var(title)%] - replace with variable $title
  [%include(title)%] - include file "title"
  [%file(title)%] - parse and insert file "title"
  [%template(title)%] - load template "title"
  [%settings(title)%] - insert settings[title]
  [%inner_content%] - insert $inner_content
  [%block(title)%] - insert $blocks[title]

  to tpl_parse_loop:
  [%row(title,format)%] - get $row from $sql_result and replace with $row[title] on format "format"
  [%loop_begin%] - start of loop part
  $loop_content
  [%loop_end%] - end of loop part
  [%summ(title)%] - summ of filds title

 */

/**
 * Parse one string from template
 *
 * @param string $content Input string
 * @param array $tags Tags array
 * @param array $sql_row Row from SQL result
 * @param array $sql_row_summ Array of summ
 * @param string $inner_content Inner content
 *
 * @return string Output string
 */
function tpl_parse_string($content, $tags = array(), $sql_row = array(), $sql_row_summ = array(), $inner_content = '') {
    global $input, $settings, $_SESSION, $server, $blocks, $conn, $DIR, $LEFT, $RIGHT, $SUBDIR;
    preg_match_all("@\[\%(.*?)\%\]@", $content, $temp, PREG_SET_ORDER);
    $total = count($temp);
    $a = 0;
    unset($replace_str);
    if ($total)
        while ($temp[$a]) {
            $replace_str = '';
            list($tagclass, $tagparam) = explode('(', $temp[$a][1], 2);
            if (strlen($tagparam))
                $tagparam = str_replace(')', '', $tagparam);
            //echo "Tag: ".$temp[$a][1]." Class: $tagclass Func: $tagparam <br>";
            if ($tagclass == "func") {
                if (strstr($tagparam, ',')) {
                    $param = explode(',', $tagparam);
                } else {
                    $param[0] = $tagparam;
                }
                eval("\$replace_str=\$param[0](\$param[1],\$sql_row);");
            } elseif ($tagclass == "var") {
                eval("\$replace_str=\"\$" . $tagparam . "\";");
            } elseif ($tagclass == 'settings') {
                $replace_str = $settings[$tagparam];
            } elseif ($tagclass == 'row') {
                if (strstr($tagparam, ',')) {
                    $param = explode(',', $tagparam);
                } else {
                    $param[0] = $tagparam;
                    $param[1] = '';
                }
                if ($param[1] == 'nl2br') {
                    $sql_row[$param[0]] = str_replace("\\r\\n", "<br>", $sql_row[$param[0]]);
                    $replace_str = nl2br($sql_row[$param[0]]) . '';
                } elseif ($param[1] == 'yes_no') {
                    $replace_str = (($sql_row[$param[0]] == '1') || (($sql_row[$param[0]] == 'Y')) ? 'Да' : 'Нет') . '';
                } elseif ($param[1] == 'if') {
                    $replace_str = ( ($sql_row[$param[0]] == '1') || ($sql_row[$param[0]] == 'Y') ||
                            (strlen($sql_row[$param[0]])) && $sql_row[$param[0]] != 'N' ? $param[2] : $param[3]) . '';
                } else {
                    $replace_str = $sql_row[$param[0]] . "";
                }
            } elseif ($tagclass == 'summ') {
                $replace_str = $sql_row_summ[$tagparam] . '';
            } elseif ($tagclass == 'include') {
                if (file_exists($tagparam))
                    $fname = $tagparam;
                if (file_exists($DIR . $tagparam))
                    $fname = $DIR . $tagparam;
                if (strlen($fname)) {
                    ob_start();
                    include_once($fname);
                    $replace_str = ob_get_contents();
                    ob_end_clean();
                } else {
                    $tags[file_name] = $tagparam;
                    my_msg('file_not_found', $tags);
                }
            } elseif ($tagclass == 'file') {
                if (file_exists($tagparam)) {
                    $replace_str = implode("", file($tagparam));
                } else {
                    $tags[file_name] = $tagparam;
                    my_msg('file_not_found', $tags);
                    return '';
                }
            } elseif ($tagclass == 'template') {
                $replace_str = get_tpl_by_title($tagparam);
                if (!$replace_str) {
                    $tags[title] = $tagparam;
                    my_msg('tpl_not_found', $tags);
                    return "";
                }
            } elseif ($tagclass == 'block') {
                // $replace_str=$blocks[$tagparam];
                $replace_str = get_block($tagparam);
            } elseif (($tagclass == 'inner_content') && (strlen($inner_content))) {
                $replace_str = $inner_content;
            } elseif (isset($tags[$tagclass])) {
                $replace_str = $tags[$tagclass];
            }
            if (isset($replace_str))
                $content = str_replace('[%' . $temp[$a][1] . '%]', $replace_str, $content);
            $a++;
        }
    return $content;
}

/**
 * Parse template
 *
 * @param string $content Input template
 * @param array $tags Tags array
 * @param array $sql_result Result from SQL query
 * @param string $inner_content Inner content
 *
 * @return string Output content
 */
function tpl_parse($content, $tags = array(), $sql_result = array(), $inner_content = '') {
    global $server, $BASE_HREF, $SUBDIR;
    $tags[PHP_SELF] = $server['PHP_SELF'];
    $tags[PHP_SELF_DIR] = $server['PHP_SELF_DIR'];
    $tags[BASE_HREF] = $BASE_HREF;
    $tags[SUBDIR] = $SUBDIR;
    //foreach ($tags as $key => $value) { $result=str_replace("[%".$key."%]",$value,$result); }
    $strings = explode("\n", $content);
    $loop_start = 0;
    $loop_content = '';
    $result = '';
    foreach ($strings as $key => $value) {
        if (strstr($value, '[%loop_begin%]')) {
            $loop_start = 1;
        } elseif (strstr($value, '[%loop_end%]')) {
            unset($mysql_row_summ);
            if ($sql_result)
                while ($row = $sql_result->fetch_array()) {
                    $result.=tpl_parse_string($loop_content, $tags, $row, $inner_content) . "\n";
                    foreach ($row as $key => $value)
                        if ((is_double($value))or ( is_numeric($value)))
                            $mysql_row_summ[$key] = +$value;
                }
            $loop_start = 0;
        }elseif ($loop_start) {
            $loop_content.=$value;
        } else {
            $result.=tpl_parse_string($value, $tags, $sql_result, $mysql_row_summ, $inner_content) . "\n";
        }
    }
    return $result;
}

/**
 * Load template from templates file
 *
 * @param string $file_name Name of tamplate file
 * @param string $title Template's title
 *
 * @return string Output template
 */
function load_tpl_from_file($file_name, $title) {
    $tpl_file = @file($file_name);
    if ($tpl_file == false) {
        echo 'Error open';
        exit;
    }
    $tpl_started = false;
    $tpl_title = '';
    $tpl_content = '';
    for ($line = 0; $line < sizeof($tpl_file); $line++) {
        if (preg_match("@<!--\[title\](.*?)\[/title\]-->@", $tpl_file[$line], $temp)) {
            $tpl_title = $temp[1];
        } elseif (preg_match("@<!--\[content\]-->@", $tpl_file[$line])) {
            $tpl_started = true;
            $tpl_content = '';
        } elseif (preg_match("@<!--\[/content\]-->@", $tpl_file[$line])) {
            $tpl_started = false;
            $templates[$tpl_title] = $tpl_content;
        } elseif ($tpl_started) {
            $tpl_content.=$tpl_file[$line];
        }
    }
    return $templates[$title];
}

/**
 * Parse template by title
 *
 * @param string $title Template's title
 * @param array $tags Tags array
 * @param array $sql_result Result from SQL query
 * @param string $inner_content Inner content
 *
 * @return string Output content
 */
function get_tpl_by_title($title, $tags = array(), $sql_result = array(), $inner_content = '') {
    global $server, $settings, $DIR;
    if (file_exists(dirname($server['SCRIPT_FILENAME']) . '/templates.tpl')) {
        $temp = load_tpl_from_file(dirname($server['SCRIPT_FILENAME']) . '/templates.tpl', $title);
        if ($temp) {
            $template['content'] = $temp;
            $template['do_parse'] = 1;
        }
    }
    if (!$template) {
        $template = my_select_row("SELECT * FROM templates WHERE title='$title' AND '" . $server["REQUEST_URI"] . "' LIKE concat('%',uri,'%')", true);
    }
    if (!$template) {
        $template = my_select_row("SELECT * FROM templates WHERE title='$title'", true);
    }
    if (!$template) {
        $tags['title'] = $title;
        my_msg('tpl_not_found', $tags);
        return '';
    }
    if ($template['file_name']) {
        $fname = '';
        if (file_exists($template[file_name]))
            $fname = $template[file_name];
        if (file_exists($DIR . $template[file_name]))
            $fname = $DIR . $template[file_name];
        if ($fname) {
            $template[content] = implode('', file($fname));
        } else {
            $tags[file_name] = $template[file_name];
            my_msg('file_not_found', $tags);
            return '';
        }
    }
    if ((!$template[do_parse]) || (!strstr($template[content], '[%'))) {
        return($template[content]);
    }
    return tpl_parse($template[content], $tags, $sql_result, $inner_content);
}

?>