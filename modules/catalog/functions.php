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
    return $max_width;
}

function get_cache_file_name($file_name, $max_width) {
    return 'var/cache/catalog/' . md5($file_name.$max_width) . '.jpeg';
}

function get_prop_value($row,$name) {
    $props_values = [];
    if(strlen($row['props'])) {
        $props_values=json_decode($row['props'], true);
        if(json_last_error() != JSON_ERROR_NONE) {
            print_debug(json_last_error_msg() . ' JSON: ' . $row['props']);
            return false;
        }
        // echo $row['id'] . ': ' . $name .': '. $props_values[$name] . ' rere '. strlen($props_values[$name]) .'<br />';
        $result = $props_values[$name];
        
        return strlen($result)>0 ? $result : false;
    }
    return false;
}

function get_props_array($row) {
    $props_values = [];
    if(strlen($row['props'])) {
        $props_values=json_decode($row['props'], true);
        if(json_last_error() != JSON_ERROR_NONE) {
            print_debug(json_last_error_msg() . ' JSON: ' . $row['props']);
            return false;
        }
        // echo $row['id'] . ': ' . $name .': '. $props_values[$name] . ' rere '. strlen($props_values[$name]) .'<br />';
        
        if(is_array($props_values)) {
            return $props_values;
        } else {
            return false;
        }
    }
    return false;
}


function get_prop_name($row,$name) {
    $query = "select items_props from cat_part where id='{$row['part_id']}'";
    list($items_props) = my_select_row($query, true);
    if(strlen($items_props)) {
        $props_values=json_decode($items_props, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            print_debug(json_last_error_msg() . ' JSON: ' . $items_props);
            return false;
        }
        // echo $row['id'] . ': ' . $name .': '. $props_values[$name] .'<br />';
        return $props_values[$name]['name'];
    }
    return false;
    
}



