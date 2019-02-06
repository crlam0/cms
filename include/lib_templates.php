<?php

use Classes\Template;
use Classes\MyGlobal;
use Classes\Blocks;
use Classes\BlocksLocal;

if(file_exists($INC_DIR.'classes/BlocksLocal.php')) {
    $BlocksObject = new BlocksLocal();    
} else {
    $BlocksObject = new Blocks();
}
MyGlobal::set('Blocks', $BlocksObject );

$Template = new Template($BlocksObject);
MyGlobal::set('Template', $Template );


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
function get_tpl_by_title($title, $tags = [], $sql_result = [], $inner_content = '') {
    global $Template;
    return $Template->get_tpl_by_title($title, $tags, $sql_result, $inner_content);
}

/**
 * Parse default template
 *
 * @param array $tags Tags array
 * @param array $sql_result Result from SQL query
 * @param string $inner_content Inner content
 *
 * @return string Output content
 */
function get_tpl_default($tags = [], $sql_result = [], $inner_content = '') {
    global $Template;
    return $Template->get_tpl_by_title(MyGlobal::get('tpl_default'), $tags, $sql_result, $inner_content);
}
