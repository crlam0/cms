<?php
$tags[Header]="Прайс-лист";
include "../include/common.php";

$_SESSION["PART_ID"]=0;

/*
if(!count($input))$input[part_id]=0;

if (isset($input["uri"])) {
    $params=explode("/", $input["uri"]);
    $prev_id=0;
    foreach($params as $alias){
        $query="select id from cat_part where seo_alias like '$alias' and prev_id='{$prev_id}'";
        $row=my_select_row($query,true);
        $prev_id=$row["id"];
    }
    $input[part_id]=$prev_id;
}

if(isset($input[part_id])){
	$_SESSION["PART_ID"]=$input[part_id];
	unset($_SESSION["catalog_page"]);
}

if(isset($input[part_id])){
	$_SESSION["PART_ID"]=$input[part_id];
	unset($_SESSION["catalog_page"]);
}
if(!isset($_SESSION["PART_ID"]))$_SESSION["PART_ID"]="0";

if(isset($input["add_buy"])){
        $_SESSION["BUY"][$input["item_id"]]["count"]+=$input["cnt"];
        echo "OK";
        exit;
}

function prev_part($prev_id,$deep){
	global $conn,$arr;
	$query="SELECT id,title,prev_id from cat_part where id='$prev_id' order by title asc";
	$result=my_query($query,$conn);
	$arr[$deep]=$result->fetch_array();
	if($arr[$deep]["prev_id"])prev_part($arr[$deep]["prev_id"],$deep+1);
}

if(isset($_GET["view_item"])){
	list($_SESSION["PART_ID"])=my_select_row("select part_id from cat_item where id='{$_GET["view_item"]}'",1);
}

if(isset($_GET["show_all"]))$_SESSION["PART_ID"]=0;

$tags[nav_str].="<span class=nav_next><a href={$SUBDIR}price/ class=top>$tags[Header]</a></span>";
if($_SESSION["PART_ID"]){
	prev_part($_SESSION["PART_ID"],0);
	$arr=array_reverse($arr);
	$max_size=sizeof($arr)-1;
	while (list ($n, $row) = @each ($arr)){
		if(($n<$max_size)||(isset($_GET["view_item"]))){
			$tags[nav_str].="<span class=nav_next><a href=".$_SERVER["PHP_SELF"]."?part_id=$row[id]>$row[title]</a></span>";
			$tags[Header].=" - $row[title]";
		}else{
			$tags[nav_str].="<span class=nav_next>$row[title]</span>";
		}
	}
}

*/

$tags[nav_str].="<span class=nav_next><a href={$SUBDIR}price/ class=top>$tags[Header]</a></span>";

function part_items($part_id){
    global $conn;
    $query="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
    left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id)
    where part_id='{$part_id}'
    order by num,title asc";
    $result=my_query($query,$conn,1);
        if($result->num_rows){
            $content.="<br>
            <table width=100% align=center cellspacing=1 class=price_table>
            <tr class=price_header>
            <td width=30% class=price>Наименование</td>
            <td width=40% class=price>Описание</td>
            <td width=15% class=price>Цена с НДС по безналичному расчету руб/час</td>
            <td width=15% class=price>Минимальное время заказа</td>
            </tr>";
            while ($row = $result->fetch_array()){
                    $content.="<tr valign=middle class=price_line>
                    <td class=title>$row[title]</td>
                    <td class=title>".nl2br($row['descr'])."</td>
                    <td class=price>$row[price]</td>
                    <td class=price>$row[minimum_time]</td>
                    ";
                    /*  
                    <td class=price>
                        <input class=\"cnt_{$row[id]}\" size=1 maxlength=2 value=1>
                        <a class=buy_button item_id=\"{$row[id]}\">Заказать</a>
                    </td>
                    */
                    $content.="</tr>";
            }
            $content.="</table><br>\n";
    }    
    return $content;
}

if( true ){
    $content.="<div id=cat_parts>";
    $query = "select content from article_item where seo_alias='before_price'";
    list($before_price) =  my_select_row($query);
    $content.=$before_price."<br />";
    $subparts=0;
    function sub_part($prev_id,$deep,$max_deep){
        global $conn,$tags,$content,$IMG_PART_PATH,$IMG_PART_URL,$subparts,$SUBDIR;
        if($deep)$subparts++;
        $query="SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='$prev_id' group by cat_part.id order by cat_part.num,cat_part.title asc";
        $result=my_query($query,$conn,1);
        while ($row = $result->fetch_array()){
            $pan_ins="";
//                        $subparts++;
            if((!$deep)&&(!$prev_id)){
                    $content.="<div class=root_part><a name={$row['seo_alias']}></a><h3>$row[title]</h3></div>";
            }else{
                $content.="
                <div class=sub_part>
                    <h4> $row[title]</h4>                    
                </div>";				
            }
            $content.=part_items($row[id]);
            if($deep<$max_deep)sub_part($row[id],$deep+1,$max_deep);
        }
    }
    sub_part(0,0,2);
    $content.="</div>";
    $content.="</div>";
}

/*

if($_SESSION["PART_ID"]){
	$row_part=my_select_row("select * from cat_part where id='{$_SESSION["PART_ID"]}'",1);
	//	if(is_file($IMG_PART_PATH.$row[img]))echo "<img src=$IMG_PART_URL$row[img] border=0 align=left>\n";
	if(strlen($row_part[descr]))$content.="<div class=part_descr>$row_part[descr]</div>\n";
	$tags[Header].=" - $row_part[title]";
}

if(!isset($_SESSION["catalog_page"]))$_SESSION["catalog_page"]=1;
if(isset($input["page"])){
	$_SESSION["catalog_page"]=$input["page"];
}
list($PAGES)=my_select_row("SELECT ceiling(count(id)/$settings[catalog_items_per_page]) from cat_item where part_id='".$_SESSION["PART_ID"]."'",1);
if($PAGES>1){
	$tags[pages_list]="<div class=cat_pages>";
	for($i=1;$i<=$PAGES;$i++)$tags[pages_list].=($i==$_SESSION["catalog_page"]?"[ <b>$i</b> ]&nbsp;":"[ <a href=".$_SERVER["PHP_SELF"]."?page=$i>$i</a> ]&nbsp;");
	$tags[pages_list].="</div>";
}
$content.=$tags[pages_list];
$offset=$settings[catalog_items_per_page]*($_SESSION["catalog_page"]-1);	

$query="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id)"
.(isset($_GET["show_all"])?"":" where part_id='".$_SESSION["PART_ID"]."'")." 
order by cat_item.id,b_code,title asc limit $offset,$settings[catalog_items_per_page]";
$result=my_query($query,$conn,1);
if($result->num_rows){
	$content.="<div id=price>\n";
        $content.="<br>
        <table width=100% align=center cellspacing=1 class=price_table>
        <tr class=price_header>
        <td width=50% class=price>Наименование</td>
        <td width=10% class=price>Ед.</td>
        <td width=10% class=price>Цена, руб.</td>
        <td width=20% class=price>&nbsp;</td>";
        $content.="</tr>";
	while ($row = $result->fetch_array()){
		$content.="<tr valign=middle class=price_line>
        	<td class=title>$row[title]</td>
        	<td class=price>$row[units]</td>
        	<td class=price>$row[price]</td>
<td class=price>
                            <input class=\"cnt_{$row[id]}\" size=1 maxlength=2 value=1>
                            <a class=buy_button item_id=\"{$row[id]}\">Заказать</a>
</td>";
        	$content.="</tr>";
	}
	$content.="</table><br>\n";
	$content.="</div>\n";
}elseif( ($_SESSION["PART_ID"]) && (!$subparts)){
	$content.=my_msg_to_str("list_empty");
}

*/

$price_parts_content="";
$query="SELECT cat_part.*from cat_part where prev_id='0' order by cat_part.num,cat_part.title asc";
$result=my_query($query,$conn,1);
while ($row = $result->fetch_array()){
    $price_parts_content.="<a href=#{$row['seo_alias']}>{$row['title']}<a><br />";
}

$final_content='
    <div id=price>
    <div class=price_column>'.$content.'</div>
    <price_parts>'.$price_parts_content.'</div>
    </div>
    ';

if(strlen($row_part[descr_bottom]))$content.="<div class=part_descr>".nl2br($row_part[descr_bottom])."</div>\n";

$tags[head_inc] ="<script type=\"text/javascript\" src=\"{$BASE_HREF}inc/popup.js\"></script>\n";

echo get_tpl_by_title($part[tpl_name],$tags,"",$final_content);
?>

<script>
    var aside = document.querySelector('price_parts'),
        HTMLtop = document.documentElement.getBoundingClientRect().top,
        t0 = aside.getBoundingClientRect().top - HTMLtop;

    window.onscroll = function() {
        aside.className = (t0 < window.pageYOffset ? 'sticky' : '');
    };
</script>
