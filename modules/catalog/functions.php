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

function get_props_array($props) {
    $props_values = [];
    if(strlen($props)) {
        $props_values=json_decode($props, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            print_debug(json_last_error_msg() . ' JSON: ' . $props);
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


function get_prop_name($part_id,$name) {
    $query = "select items_props from cat_part where id='{$part_id}'";
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

function get_prop_names_array($part_id) {
    $query = "select items_props from cat_part where id='{$part_id}'";
    list($items_props) = my_select_row($query, true);
    if(strlen($items_props)) {
        $props_values=json_decode($items_props, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            print_debug(json_last_error_msg() . ' JSON: ' . $items_props);
            return false;
        }
        // echo $row['id'] . ': ' . $name .': '. $props_values[$name] .'<br />';
        $result=[];
        foreach($props_values as $name){
            // $result[$name]=$props_values[$name]['name'];
        }
        return $result;
    }
    return false;
    
}

function get_item_image_url($file_name, $width, $fix_size=1) {
    global $DIR, $IMG_ITEM_PATH;
    $cache_file_name = get_cache_file_name($IMG_ITEM_PATH . $file_name, $width);
    if(is_file($DIR . $cache_file_name)) {
        return $cache_file_name;
    } else {
        return "modules/catalog/image.php?file_name={$file_name}&preview={$width}&fix_size={$fix_size}";
    }
}

function get_item_image_filename($fname, $width = 0) {
    global $IMG_ITEM_PATH, $settings;
    if(!$width) {
        $width = $settings['catalog_item_img_preview'];
    }        
    if (is_file($IMG_ITEM_PATH . $fname)) {
        return get_item_image_url($fname, $width);
    } else {
        return false;
    }
}

function get_part_image_filename($fname, $width = 0) {
    global $IMG_PART_PATH, $settings;
    if(!$width) {
        $width = $settings['catalog_part_img_preview'];
    }        
    if (is_file($IMG_PART_PATH . $fname)) {
        return $settings['catalog_part_img_path'] . $fname;
    } else {
        return false;
    }
}

