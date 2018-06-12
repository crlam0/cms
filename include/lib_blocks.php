<?php

use Classes\Blocks;
use Classes\BlocksLocal;

$BlocksObject = null;

if($BlocksObject===null) {        
    
    if(file_exists($INC_DIR.'classes/BlocksLocal.php')) {
        $BlocksObject = new BlocksLocal();    
    } else {
        $BlocksObject = new Blocks();
    }
}

/**
 * Return block content
 *
 * @param string $block_name Block name
 *
 * @return string Block content
 */
function get_block($block_name) {
    global $BlocksObject;
    return $BlocksObject->content($block_name);
}


