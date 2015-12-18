<?php

$tags[Header] = "Статьи";
include "../include/common.php";

if ($input["view_article"]) {
    $_SESSION["view_article"] = $input["id"];
}

if ($input["view_list"]) {
    $_SESSION["view_article"] = "";
}

if ($_SESSION["view_article"]) {
    list($list_title) = my_select_row("select title from article_list where id='" . $_SESSION["view_article"] . "'", 1);
    $tags[Header].=" -> $list_title";
}

if ($input["del_article"]) {
    $query = "delete from article where id='$input[id]'";
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Статья успешно удалена.");
}

if ($input["added_article"]) {
    $input[form][date_add] = "now()";
    $input[form][list_id] = $_SESSION["view_article"];
    $input[form][content] = $_POST["form"]["content"];
    $input[form][content]=replace_base_href($input[form][content],true);
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $query = "insert into article" . db_insert_fields($input[form]);
    my_query($query, $conn, 1);
    $content.=my_msg_to_str("", "", "Статья успешно добавлена.");
}

if($input["revert"]){
    unset($input["edited_article"]);
    $input["edit_article"]=1;
}

if ($input["edited_article"]) {
    $input[form][date_add] = "now()";
    $input[form][content] = $_POST["form"]["content"];
    $input[form][content]=replace_base_href($input[form][content],true);
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $query = "update article set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn, 1);
    $content.=my_msg_to_str("", "", "Статья успешно изменена.");
    if($input["update"]){
        $input["edit_article"]=1;
    }
}

if (($input["edit_article"]) || ($input["add_article"])) {
    if ($input["edit_article"]) {
	$query = "select * from article where id='$input[id]'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags[type] = "edited_article";
	$tags[form_title] = "Редактирование";
	$tags[Header] = "Редактирование статьи";
    } else {
	$tags[type] = "added_article";
	$tags[form_title] = "Добавление";
	$tags[Header] = "Добавление статьи";
    }
    $tags[head_inc] = $JQUERY_INC . $EDITOR_INC;
    $tags["content"]=replace_base_href($tags["content"],false);
    $content.=get_tpl_by_title("article_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

if ($_SESSION["view_article"]) {
    $query = "SELECT * from article where list_id=" . $_SESSION["view_article"] . " order by date_add asc";
    $result = my_query($query, $conn, true);
    $content.=get_tpl_by_title("article_edit_table", $tags, $result);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

if ($input["del_list"]) {
    $query = "select id from article where list_id='$input[id]'";
    $result = my_query($query, $conn);
    if ($result->num_rows) {
	$content.=my_msg_to_str("error","","Этот раздел не пустой !");
    } else {
        $query = "delete from article_list where id='$input[id]'";
        my_query($query, $conn);
	$content.=my_msg_to_str("", "", "Раздел успешно удален.");
    }
}

if ($input["added_list"]) {
    $input[form][date_add] = "now()";
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $query = "insert into article_list " . db_insert_fields($input[form]);
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Раздел успешно добавлен.");
}

if ($input["edited_list"]) {
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $query = "update article_list set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn);
    $content.=my_msg_to_str("", "", "Раздел успешно изменен.");
}

if (($input["edit_list"]) || ($input["add_list"])) {
    if ($_GET["edit_list"]) {
	$query = "select * from article_list where id='$input[id]'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags[type] = "edited_list";
	$tags[form_title] = "Редактирование";
	$tags[Header] = "Редактирование раздела";
    } else {
	$tags[type] = "added_list";
	$tags[form_title] = "Добавление";
	$tags[Header] = "Добавление раздела";
    }
    $tags[head_inc] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_title("article_list_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

$query = "SELECT article_list.*,count(article.id) as files
from article_list 
left join article on (article.list_id=article_list.id) 
group by article_list.id order by article_list.date_add desc";
$result = my_query($query, $conn, true);
$content.=get_tpl_by_title("article_list_edit_table", $tags, $result);
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>
