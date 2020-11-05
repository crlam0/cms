<?php

use classes\App;
use classes\Blocks;

if(class_exists('\local\Blocks')) {
    $BlocksObject = new \local\Blocks();    
} else {
    $BlocksObject = new Blocks();
}

App::set('Blocks', $BlocksObject);

/**
 * Parse template by name
 *
 * @param string $name Template's name
 * @param array $tags Tags array
 * @param array $sql_result Result from SQL query
 * @param string $inner_content Inner content
 *
 * @return string Output content
 */
function get_tpl_by_name($name, $tags = [], $sql_result = [], $inner_content = '') {
    return App::$template->parse($name, $tags, $sql_result, $inner_content);
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
    return App::$template->parse(App::get('tpl_default'), $tags, $sql_result, $inner_content);
}
