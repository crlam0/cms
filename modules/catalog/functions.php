<?php

use Classes\App;

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
    if($props_values = my_json_decode($row['props'])) {
        $result = $props_values[$name];        
        return strlen($result)>0 ? $result : false;
    }
    return false;
}

function get_props_array($props) {    
    if($props_values = my_json_decode($props)) {
        foreach($props_values as $key => $value ){
            if(!strlen($props_values[$key])) {
                unset($props_values[$key]);
            }
        }
        return $props_values;
    }
    return false;
}


function get_prop_name($part_id,$name) {
    $query = "select items_props from cat_part where id='{$part_id}'";
    list($items_props) = my_select_row($query, true);
    if($props_values = my_json_decode($items_props)) {
//        return $props_values[$name]['name'];
    }
    return false;
}

function get_prop_names_array($part_id) {
    $query = "select items_props from cat_part where id='{$part_id}'";
    list($items_props) = my_select_row($query, true);
    if($props_values = my_json_decode($items_props)) {        
        $result=[];
        foreach($props_values as $name){
            // $result[$name]=$props_values[$name]['name'];
        }
        return $result;
    }
    return false;    
}

function get_item_image_url($file_name, $width, $fix_size=1) {
    $IMG_ITEM_PATH = App::$DIR . App::$settings['catalog_item_img_path'];
    $cache_file_name = get_cache_file_name($IMG_ITEM_PATH . $file_name, $width);
    if(is_file(App::$DIR . $cache_file_name)) {
        return $cache_file_name;
    } else {
        return "modules/catalog/image.php?file_name={$file_name}&preview={$width}&fix_size={$fix_size}";
    }
}

function get_item_image_filename($fname, $width = 0) {
    $IMG_ITEM_PATH = App::$DIR . App::$settings['catalog_item_img_path'];
    if(!$width) {
        $width = App::$settings['catalog_item_img_preview'];
    }        
    if (is_file($IMG_ITEM_PATH . $fname)) {
        return get_item_image_url($fname, $width);
    } else {
        return false;
    }
}

function get_part_image_filename($fname, $width = 0) {
    $IMG_PART_PATH = App::$DIR . App::$settings['catalog_part_img_path'];
    
    if(!$width) {
        $width = App::$settings['catalog_part_img_preview'];
    }        
    if (is_file($IMG_PART_PATH . $fname)) {
        return App::$settings['catalog_part_img_path'] . $fname;
    } else {
        return false;
    }
}

function get_items_count($id) {
    global $_SESSION;
    return $_SESSION['BUY'][$id]['count'];
}

