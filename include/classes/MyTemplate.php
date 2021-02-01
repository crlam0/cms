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

namespace classes;

use classes\App;

class MyTemplate
{


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
    private function parseString(string $content, array $tags = [], $sql_row = [], $sql_row_summ = [], string $inner_content = '') : string
    {
        preg_match_all("@\[\%(.*?)\%\]@", $content, $temp, PREG_SET_ORDER);
        $total = count($temp);
        $a = 0;
        unset($replace_str);
        if ($total) {
            // print_array($temp);
            // exit();

            while ($temp[$a]) {
                $replace_str = '';
                // if(array_key_exists(1,$temp[$a])) {
                if (strstr($temp[$a][1], '(')) {
                    list($tagclass, $tagparam) = explode('(', $temp[$a][1], 2);
                    $tagparam = str_replace(')', '', $tagparam);
                } else {
                    $tagclass = $temp[$a][1];
                    $tagparam = '';
                }
//                if (strlen($tagparam)){
//                }
                //echo "Tag: ".$temp[$a][1]." Class: $tagclass Func: $tagparam <br>";
                if ($tagclass == "func") {
                    if (strstr($tagparam, ',')) {
                        $param = explode(',', $tagparam);
                    } else {
                        $param[0] = $tagparam;
                        $param[1] = "''";
                    }
                    eval("\$replace_str=\$param[0](\$param[1],\$sql_row);");
                } elseif ($tagclass == "var") {
                    eval("\$replace_str=\"\$" . $tagparam . "\";");
                } elseif ($tagclass == 'settings') {
                    isset(App::$settings[$tagparam]) ? $replace_str = App::$settings[$tagparam] : $replace_str = '';
                } elseif ($tagclass == 'row') {
                    if (strstr($tagparam, ',')) {
                        $param = explode(',', $tagparam);
                    } else {
                        $param[0] = $tagparam;
                        $param[1] = '';
                    }
                    if ($param[1] == 'nl2br' && array_key_exists($param[0], $sql_row)) {
                        $sql_row[$param[0]] = str_replace("\\r\\n", "<br>", $sql_row[$param[0]]);
                        $replace_str = nl2br($sql_row[$param[0]]) . '';
                    } elseif ($param[1] == 'yes_no') {
                        $replace_str = (($sql_row[$param[0]] == '1') || (($sql_row[$param[0]] == 'Y')) ? 'Да' : 'Нет') . '';
                    } elseif ($param[1] == 'if') {
                        if (!isset($param[3])) {
                            $param[3] = '';
                        }
                        if (($sql_row[$param[0]] == '1') || ($sql_row[$param[0]] == 'Y') ||
                                (strlen($sql_row[$param[0]])) && $sql_row[$param[0]] != 'N') {
                            $replace_str = $param[2];
                        } else {
                            $replace_str = $param[3];
                        }
                    } elseif (array_key_exists($param[0], $sql_row)) {
                        $replace_str = $sql_row[$param[0]] . "";
                    } else {
                        $replace_str = '';
                    }
                } elseif ($tagclass == 'summ') {
                    $replace_str = $sql_row_summ[$tagparam] . '';
                } elseif ($tagclass == 'include') {
                    if (file_exists($tagparam)) {
                        $fname = $tagparam;
                    }
                    if (file_exists(App::$DIR . $tagparam)) {
                        $fname = App::$DIR . $tagparam;
                    }
                    if (isset($fname) && strlen($fname)) {
                        ob_start();
                        include_once($fname);
                        $replace_str = ob_get_contents();
                        ob_end_clean();
                    } else {
                        App::$message->get('file_not_found', ['file_name' => $tagparam]);
                    }
                } elseif ($tagclass == 'file') {
                    if (file_exists($tagparam)) {
                        $replace_str = implode("", file($tagparam));
                    } else {
                        App::$message->get('file_not_found', ['file_name' => $tagparam]);
                        return '';
                    }
                } elseif ($tagclass == 'template') {
                    $replace_str = App::$template->parse($tagparam, $tags, null, $inner_content);
                    if (!$replace_str) {
                        App::$message->get('tpl_not_found', ['name' => $tagparam]);
                        return '';
                    }
                } elseif ($tagclass == 'block') {
                    $replace_str = App::get('Blocks')->content($tagparam);
                } elseif (($tagclass == 'inner_content') && (strlen($inner_content))) {
                    $replace_str = $inner_content;
                } elseif (isset($tags[$tagclass])) {
                    $replace_str = $tags[$tagclass];
                }
                if (isset($replace_str)) {
                    $content = str_replace('[%' . $temp[$a][1] . '%]', $replace_str, $content);
                }
                $a++;
                if (!array_key_exists($a, $temp)) {
                    break;
                }
            }
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
    public function parse(string $content, array $tags = [], $sql_result = [], $inner_content = '')
    {
        $tags['PHP_SELF'] = App::$server['PHP_SELF'];
        $tags['PHP_SELF_DIR'] = App::$server['PHP_SELF_DIR'];
        $tags['BASE_HREF'] = App::$SUBDIR;
        $tags['SUBDIR'] = App::$SUBDIR;
        //foreach ($tags as $key => $value) { $result=str_replace("[%".$key."%]",$value,$result); }
        $strings = explode("\n", $content);
        $loop_start = 0;
        $loop_content = '';
        $mysql_row_summ = null;
        $result = '';
        foreach ($strings as $key => $value) {
            if (strstr($value, '[%loop_begin%]')) {
                $loop_start = 1;
            } elseif (strstr($value, '[%loop_end%]')) {
                unset($mysql_row_summ);
                if ($sql_result) {
                    while ($row = $sql_result->fetch_array()) {
                        $result.=$this->parseString($loop_content, $tags, $row, $inner_content) . "\n";
                        foreach ($row as $key => $value) {
                            if ((is_double($value))or ( is_numeric($value))) {
                                $mysql_row_summ[$key] = +$value;
                            }
                        }
                    }
                }
                $loop_start = 0;
            } elseif ($loop_start) {
                $loop_content.=$value;
            } elseif (isset($mysql_row_summ)) {
                $result.=$this->parseString($value, $tags, $sql_result, $mysql_row_summ, $inner_content) . "\n";
            } else {
                $result.=$this->parseString($value, $tags, $sql_result, [], $inner_content) . "\n";
            }
        }
        return $result;
    }

    /**
     * Load template from templates file
     *
     * @param string $file_name Name of template file
     * @param string $title Template's title
     *
     * @return string Output template
     */
    public function loadFromFile($file_name, $title)
    {
        $tpl_file = @file($file_name);
        if ($tpl_file == false) {
            echo 'Error open ' . $file_name;
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
        if (array_key_exists($title, $templates)) {
            return $templates[$title];
        } else {
            return false;
        }
    }
}
