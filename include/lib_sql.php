<?php

/* =========================================================================

  SQL library

  ========================================================================= */

use Classes\SQLHelper;

/**
 * @var Classes\SQLHelper
 */
// $DB;

if(!$DB) {
    $DB = new SQLHelper($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
}

$mysqli=$DB->mysqli;

/**
 * @var Array Debug array of all SQL query
 */
empty($DEBUG['sql_query_array']);

/**
 * Test field marameter for deny sql injections
 *
 * @param string $sql Input string
 *
 * @return string Output string
 */
function db_test_param($str,$param='') {
    global $DB;
    return $DB->test_param($str,$param);
}

/**
 * Replace for mysql_query
 *
 * @param string $sql SQL Query
 * @param boolean $dont_debug Dont echo debug info
 *
 * @return array mysqli result
 */
function my_query($sql, $dont_debug=false) {
    global $DB;
    return $DB->query($sql, $dont_debug);
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
    global $DB;
    return $DB->select_row($sql, $dont_debug);    
}

/**
 * Return string for insert query
 *
 * @param array $fields Fields and data for query
 *
 * @return string Complete string for query
 */

function db_insert_fields($fields) {
    global $DB;
    return $DB->insert_fields($fields);
}

/**
 * Return string for update query
 *
 * @param array $fields Fields and data for query
 *
 * @return string Complete string for query
 */

function db_update_fields($fields) {
    global $DB;
    return $DB->update_fields($fields);
}

$DB->query('SET character_set_client = utf8', true);
$DB->query('SET character_set_results = utf8', true);
$DB->query('SET character_set_connection = utf8', true);

