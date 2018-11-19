<?php

function get_max_width() {
    global $input, $settings;
    
    if ($input['preview']) {
        $max_width = $settings['gallery_max_width_preview'];
    } else {
        $max_width = $settings['gallery_max_width'];
    }
    if ($input['icon'] && $settings['gallery_icon_width']) {
        $max_width = $settings['gallery_icon_width'];
    }
    if ($input['width'] && is_integer($input['width'])) {
        $max_width = $input['width'];
    }
    
    return $max_width;
}

function get_cache_file_name($file_name, $max_width) {
    return 'var/cache/gallery/' . md5($file_name.$max_width) . '.jpeg';
}