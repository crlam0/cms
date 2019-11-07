<?php

/* =========================================================================

  SQL library

  ========================================================================= */

use Classes\App;


/**
 * Replace for mysql_query
 *
 * @param string $sql SQL Query
 * @param boolean $dont_debug Dont echo debug info
 *
 * @return array mysqli result
 */
function my_query($sql, $dont_debug=false) {
    return App::$db->query($sql, $dont_debug);
}

/**
 * Return one row from query
 *
 * @param string $sql SQL Query
 * @param boolean $dont_debug Dont echo debug info
 *
 * @return array One row
 */
function my_select_row($sql, $dont_debug=false) {
    return App::$db->select_row($sql, $dont_debug);    
}

/**
 * Return string for insert query
 *
 * @param array $fields Fields and data for query
 *
 * @return string Complete string for query
 */

function db_insert_fields($fields) {
    return App::$db->insert_fields($fields);
}

/**
 * Return string for update query
 *
 * @param array $fields Fields and data for query
 *
 * @return string Complete string for query
 */

function db_update_fields($fields) {
    return App::$db->update_fields($fields);
}

App::$db->query('SET character_set_client = utf8', true);
App::$db->query('SET character_set_results = utf8', true);
App::$db->query('SET character_set_connection = utf8', true);

