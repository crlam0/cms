<?php

use Classes\Template;

if(!$Template) {
    $Template = new Template();
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
function get_tpl_by_title($title, $tags = [], $sql_result = [], $inner_content = '') {
    global $Template;
    return $Template->get_tpl_by_title($title, $tags, $sql_result, $inner_content);   
    
}

