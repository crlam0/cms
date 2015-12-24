<?php

function get_block($block_name){
global $DIR,$SUBDIR,$BASE_HREF,$conn,$settings,$input;
	switch ($block_name){		
		
	case "menu":
		list($root_id)=my_select_row("select id from menu_list where root=1",1);
		if(!$root_id)return "";
		$query="select * from menu_item where '".$_SESSION["FLAGS"]."' like concat('%',flag,'%') and menu_id='$root_id' and active=1 order by position asc";
		$result=my_query($query,$conn,1);
		if(!$result->num_rows)return "";
		while ($row = $result->fetch_array()){
			$tags[menu_content].="<li><a href=\"".$SUBDIR.get_menu_href($tmp,$row)."\" class=\"$row[css_class]\"><span>$row[title]</span></a>\n";			
			$query="select * from menu_item where '".$_SESSION["FLAGS"]."' like concat('%',flag,'%') and menu_id='$row[submenu_id]' order by position asc";
			$result_sub=my_query($query,$conn,1);
			if($result_sub->num_rows){
				$tags[menu_content].="<ul>\n";
				while ($row_sub = mysql_fetch_array($result_sub)){
					$tags[menu_content].="<li><a href=\"".$SUBDIR.get_menu_href($tmp,$row_sub)."\" class=\"$row_sub[css_class]\"><span>$row_sub[title]</span></a></li>\n";
				}
				$tags[menu_content].="</ul>\n";
			}
			$tags[menu_content].="</li>\n";
		}
		return get_tpl_by_title("block_menu",$tags,$result);		
	break;	
	
        case "menu_bottom":
            list($root_id) = my_select_row("select id from menu_list where bottom_menu=1", 1);
            if (!$root_id)
                return "";
            $query = "select * from menu_item where '" . $_SESSION["FLAGS"] . "' like concat('%',flag,'%') and menu_id='$root_id' and active=1 order by position asc";
            $result = my_query($query, $conn, 1);
            if (!$result->num_rows)
                return "";
            while ($row = $result->fetch_array()) {
                $tags[menu_content].="<li class=\"menu-item menu-item-type-post_type menu-item-object-page\"><a href=\"" . $SUBDIR . get_menu_href($tmp, $row) . "\" title=\"{$row["title"]}\">{$row["title"]}</a></li>";
            }
            return $tags[menu_content];
            break;


        case "menu_main":
            list($root_id) = my_select_row("select id from menu_list where root=1", 1);
            if (!$root_id)
                return "";
            $query = "select * from menu_item where '" . $_SESSION["FLAGS"] . "' like concat('%',flag,'%') and menu_id='$root_id' and active=1 order by position asc";
            $result = my_query($query, $conn, 1);
            if (!$result->num_rows)
                return "";
            while ($row = $result->fetch_array()) {
                $tags[menu_content].="<li><a href=\"" . $SUBDIR . get_menu_href($tmp, $row) . "\" title=\"{$row["title"]}\">{$row["title"]}</a></li>";
            }
            return $tags[menu_content];
            break;


        case "cat_menu":
                function sub_part_bl($prev_id,$deep){
                        global $conn,$SUBDIR;
                        $query="SELECT * from cat_part where prev_id=$prev_id order by num,title asc";
                        $result=mysql_query($query,$conn);
                        $content="";
                        while ($row = mysql_fetch_array($result)){
                                $content.="<a class=item2 href=\"".$SUBDIR.get_cat_part_href($row[id])."\" title=\"$row[title]\"><span> - $row[title]</span></a>\n";
                //		sub_part_bl($row[id],$deep+1);
                        }
                        return $content;
                }

                $query="SELECT * from cat_part where prev_id='0' order by num,title asc";
                $result=mysql_query($query,$conn);
                $content="";
                while ($row = mysql_fetch_array($result)){
                        $content.="<a class=item href=\"".$SUBDIR.get_cat_part_href($row[id])."\" title=\"$row[title]\"><span>$row[title]</span></a>\n";
                        if($_SESSION["PART_ID"]==$row[id]){
                            $content.=sub_part_bl($row[id],1);
                        }else{
                            list($prev_id)=my_select_row("select prev_id from cat_part where id='{$_SESSION["PART_ID"]}'",1);
                            if($prev_id==$row[id]){
                                $content.=sub_part_bl($row[id],1);
                            }
                        }
                }
                $tags[menu_content].=$content;
                return get_tpl_by_title("block_cat_menu",$tags,$result);
	break;	
    
	case "news":
		$query="select *,date_format(date,'%d.%m.%Y') as date from news order by id desc limit $settings[news_block_count]";
		$result=my_query($query);
		function get_news_short_content($tmp,$row){
			return cut_str($row[content],100);
		}
		return get_tpl_by_title("block_news",$tags,$result);				
	break;	

        case "last_posts":
            $TABLE="blog_posts";
            $query = "SELECT {$TABLE}.*,users.fullname as author from {$TABLE} left join users on (users.id=uid)
                    where {$TABLE}.active='Y'
                    group by {$TABLE}.id  order by {$TABLE}.id desc limit 5";
            $result = my_query($query, $conn, true);

            if ($result->num_rows) {
                while ($row = $result->fetch_array()) {
                    $content.='<li class="rp-item">';
                    if(is_file($DIR.$settings['blog_img_path'].$row['image_name'])){
                        $content.='<div class="rp-thumb"><a href="'.get_post_href($row).'"><img width="150" height="150" src="'.$SUBDIR.$settings['blog_img_path'].$row['image_name'].'" class="attachment-thumbnail wp-post-image" alt="'.$row['title'].'"></a></div>';
                    }            
                    $content.='<div class="rp-title"><a href="'.get_post_href($row).'">'.$row["title"].'</a></div>
                    </li>';
                }
            }

            return $content;
            break;

        case "partners":
            $SCRIPT = $server['SCRIPT_NAME'];
            if (strlen($SUBDIR) > 1 )$SCRIPT = str_replace($SUBDIR, "/", $SCRIPT);

            if($SCRIPT=='/index.php'){
                $query = "SELECT * from slider_images where length(file_name)>0 order by pos,title asc";
                $result = my_query($query, $conn, true);
                return get_tpl_by_title("slider_items", $tags, $result);
            }else{
                return "";
            }
            break;

        case "slider":
            $SCRIPT = $server['SCRIPT_NAME'];
            if (strlen($SUBDIR) > 1 )$SCRIPT = str_replace($SUBDIR, "/", $SCRIPT);

            if($SCRIPT=='/index.php'){
                $query = "SELECT * from slider_images where length(file_name)>0 order by pos,title asc";
                $result = my_query($query, $conn, true);
                return get_tpl_by_title("slider_items", $tags, $result);
            }else{
                return "";
            }
            break;

        case "vote":
            $query = "select id,title,type from vote_list where active=1 limit 1";
            $result = my_query($query, $conn, 1);
            if ($result->num_rows) {
                list($vote_id, $title, $type) = $result->fetch_array();
                $tags[vote_title] = $title;
                $tags[variants] = "";
                $query = "select * from vote_variants where vote_id='$vote_id'";
                $result = my_query($query, $conn, 1);
                if (!$result->num_rows)
                    return "";
                $i = 0;
                while ($row = $result->fetch_array()) {
                    $tags[variants].="
                            <div class=vote_variant>
                            <input type=$type name=vote[] value=$row[id]" . ((!$i) && ($type == "radio") ? " checked" : "") . ">$row[title]
                            </div>\n";
                    $i++;
                }
                return get_tpl_by_title("block_vote", $tags);
            }
            break;

        case "contacts":
            $query="select content from article where seo_alias = 'contacts_block'";
            $result=my_query($query);
            list($content)=$result->fetch_array();
            return $content;
            break;

        case "calendar":
            ob_start();
            show_month(date('n'), 0);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
            break;

        case "banners":
            ob_start();
            include_once($DIR . "bannners.php");
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
            break;
	
	case "menu_admin":
		ob_start();
		include_once($DIR."admin/nav.php");
		$content=ob_get_contents();
		ob_end_clean();		
		return $content;
	break;	
	
	default:
		$tags[title]=$block_name;
		return my_msg_to_str("block_not_found",$tags);
	}
}


?>