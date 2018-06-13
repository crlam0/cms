<?php

use Classes\Blocks;
use Classes\BlocksLocal;
use Classes\MyTemplate;

if(!$BlocksObject) {    
    if(file_exists($INC_DIR.'classes/BlocksLocal.php')) {
        $BlocksObject = new BlocksLocal();    
    } else {
        $BlocksObject = new Blocks();
    }
}
if(!$MyTemplate) {
    $MyTemplate = new MyTemplate($BlocksObject);
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
    global $MyTemplate,$server, $settings, $DIR, $DEBUG;

    if (file_exists(dirname($server['SCRIPT_FILENAME']) . '/templates.tpl')) {
        $temp = $MyTemplate->load_from_file(dirname($server['SCRIPT_FILENAME']) . '/templates.tpl', $title);
        if ($temp) {
            $template['content'] = $temp;
            $template['do_parse'] = true;
        }
    }
    if(strstr($title,'.tpl')) {
        $template['file_name']=$title;
        $template['do_parse'] = true;
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
        if (file_exists($template['file_name'])) {
            $fname = $template['file_name'];
        }    
        if (file_exists($DIR . $template['file_name'])) {
            $fname = $DIR . $template['file_name'];
        }    
        if ($fname) {
            $template['content'] = implode('', file($fname));
        } else {
            $tags['file_name'] = $template['file_name'];
            my_msg('file_not_found', $tags);
            return '';
        }
    }
    if ((!$template['do_parse']) || (!strstr($template['content'], '[%'))) {
        return($template['content']);
    }
    add_to_debug("Parse template '{$title}'");
    return $MyTemplate->parse($template['content'], $tags, $sql_result, $inner_content);
}

