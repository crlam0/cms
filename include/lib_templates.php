<?php

use Classes\Template;
use Classes\App;
use Classes\Blocks;
use Classes\BlocksLocal;

if(file_exists(__DIR__.'/Classes/BlocksLocal.php')) {
    $BlocksObject = new BlocksLocal();    
} else {
    $BlocksObject = new Blocks();
}
App::set('Blocks', $BlocksObject);

$Template = new Template();
App::set('Template', $Template);


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
    return App::get('Template')->get_by_title($title, $tags, $sql_result, $inner_content);
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
    return App::get('Template')->get_by_title(App::get('tpl_default'), $tags, $sql_result, $inner_content);
}
