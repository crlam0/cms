<?php
if(!isset($input)) {
    require '../include/common.php';
}


$tags['INCLUDE_HEAD'] = $JQUERY_INC;

$tags['Header'] = 'Прайс-лист';

$tags['INCLUDE_CSS'] .= '<link href="' . $SUBDIR . 'css/price.css" type="text/css" rel=stylesheet />' . "\n";
$tags['nav_str'] .= "<span class=nav_next>{$tags['Header']}</span>";


if (isset($input['attr_name'])) {
    $input['id'] = intval($input['id']);
    // $input['value'] = intval($input['value']);
    if($input['attr_type'] == "simple") {
        $query = "update cat_item set {$input['attr_name']}='{$input['value']}' where id='{$input['id']}'";
        $result = my_query($query);
    } else if($input['attr_type'] == "json" || $input['attr_type'] == "boolean") {
        $query = "select props from cat_item where id='{$input['id']}'";
        $row = my_select_row($query);
        if($row){
            if(!$props_values = my_json_decode($row['props'])) {
                $props_values=[];
            }
            $props_values[$input['attr_name']] = $input['value'];
            $props_json = json_encode($props_values);
            $query = "update cat_item set props='{$props_json}' where id='{$input['id']}'";
            $result = my_query($query);
        }
    }
    if($result) {
        echo 'OK';
    } else {
        echo "Fuck";
    }
    exit();
}

function part_items($part_id) {
    $content = '';
    $query = "select cat_item.*,cat_item.id as item_id,cat_part.items_props
        from cat_item
        left join cat_part on (cat_part.id=part_id)
    where part_id='{$part_id}'
    group by cat_item.id
    order by num,title asc";
    $result = my_query($query);
    $content = '<table class="table table-striped table-responsive table-bordered">';
    if ($result->num_rows) {
        $content .= '<tr>';
        $tags = $result->fetch_array();
        $content .= '<td>Название</td>'. '<td>Базовая цена</td>' . PHP_EOL;
        if(strlen($tags['items_props'])) {
            $props_array = json_decode($tags['items_props'], true);
            // print_array($props_array);
            if(!is_array($props_array)) {
                $content.=my_msg_to_str('',[],'Массив свойств неверен');
            } else {
                $props_values=json_decode($tags['props'], true);
                // print_array($props_values);
                if(is_array($props_values)){
                    foreach ($props_values as $input_name => $value) {
                        $param_value[$input_name]=$value;
                    }
                }
                foreach ($props_array as $input_name => $params) {
                    $content .= '<td align="center">'.$params['name'].'</td>' . PHP_EOL;
                }
            }
        }
        $result->data_seek(0);
        $content .= '</tr>' . PHP_EOL;
        while ($tags = $result->fetch_array()) {
            $content .= '<tr><td width="300">'.$tags['title'].'</td>';
            $content .= '<td><input type="edit" class="form-control attr_change" maxlength="8" size="4" id="'.$tags['id'].'" attr_type="simple" attr_name="price" value="'.$tags['price'].'"></td>';
            // echo $tags['items_props'];
            if(strlen($tags['items_props'])) {
                $props_array = json_decode($tags['items_props'], true);
                // print_array($props_array);
                if(!is_array($props_array)) {
                    $content.=my_msg_to_str('',[],'Массив свойств неверен');
                } else {
                    $props_values=json_decode($tags['props'], true);
                    // print_array($props_values);
                    $param_value = [];
                    if(is_array($props_values)){
                        foreach ($props_values as $input_name => $value) {
                            $param_value[$input_name]=$value;
                        }
                    }
                    foreach ($props_array as $input_name => $params) {
                        $content .= '<td align="center">' . PHP_EOL;
                        if(check_key('type', $params) == 'boolean') {
                            $content .= '<input type="checkbox" class="attr_change" size="8" id="'.$tags['id'].'"  attr_type="boolean" attr_name="'.$input_name.'" '.(check_key($input_name,$param_value) ? ' checked' : '').'>';
                        } else {
                            $content .= '<input type="edit" class="form-control attr_change" maxlength="8" size="4" id="'.$tags['id'].'" attr_type="json" attr_name="'.$input_name.'" value="'.check_key($input_name,$param_value).'">';
                        }
                        $content .= '</td>' . PHP_EOL;
                    }
                }
            }
        }
    }
    $content .= '</table>';
    return $content;
}

if (true) {
    $subparts = 0;

    function sub_part($prev_id, $deep, $max_deep) {
        global $content, $subparts;
        if ($deep){
            $subparts++;
        }
        $query = "SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='$prev_id' group by cat_part.id order by cat_part.num,cat_part.title asc";
        $result = my_query($query);
        while ($row = $result->fetch_array()) {
//          $subparts++;
            if ((!$deep) && (!$prev_id)) {
                $content .= "<h3>{$row['title']}</h3>";
            } else {
                $content .= "<h4>{$row['title']}</h4>";
            }

            $content .= part_items($row['id']);
            if ($deep < $max_deep){
                sub_part($row['id'], $deep + 1, $max_deep);
            }
        }
    }

    sub_part(0, 0, 2);
}


$final_content = $content . '
    
<script type="text/javascript">
$(document).ready(function(){  
    $("input.attr_change").change(function(){
	var id=$(this).attr("id");
        var attr_type=$(this).attr("attr_type");
        var attr_name=$(this).attr("attr_name");
        if(attr_type == "boolean") {
            var value=$(this).prop("checked");
            if (!value) {
                value = "";
            }
        } else {
            var value=$(this).val();
        }
        var data = "attr_type=" + attr_type + "&attr_name="+attr_name + "&value=" +value + "&id=" + id;
        // console.log(data);
        
	$.ajax({
	   type: "POST", url: "'.$server['PHP_SELF'].'", data: data,
	   success: function(msg){
            if(msg !== "OK") {
                  alert(msg);
            }
	   }
	});
    });
});
</script>

    ';


echo get_tpl_by_name($part['tpl_name'], $tags, '', $final_content);
