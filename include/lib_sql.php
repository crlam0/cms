<?php

/* =========================================================================

  SQL library

  ========================================================================= */

use classes\App;

/**
 * Replace for mysql_query
 *
 * @param string $sql SQL Query
 * @param boolean $dont_debug Dont echo debug info
 *
 * @return array mysqli result
 */
function my_query($sql, $dont_debug = false)
{
    return App::$db->query($sql);
}

/**
 * Return one row from query
 *
 * @param string $sql SQL Query
 * @param boolean $dont_debug Dont echo debug info
 *
 * @return array One row
 */
function my_select_row($sql, $dont_debug = false)
{
    return App::$db->getRow($sql);
}

/**
 * Return string for insert query
 *
 * @param array $fields Fields and data for query
 *
 * @return int|string Complete string for query
 */
function db_insert_fields($fields)
{
    return App::$db->insertFields($fields);
}

/**
 * Return string for update query
 *
 * @param array $fields Fields and data for query
 *
 * @return string Complete string for query
 */

function db_update_fields($fields)
{
    return App::$db->updateFields($fields);
}
