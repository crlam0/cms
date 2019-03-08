<!--[title]admin_last_comments[/title]-->
<!--DESCRIPTION: Последние комментарии -->
<!--[content]-->
<h3>Последние комментарии</h3>
<table width=800  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
        <td width=15%>Дата</td>
        <td width=15%>Раздел</td>
        <td width=40%>Сообщение</td>
        <td width=20%>Автор</td>
</tr>
[%loop_begin%]
        <tr class="content" align="left">
        <td><b>[%row(date_add)%]</b></td>
        <td align="center">[%row(target_type)%]</td>
        <td align="center">[%row(content)%]</td>
        <td align="center">[%row(author)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]admin_last_requests[/title]-->
<!--DESCRIPTION: Последние заказы -->
<!--[content]-->
<h3>Последние заказы</h3>
<table width=800  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
        <td width=20%>Дата</td>
        <td width=40%>Контакты</td>
        <td width=40%>Сообщение</td>
</tr>
[%loop_begin%]
        <tr class="content" align="left">
        <td align="center"><b>[%row(date)%]</b></td>
        <td>[%row(contact_info,nl2br)%]</td>
        <td>[%row(item_list,nl2br)%][%row(message,nl2br)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]article_list_edit_table[/title]-->
<!--DESCRIPTION: Список разделов в статьях -->
<!--[content]-->
<table width=600  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15%>Дата</td>
	<td width=30%>Тема</td>
	<td width=30%>Алиас</td>
        <td width=15%>Статей</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td>[%row(date_add)%]</td>
	<td><b><a href=[%PHP_SELF%]?view_article=1&id=[%row(id)%]>[%row(title)%]</a></b></td>
	<td align="center">[%row(seo_alias)%]</td>
	<td align="center">[%row(files)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_list=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_list=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add_list value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center>
<!--[/content]-->


<!--[title]article_list_edit_form[/title]-->
<!--DESCRIPTION: Форма редактиролвания разделов -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class="content"><td align="left" colspan="2">
		<textarea class="form-control" name=form[descr] rows=25 cols=100 maxlength=64000>[%descr%]</textarea>
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->


<!--[title]article_edit_table[/title]-->
<!--DESCRIPTION: Список статей адм. разделе -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_article value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=600  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=10%>Дата</td>
	<td width=40%>Название</td>
	<td width=40%>Алиас</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td>[%row(date_add)%]</td>
	<td><b>[%row(title)%]</b></td>
	<td align="center">[%row(seo_alias)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_article=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_article=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center>
<form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add_article value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form>
</center>
<center><a href=[%PHP_SELF%]?view_list=1 class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->


<!--[title]article_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования статьи -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class="content" align="left"><td>Автор:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[author] value="[%author%]"></td></tr>
	<tr class="content"><td align="left" colspan="2">
		<textarea class="form-control" id=editor name=form[content] rows=35 cols=80 maxlength=64000>[%content%]</textarea>
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2">
                <input class="btn btn-primary" type="submit" name=update value="  Сохранить  "> 
                <input class="btn btn-primary" type="submit" value="  Сохранить и выйти ">
                <input class="btn btn-primary" type="submit" name=revert value="  Вернуть исходный  "> 
        </td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>
<!--[/content]-->

<!--[title]blog_edit_table[/title]-->
<!--DESCRIPTION: Список постов в блоге -->
<!--[content]-->
<style type="text/css">
tr.active_Y { background: #ffffff; }
tr.active_N { background: #dddddd; }
</style>
<br><center><table width=700  class="table table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15%>Дата</td>
	<td width=15%>Алиас</td>
	<td width=50%>Заголовок</td>
	<td width=10%>Активен</td>
	<td width=5%>&nbsp;</td>
	<td width=5%>&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class=active_[%row(active)%] align="left" id=tr_[%row(id)%]>
	<td>[%row(date_add)%]</b></td>
	<td>[%row(seo_alias)%]</td>
	<td>[%row(title)%]</td>
	<td align="center"><input class="" type=checkbox class=sw_active value='[%row(id)%]' [%row(active,if,checked)%]></td>
	<td width=16><a href="[%PHP_SELF%]?edit_post=1&id=[%row(id)%]"><img src="../images/open.gif" alt="Редактировать" border="0"></a></td>
	<td width=16><a href="[%PHP_SELF%]?del_post=1&id=[%row(id)%]"><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center>
<form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add_post value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form>
</center>
<script type="text/javascript">
$(document).ready(function(){  
    $('input:checkbox').change(function(){
	var id=$(this).val();
	if( $(this).prop("checked") ){ var active='Y'; }else{ var active='N'; }
	$.ajax({
	   type: "POST", url: "[%PHP_SELF%]", data: "active="+active+"&id="+id,
	   success: function(msg){
	     var tr_id="#tr_"+id;
	     if(msg == 'Y') $(tr_id).attr("class","active_Y");
	     else if(msg == 'N') $(tr_id).attr("class","active_N");
	     else alert(msg);
	   }
	});
    });
});
</script>
<!--[/content]-->


<!--[title]blog_post_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования поста в блоге -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table align="center" width=500  class="table table-striped table-responsive table-bordered normal-form">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
 	<tr class="content" align="left"><td>Ссылается на:</td><td>[%target_type_select%]</td></tr>
        <tr class="content" align="left" id="target_select"></tr>
	<tr class="content"><td align="left" colspan="2">
		<textarea class="form-control" id=editor name=form[content] rows=35 cols=80 maxlength=64000>[%content%]</textarea>
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2">
                <input class="btn btn-primary" type="submit" name=update value="  Сохранить  "> 
                <input class="btn btn-primary" type="submit" value="  Сохранить и выйти ">
                <input class="btn btn-primary" type="submit" name=revert value="  Вернуть исходный  "> 
        </td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<script type="text/javascript">
function get_target_select () {
    var target_type = "";
    $("#target_type option:selected").each(function () {
        target_type = $(this).attr("value");
    });
    $('#target_select').load("[%PHP_SELF%]?get_target_select=1&item_id=[%id%]&target_type="+target_type);
}

$(document).ready(function(){
    get_target_select ();
    $('#target_type').change(function(){
        get_target_select ();
    });
});
</script>
<!--[/content]-->


<!--[title]cat_part_table[/title]-->
<!--DESCRIPTION: Список разделов в каталоге -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form></center><br>
<table width=800  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=50%>Название</td>
	<td width=24%>Алиас</td>
	<td width=20%>Картинка</td>
	<td width=3%>&nbsp;</td>
	<td width=3%>&nbsp;</td>
</tr>
[%table_content%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=adding value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->

<!--[title]cat_part_form[/title]-->
<!--DESCRIPTION: Форма редактирования раздела каталога -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" name=main_form enctype="multipart/form-data">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=MAX_FILE_SIZE value=3000000>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Позиция:</td><td><input class="form-control" type="edit" maxlength=45 size="64" name=form[num] value="[%num%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength=45 size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class=header><td colspan="2">Описание :</td></tr>
	<tr class="content"><td align="left" colspan="2">
		<textarea class="form-control" rows=15 cols=100 id=editor maxlength=64000 name=form[descr]>[%descr%]</textarea>
	</td></tr>
	<tr class=header><td colspan="2">Прочее :</td></tr>
	<tr class="content" align="left"><td>Находится в разделе:</td><td>
		<select class="form-control" name=form[prev_id]>
		<option value=0>-</option>
		[%prev_id_select%]
		</select>
	</td></tr>
	<tr class="content" align="left"><td>Колонок с ценами:</td><td><input class="form-control" type="edit" maxlength=1 size="16" name=form[price_cnt] value="[%price_cnt%]"></td></tr>
	<tr class="content" align="left"><td>Колонка 1:</td><td><input class="form-control" type="edit" maxlength="255" size="16" name=form[price1_title] value="[%price1_title%]"></td></tr>
	<tr class="content" align="left"><td>Колонка 2:</td><td><input class="form-control" type="edit" maxlength="255" size="16" name=form[price2_title] value="[%price2_title%]"></td></tr>
	<tr class="content" align="left"><td>Колонка 3:</td><td><input class="form-control" type="edit" maxlength="255" size="16" name=form[price3_title] value="[%price3_title%]"></td></tr>
	<tr class="content" align="left"><td>Фиксированая ширина для фотографий товаров:</td><td><input class="form-control" type="edit" maxlength="255" size="16" name=form[item_image_width] value="[%item_image_width%]"></td></tr>
	<tr class="content" align="left"><td>Фиксированая высота для фотографий товаров:</td><td><input class="form-control" type="edit" maxlength="255" size="16" name=form[item_image_height] value="[%item_image_height%]"></td></tr>
	<tr class="content">
		<td>Картинка:</td>
		<td align="center">[%img_tag%][%del_button%]<br>Загрузить: <input class="form-control" name=img_file type=file><br></td>
	</tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
	<tr class="header"><td colspan=2>Properties (изменять если вы ТОЧНО знаете что делаете):</td></tr>
	<tr class="content"><td align="left" colspan=2>
		<textarea class="form-control" rows="15" cols="100" id="editor_html" maxlength="6400"0 name="form[items_props]">[%items_props%]</textarea>
	</td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>
<!--[/content]-->



<!--[title]cat_item_table[/title]-->
<!--DESCRIPTION: Список товаров в каталоге -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=900  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=5%>Номер</td>
	<td width=20%>Название</td>
	<td width=25%>Описание</td>
	<td width=10%>Цена</td>
	<td width=10%>Изображение</td>
	<td width=5%>&nbsp;</td>
	<td width=5%>&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content">
	<td align="center">[%row(num)%]</td>
	<td align="left"><b>[%row(title)%]</b></td>
	<td align="left">[%row(descr)%]</td>
	<td align="center">[%row(price)%]</td>
	<td align="center">[%func(show_img)%]</td>
	<td><a href=[%PHP_SELF%]?edit=1&id=[%row(id)%]><img src="../images/open.gif" alt="Редактировать" border="0"></a></td>
	<td><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<center><a href=cat_part_edit.php class="btn btn-default"> << Назад</a></center>
<!--[/content]-->



<!--[title]cat_item_form[/title]-->
<!--DESCRIPTION: форма редактирования наименований -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=MAX_FILE_SIZE value=3000000>
<input type="hidden" name=[%type%] value=1>
<table width=900  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Позиция:</td><td><input class="form-control" type="edit" maxlength=45 size="64" name=form[num] value="[%num%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength=45 size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
<!--
        <tr class="content" align="left"><td>Специальное предложение:</td><td><input type=checkbox maxlength="255" size="64" name=form[special_offer] value="1" [%special_offer%]></td></tr>
        <tr class="content" align="left"><td>Новинка:</td><td><input type=checkbox maxlength="255" size="64" name=form[novelty] value="1" [%novelty%]></td></tr>
	<tr class="content" align="left"><td>Остаток на складе:</td><td><input class="form-control" type="edit" maxlength=64 size=128 name=form[balance] value="[%balance%]"></td></tr>
-->        
	[%price_inputs%]
	<tr class="content" align="left"><td>Вес/количество/объем:</td><td><input class="form-control" type="edit" maxlength=45 size="64" name=form[cnt_weight] value="[%cnt_weight%]"></td></tr>
        
        [%props_inputs%]
        
        
	<tr class=header><td colspan="2">Краткое описание</td></tr>
	<tr class="content"><td align="center" colspan="2">
		<textarea class="form-control" name=form[descr] rows=7 cols=90 maxlength=64000>[%descr%]</textarea>
	</td></tr>
	<tr class="content"><td align="center" colspan="2">
                <center>
                    <a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a>
                    <input class="btn btn-primary" type="submit" value="  Сохранить  ">
                </center>
	</td></tr>
	<tr class=header><td colspan="2">Полное описание</td></tr>
	<tr class="content"><td align="center" colspan="2">
		<textarea class="form-control" id=editor name=form[descr_full] rows=7 cols=90 maxlength=64000>[%descr_full%]</textarea>
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>

<!--[/content]-->
<!--[title]cat_item_images_form[/title]-->
<!--DESCRIPTION: форма редактирования изображений -->
<!--[content]-->
<br>
<div id="image_list">[%images%]</div>
<br>
<form action="[%PHP_SELF%]" method="POST" id="upload_form" enctype="multipart/form-data">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=add_image value=[%id%]>
<table width=550  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header align="center"><td colspan="4">Добавить изображение:</td></tr>
	<tr class="content" align="left"><td colspan="4">
		<table width=550 border="0" cellspacing=5 cellpadding=5 align="center">
		<tr><td nowrap>Имя файла:</td><td><input class="form-control" type=file name=img_file></td></tr>
		<tr><td nowrap>Описание : </td><td><input class="form-control" type="edit" size=32 maxlength=128 name=descr></td></tr>
		<tr><td align="center" colspan="2"><input class="btn btn-primary" type="submit" id=upload_submit value="  Добавить  "></td></tr>
		</table>
	</td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>

<script type="text/javascript">
function update_image_list () {
        $('#image_list').load("[%PHP_SELF%]?get_image_list=1&item_id=[%id%]");
        
}
$(document).ready(function(){
        update_image_list();
        $('.default_img').live('change',function(){
                var id=$(this).attr("image_id");
                $.ajax({
                   type: "POST", url: "[%PHP_SELF%]", data: "item_id=[%id%]&default_img="+id,
                   success: function(msg){
                                if(msg != 'OK') alert(msg);
                   }
                });
        });
        $('.del_button').live('click',function(){
                var id=$(this).attr("image_id");
                if (confirm("Вы уверены ?")){
                        $.ajax({
                                type: "POST", url: "[%PHP_SELF%]", data: "del_image=1&id="+id,
                                success: function(msg){
                                        if(msg == 'OK') update_image_list();
                                        else alert(msg);
                                }
                        });
                }
                return false;
        });
        var options = { 
            target: '#image_list',
            success: update_image_list
        }; 
        $('#upload_form').submit(function(){
                $(this).ajaxSubmit(options);
                return false;
        });
});
</script>
<!--[/content]-->

<!--[title]price_items_edit[/title]-->
<!--DESCRIPTION: Список товаров -->
<!--[content]-->

<br />
<table width="100%" class="table table-striped table-responsive table-bordered normal-form">
    
    <form action="[%PHP_SELF%]" method="POST" id="upload_form" enctype="multipart/form-data">
<tr class="price_header" valign="middle">
	<td width="30%" class="price_header">Наименование</td>
	<td width="50%" class="price_header">Описание</td>
	<td width="50%" class="price_header">Остаток</td>
	<td width="50%" class="price_header">Остаток Б/У</td>
</tr>
[%loop_begin%]
	<tr valign="middle" class="price"_line>
	<td class="title">[%row(title)%]</td>
	<td class="title">[%row(descr,nl2br)%]</td>
        <td class="price"><input type="edit" size="20" maxlength="64" value="[%row(balance)%]" class="attr_change" attr_name="balance" id="[%row(id)%]" /></td>
	<td class="price"><input type="edit" size =4" maxlength="4" value="[%row(used_balance)%]" class="attr_change" attr_name="used_balance" id="[%row(id)%]" /></td>
	</tr>
[%loop_end%]</form>
    
</table>
<br />

<!--[/content]-->




<!--[title]comments_edit_table[/title]-->
<!--DESCRIPTION: Список постов в комментариях -->
<!--[content]-->
<style type="text/css">
tr.active_Y { background: #ffffff; }
tr.active_N { background: #dddddd; }
</style>
<br><center><table width=800  class="table table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=10%>Дата</td>
	<td width=10%>Тип</td>
	<td width=20%>Автор/Email</td>
	<td width=50%>Содержание</td>
	<td width=10%>Активен</td>
	<td width=5%>&nbsp;</td>
	<td width=5%>&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class=active_[%row(active)%] align="left" id=tr_[%row(id)%]>
	<td align="center">[%row(date_add)%]</b></td>
	<td align="center">[%row(target_type)%]</td>
	<td align="center"><b>[%row(author)%]</b><br />[%row(email)%]<br />[%row(ip)%]</td>
	<td>[%row(content,nl2br)%]</td>
	<td align="center"><input class="" type=checkbox class=sw_active value='[%row(id)%]' [%row(active,if,checked)%]></td>
	<td width=16><a href="[%PHP_SELF%]?edit_comment=1&id=[%row(id)%]"><img src="../images/open.gif" alt="Редактировать" border="0"></a></td>
	<td width=16><a href="[%PHP_SELF%]?del_comment=1&id=[%row(id)%]"><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center>
<form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add_post value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form>
</center>
<script type="text/javascript">
$(document).ready(function(){  
    $('input:checkbox').change(function(){
	var id=$(this).val();
	if( $(this).prop("checked") ){ var active='Y'; }else{ var active='N'; }
	$.ajax({
	   type: "POST", url: "[%PHP_SELF%]", data: "active="+active+"&id="+id,
	   success: function(msg){
	     var tr_id="#tr_"+id;
	     if(msg == 'Y') $(tr_id).attr("class","active_Y");
	     else if(msg == 'N') $(tr_id).attr("class","active_N");
	     else alert(msg);
	   }
	});
    });
});
</script>
<!--[/content]-->


<!--[title]comment_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования поста в блоге -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table align="center" width=500  class="table table-striped table-responsive table-bordered normal-form">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content"><td align="left" colspan="2">
		<textarea class="form-control" id=editor name=form[content] rows=15 cols=80 maxlength=64000>[%content%]</textarea>
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2">
                <input class="btn btn-primary" type="submit" name=update value="  Сохранить  "> 
        </td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->

<!--[title]discount_edit_table[/title]-->
<!--DESCRIPTION: Список скидок -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td>Сумма</td>
<td>Скидка</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
[%loop_begin%]
        <tr class="content" align="left">
        <td>[%row(summ)%]</td>
        <td><b>[%row(discount)%]%</b></td>
        <td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" width=16 height=16 alt="Редактировать" border="0"></a></td>
        <td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
        </tr>
        [%loop_end%]
</table>
<br>
<center>
<form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form>
</center>
<!--[/content]-->

<!--[title]discount_edit_form[/title]-->
<!--DESCRIPTION: форма редактирования скидок -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
        <tr class="content" align="left"><td>Сумма:</td><td><input class="form-control" type="edit" maxlength="16" size="16" name=form[summ] value="[%summ%]"></td></tr>
        <tr class="content" align="left"><td>Скидка:</td><td><input class="form-control" type="edit" maxlength="16" size="16" name=form[discount] value="[%discount%]"></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>
<!--[/content]-->




<!--[title]faq_edit_table[/title]-->
<!--DESCRIPTION: Список сообщений в вопрос/ответ -->
<!--[content]-->
<style type="text/css">
tr.active_Y { background: #ffffff; }
tr.active_N { background: #dddddd; }
</style>
<br><center><table width=700  class="table table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15%>Дата</td>
	<td width=50%>Сообщение</td>
	<td width=15%>Автор</td>
	<td width=10%>Активен</td>
	<td width=5%>&nbsp;</td>
	<td width=5%>&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class=active_[%row(active)%] align="left" id=tr_[%row(id)%]>
	<td>[%row(date)%]</b></td>
	<td>[%row(txt,nl2br)%]
	[%row(ans,if,<b>Ответ:</b><br>)%][%row(ans,nl2br)%]
	</td>
	<td align="center"><b>[%row(author)%]</b><br>[ [%row(ip)%] ]</td>
	<td align="center"><input class="" type=checkbox class=sw_active value='[%row(id)%]' [%row(active,if,checked)%]></td>
	<td width=16><a href="[%PHP_SELF%]?edit=1&id=[%row(id)%]"><img src="../images/open.gif" alt="Редактировать" border="0"></a></td>
	<td width=16><a href="[%PHP_SELF%]?del=1&id=[%row(id)%]"><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<script type="text/javascript">
$(document).ready(function(){  
    $('input:checkbox').change(function(){
	var id=$(this).val();
	if( $(this).prop("checked") ){ var active='Y'; }else{ var active='N'; }
	$.ajax({
	   type: "POST", url: "[%PHP_SELF%]", data: "active="+active+"&id="+id,
	   success: function(msg){
	     var tr_id="#tr_"+id;
	     if(msg == 'Y') $(tr_id).attr("class","active_Y");
	     else if(msg == 'N') $(tr_id).attr("class","active_N");
	     else alert(msg);
	   }
	});
    });
});
</script>
<!--[/content]-->


<!--[title]faq_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования сообщений в вопрос/ответ -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name=edited value=[%id%]>
<table align="center" width=500  class="table table-striped table-responsive table-bordered normal-form">
	<tr class="content" align="left"><td>Автор:</td><td><input class="form-control" type="edit" maxlength=64 name=form[author] value="[%author%]"></td></tr>        
	<tr class=header><td align="center" colspan="2">Сообщение :</td></tr>
	<tr class="content"><td colspan="2">
		<textarea class="form-control" name=form[txt] rows=10 cols=100>[%txt%]</textarea>
	</td></tr>
	<tr class=header><td align="center" colspan="2">Ответ :</td></tr>
	<tr class="content"><td colspan="2">
		<textarea class="form-control" name=form[ans] rows=10 cols=100>[%ans%]</textarea>
	</td></tr>
	<tr class=header><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="Сохранить"></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->


<!--[title]gallery_list_edit_table[/title]-->
<!--DESCRIPTION: Список галерей -->
<!--[content]-->
<style type="text/css">
tr.active_Y { background: #ffffff; }
tr.active_N { background: #dddddd; }
</style>
<table width=600  class="table table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15%>Дата</td>
	<td width=25%>Тема</td>
	<td width=20%>Алиас</td>
	<td width=15%>Изображений</td>
	<td width=15%>Активна</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="active_[%row(active)%]" align="left" id="tr_[%row(id)%]">
	<td>[%row(date_add)%]</td>
	<td><b><a href=[%PHP_SELF%]?view_gallery=1&id=[%row(id)%]>[%row(title)%]</a></b></td>
	<td align="center">[%row(seo_alias)%]</td>
	<td align="center">[%row(images)%]</td>
	<td align="center"><input class="" type=checkbox class=sw_active value='[%row(id)%]' [%row(active,if,checked)%]></td>
	<td width=16><a href=[%PHP_SELF%]?edit_gallery=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_gallery=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<script type="text/javascript">
$(document).ready(function(){  
    $('input:checkbox').change(function(){
	var id=$(this).val();
	if( $(this).prop("checked") ){ var active='Y'; }else{ var active='N'; }
	$.ajax({
	   type: "POST", url: "[%PHP_SELF%]", data: "active="+active+"&id="+id,
	   success: function(msg){
	     var tr_id="#tr_"+id;
	     if(msg === 'Y') $(tr_id).attr("class","active_Y");
	     else if(msg === 'N') $(tr_id).attr("class","active_N");
	     else alert(msg);
	   }
	});
    });
});
</script>

<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_gallery value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->



<!--[title]gallery_list_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования галерей -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class="content"><td align="left" colspan="2">[%descr%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]gallery_image_edit_table[/title]-->
<!--DESCRIPTION: Список изображений в адм. разделе -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_image value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=700  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15%>Дата</td>
        <td width=15%>По умолчанию</td>
	<td width=20%>Название</td>
	<td width=40%>Изображение</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td align="center">[%row(date_add)%]</td>
	<td align="center"><input type=radio name=ch_default class=default_image image_id=[%row(id)%][%func(is_default_image)%]></td>
	<td><b>[%row(title)%]</b><br>[%row(descr)%]</td>
	<td align="center">[%func(show_img)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_image=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_image=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_image value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br /><br />

<center><form action="[%PHP_SELF%]" method="post" enctype="multipart/form-data">
  <input class="form-control normal-form" type="file" multiple="multiple" name="files[]" />
  <input type="hidden" name=MAX_FILE_SIZE value=16000000>		
  <input type="hidden" name=add_multiple value=1>
  <input class="btn btn-primary" type="submit" value="Добавить несколько" />
</form>
</center>    

<script type="text/javascript">
$(document).ready(function(){
    $('.default_image').live('change',function(){
        var id=$(this).attr("image_id");
        $.ajax({
           type: "POST", url: "[%PHP_SELF%]", data: "default_image_id="+id,
           success: function(msg){
                if(msg !== 'OK') alert(msg);
                $(this).prop("checked",true);
           }
        });
    });
});
</script>

<center><a href=[%PHP_SELF%]?list_gallery=1 class="btn btn-default"><<  Назад</a></center>

<!--[/content]-->



<!--[title]gallery_image_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования изображений -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" enctype="multipart/form-data">
<input type="hidden" name=MAX_FILE_SIZE value=16000000>		
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Изображение:</td><td><input class="form-control" name=img_file type=file size=40></td></tr>
	<tr class="content"><td align="left" colspan="2">[%descr%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]media_list_edit_table[/title]-->
<!--DESCRIPTION: Список разделов в файлах -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15%>Дата</td>
	<td width=30%>Тема</td>
	<td width=30%>Алиас</td>
	<td width=15%>Файлов</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td>[%row(date_add)%]</td>
	<td><b><a href=[%PHP_SELF%]?view_list=1&id=[%row(id)%]>[%row(title)%]</a></b></td>
	<td align="center">[%row(seo_alias)%]</td>
	<td align="center">[%row(files)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_list=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_list=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_list value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->



<!--[title]media_list_edit_form[/title]-->
<!--DESCRIPTION: Форма редактиролвания разделов -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Алиас:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class="content"><td align="left" colspan="2">
	[%descr%]
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]media_files_edit_table[/title]-->
<!--DESCRIPTION: Список файлов в адм. разделе -->
<!--[content]-->
<center>
<form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add_file value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form>
</center>
<br>
<table width="500" class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width="5%" align="center">№</td>
	<td width="15%">Дата</td>
	<td width="30%">Название </td>
	<td width="20%">Имя файла, размер</td>
	<td width="5%" align="center">&nbsp;</td>
	<td width="5%" align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td align="center">[%row(num)%]</td>
	<td align="center">[%row(date_add)%]</td>
	<td><b>[%row(title)%]</b><br>[%row(descr)%]</td>
	<td align="center"><b>[%row(file_name%]</b><br>[%func(show_size)%]</td>
	<td width="16"><a href="[%PHP_SELF%]?edit_file=1&id=[%row(id)%]"><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width="16"><a href="[%PHP_SELF%]?del_file=1&id=[%row(id)%]"><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_file value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<center><a href=[%PHP_SELF%]?list_media=1 class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]media_files_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования файлов -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" enctype="multipart/form-data">
<input type="hidden" name=MAX_FILE_SIZE value=67108864>		
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Позиция:</td><td><input class="form-control" type="edit" maxlength=45 size="64" name=form[num] value="[%num%]"></td></tr>
	<tr class="content" align="left"><td>Файл:</td><td><input class="form-control" name=uploaded_file type=file size=40></td></tr>
	<tr class="content"><td align="left" colspan="2">[%descr%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]menu_edit_table[/title]-->
<!--DESCRIPTION: Список меню -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=60%>Название</td>
	<td width=30%>Главное</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b><a href=[%PHP_SELF%]?view_menu=1&id=[%row(id)%]>[%row(title)%]</a></b></td>
	<td align="center">[%row(root,yes_no)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_menu=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_menu=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_menu value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->



<!--[title]menu_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования списка меню -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Основное:</td><td><input class="" type=checkbox maxlength="255" size="64" name=form[root] value="1" [%root%]></td></tr>
	<tr class="content" align="left"><td>Верхнее:</td><td><input class="" type=checkbox maxlength="255" size="64" name=form[top_menu] value="1" [%top_menu%]></td></tr>
	<tr class="content" align="left"><td>Нижнее:</td><td><input class="" type=checkbox maxlength="255" size="64" name=form[bottom_menu] value="1" [%bottom_menu%]></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>		
<center><a href="[%PHP_SELF%]" class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]menu_item_edit_table[/title]-->
<!--DESCRIPTION: Таблица пунктов меню -->
<!--[content]-->
<table width=600  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=10%>Номер</td>
	<td width=10%>Активен</td>
	<td>Название</td>
	<td width=45%>Ссылка</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td align="center">[%row(position)%]</td>
	<td align="center">[%row(active,yes_no)%]</td>
	<td><b>[%row(title)%]</b></td>
	<td>[%func(get_menu_href)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_menu_item=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_menu_item=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_menu_item value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<center><a href=[%PHP_SELF%]?view_list=1  class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]menu_item_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования пунктов меню -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Номер:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[position] value="[%position%]"></td></tr>
	<tr class="content" align="left"><td>Активен:</td><td><input class="" type=checkbox maxlength="255" size="64" name=form[active] value="1" [%active%]></td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
 	<tr class="content" align="left"><td>Тип:</td><td>[%target_type_select%]</td></tr>
        <tr class="content" align="left" id="target_select"></tr>
	<tr class="content" align="left"><td>Флаг доступа:</td><td><select class="form-control" name=form[flag]><option value="">-</option>[%flag_select%]</select></td></tr>
	<tr class="content" align="left"><td>Подменю:</td><td>[%submenu_select%]</td></tr>
	<tr class="content" align="left"><td>Рисунок:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[css_class] value="[%css_class%]"></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href="[%PHP_SELF%]" class="btn btn-default"><<  Назад</a></center>

<script type="text/javascript">
function get_target_select () {
    var target_type = "";
    $("#target_type option:selected").each(function () {
        target_type = $(this).attr("value");
    });
    $('#target_select').load("[%PHP_SELF%]?get_target_select=1&item_id=[%id%]&target_type="+target_type);
}

$(document).ready(function(){
    get_target_select ();
    $('#target_type').change(function(){
        get_target_select ();
    });
});
</script>
<!--[/content]-->



<!--[title]messages_edit_table[/title]-->
<!--DESCRIPTION: Таблица сообщений -->
<!--[content]-->
<table width=90%  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td>Название</td>
	<td width=30%>Тип</td>
	<td width=30%>Сообщение</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b></td>
	<td>[%row(type)%]</td>
	<td>[%row(content)%]</td>
	<td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=adding value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->



<!--[title]messages_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования сообщений -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%form_type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Тип</td><td>[%types%]</td></tr>
	<tr class="content" align="left"><td colspan="2"><textarea class="form-control" name=form[content] rows=8 cols=90 maxlength=64000>[%content%]</textarea></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]news_edit_table[/title]-->
<!--DESCRIPTION: Список новостей в адм. разделе -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_news value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=40%>Заголовок</td>
	<td width=40%>Изображение</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b><br>[%row(descr)%]</td>
	<td align="center">[%func(show_img)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_news=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_news=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_news value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>

<!--[/content]-->



<!--[title]news_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования новостей -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" enctype="multipart/form-data">
<input type="hidden" name=MAX_FILE_SIZE value=16000000>		
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Заголовок:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>SEO:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class="content" align="left"><td>Ссылка:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[url] value="[%url%]"></td></tr>
	<tr class="content" align="left"><td>Изображение:</td><td><input class="form-control" name=img_file type=file size=40></td></tr>
	<tr class="content"><td align="left" colspan="2">[%content%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->





<!--[title]offers_edit_table[/title]-->
<!--DESCRIPTION: Список акций в адм. разделе -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_offers value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=40%>Заголовок</td>
	<td width=40%>Изображение</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b><br>[%row(descr)%]</td>
	<td align="center">[%func(show_img)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_offers=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_offers=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_offers value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>

<!--[/content]-->



<!--[title]offers_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования акций -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" enctype="multipart/form-data">
<input type="hidden" name=MAX_FILE_SIZE value=16000000>		
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Заголовок:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>SEO:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[seo_alias] value="[%seo_alias%]"></td></tr>
	<tr class="content" align="left"><td>Ссылка:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[url] value="[%url%]"></td></tr>
	<tr class="content" align="left"><td>Изображение:</td><td><input class="form-control" name=img_file type=file size=40></td></tr>
	<tr class="content"><td align="left" colspan="2">[%content%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->




<!--[title]partners_edit_table[/title]-->
<!--DESCRIPTION: Список партнеров -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_partner value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=10%>Номер</td>
	<td width=30%>Название</td>
	<td width=40%>Изображение</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td>[%row(pos)%]</td>
	<td><b>[%row(title)%]</b><br>[%row(descr)%]</td>
	<td align="center">[%func(show_img)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_partner=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_partner=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_partner value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<center><a href=[%PHP_SELF%]?list_gallery=1 class="btn btn-default"><<  Назад</a></center>

<!--[/content]-->



<!--[title]partners_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования данных о партнерах -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" enctype="multipart/form-data">
<input type="hidden" name=MAX_FILE_SIZE value=16000000>		
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Номер:</td><td><input class="form-control" type="edit" maxlength=2 size="64" name=form[pos] value="[%pos%]"></td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Ссылка:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[url] value="[%url%]"></td></tr>
	<tr class="content" align="left"><td>Изображение:</td><td><input class="form-control" name=img_file type=file size=40></td></tr>
	<tr class="content"><td align="left" colspan="2">[%descr%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]parts_edit_table[/title]-->
<!--DESCRIPTION: Таблица разделов -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=45%>Название</td>
	<td width=45%>URI</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b></td>
	<td>[%row(uri)%]</td>
	<td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=adding value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->



<!--[title]parts_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования разделов -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=90%  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>URI:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[uri] value="[%uri%]"></td></tr>
	<tr class="content" align="left"><td>Шаблон:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[tpl_name] value="[%tpl_name%]"></td></tr>
	<tr class="content" align="left"><td>Трубуются права:</td><td><select class="form-control" name=form[user_flag]><option value="">-</option>[%user_flags%]</select></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]settings_edit_table[/title]-->
<!--DESCRIPTION: Таблица настроек -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center><br>
<table width=90%  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td>Название</td>
	<td width=30%>Значение</td>
	<td width=30%>Описание</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b></td>
	<td>[%row(value)%]</td>
	<td>[%row(comment)%]</td>
	<td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center>
<!--[/content]-->



<!--[title]settings_edit_form[/title]-->
<!--DESCRIPTION: Редактирование настроек -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Значение</td><td><input class="form-control" type="edit" maxlength=1024 size="64" name=form[value] value="[%value%]"></td></tr>
	<tr class="content" align="left"><td>Описание:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[comment] value="[%comment%]"></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href="[%PHP_SELF%]" class="btn btn-default"> << Назад</a></center>
<!--[/content]-->


<!--[title]slider_images_edit_table[/title]-->
<!--DESCRIPTION: Список изображений в адм. разделе -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_image value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<br>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=10%>Номер</td>
	<td width=30%>Название</td>
	<td width=40%>Изображение</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td>[%row(pos)%]</td>
	<td><b>[%row(title)%]</b><br>[%row(descr)%]</td>
	<td align="center">[%func(show_img)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_image=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_image=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_image value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<center><a href=[%PHP_SELF%]?list_gallery=1 class="btn btn-default"><<  Назад</a></center>

<!--[/content]-->



<!--[title]slider_images_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования изображений -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST" enctype="multipart/form-data">
<input type="hidden" name=MAX_FILE_SIZE value=16000000>		
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Номер:</td><td><input class="form-control" type="edit" maxlength=2 size="64" name=form[pos] value="[%pos%]"></td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Ссылка:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[url] value="[%url%]"></td></tr>
	<tr class="content" align="left"><td>Изображение:</td><td><input class="form-control" name=img_file type=file size=40></td></tr>
	<tr class="content"><td align="left" colspan="2">[%descr%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->



<!--[title]stats_last_visitors_table[/title]-->
<!--DESCRIPTION: Последние поситители -->
<!--[content]-->
<center><h3>Последние 20 уникальных посетителей</h3></center>
<table width=800  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td width=15%>Дата</td>
<td width=15%>Хост</td>
<td width=55%>Браузер</td>
<td width=15%>Страница</td>
</tr>
[%loop_begin%]
        <tr class="content">
        <td align="center"><b>[%row(date)%]</b></td>
        <td align="center">[%row(remote_host)%]</td>
        <td align="center">[%row(user_agent)%]</td>
        <td align="center">[%row(request_uri)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]stats_addr_table[/title]-->
<!--DESCRIPTION: Статистика по IP -->
<!--[content]-->
<center><h5>Статистика по IP</h5></center>
<table width=400  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td width=70%>Хост</td>
<td width=30%>Посещений</td>
</tr>
[%loop_begin%]
        <tr class="content">
        <td align="center"><b>[%row(remote_addr)%]</b></td>
        <td align="center">[%row(hits)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]stats_day_table[/title]-->
<!--DESCRIPTION: Статистика по дням -->
<!--[content]-->
<center><h3>Статистика по дням</h3></center>
<table width=400  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td width=33%>День</td>
<td width=33%>Посетителей</td>
<td width=33%>Уникальных</td>
</tr>
[%loop_begin%]
        <tr class="content">
        <td align="center"><b>[%row(day)%]</b></td>
        <td align="center">[%row(hits)%]</td>
        <td align="center">[%row(unique_hits)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]stats_hosts_table[/title]-->
<!--DESCRIPTION: Статистика по хостам -->
<!--[content]-->
<center><h5>Статистика по хостам</h5></center>
<table width=400  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td width=70%>Хост</td>
<td width=30%>Посещений</td>
</tr>
[%loop_begin%]
        <tr class="content">
        <td align="center"><b>[%row(remote_host)%]</b></td>
        <td align="center">[%row(hits)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]stats_script_name_table[/title]-->
<!--DESCRIPTION: Статистика по страницам -->
<!--[content]-->
<center><h5>Статистика по страницам</h5></center>
<table width=400  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td width=70%>Страница</td>
<td width=30%>Посещений</td>
</tr>
[%loop_begin%]
        <tr class="content">
        <td align="center"><b>[%row(script_name)%]</b></td>
        <td align="center">[%row(hits)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->


<!--[title]stats_user_agent_table[/title]-->
<!--DESCRIPTION: Статистика по браузерам -->
<!--[content]-->
<center><h5>Статистика по браузерам</h5></center>
<table width=400  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td width=70%>Браузер</td>
<td width=30%>Посещений</td>
</tr>
[%loop_begin%]
        <tr class="content">
        <td align="center"><b>[%row(user_agent)%]</b></td>
        <td align="center">[%row(hits)%]</td>
        </tr>
[%loop_end%]
</table>
<!--[/content]-->



<!--[title]templates_edit_table[/title]-->
<!--DESCRIPTION:  Таблица шаблонов -->
<!--[content]-->
<center>
<form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить">
</form>
</center>
<br>
<table width=90%  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=40%>Название</td>
	<td width=40%>Описание</td>
	<td width=10%>Тип</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b></td>
	<td>[%row(comment)%]</td>
	<td>[%row(template_type)%]</td>
	<td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=adding value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->



<!--[title]templates_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования шаблона -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Описание:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[comment] value="[%comment%]"></td></tr>
	<tr class="content" align="left"><td>URI:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[uri] value="[%uri%]"></td></tr>
	<tr class="content" align="left"><td>Название файла:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[file_name] value="[%file_name%]"></td></tr>
	<tr class="content" align="left"><td>Тип:</td><td>
                <select class="form-control" name="form[template_type]">[%template_type_select%]</select>
        </td></tr>
	<tr class="content"><td align="left" colspan="2">
	<textarea class="form-control" id="editor_html" name=form[content] rows=25 cols=120 maxlength=64000>[%content%]</textarea>
	</td></tr>
	<tr class=header align="left"><td align="center" colspan="2">
                <input class="btn btn-primary" type="submit" name=update value="  Сохранить  "> 
                <input class="btn btn-primary" type="submit" value="  Сохранить и выйти ">
                <input class="btn btn-primary" type="submit" name=revert value="  Вернуть исходный  "> 
        </td></tr>
</table>
</form>		
<center><a href="[%PHP_SELF%]" class="btn btn-default"> << Назад</a></center>
[%file(tpl_help.txt)%]
<!--[/content]-->


<!--[title]users_edit_table[/title]-->
<!--DESCRIPTION: Таблица пользователей -->
<!--[content]-->
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center><br>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=45%>Логин</td>
	<td width=45%>Полное имя</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(login)%]</b></td>
	<td>[%row(fullname)%]</td>
	<td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center>
<!--[/content]-->



<!--[title]users_edit_form[/title]-->
<!--DESCRIPTION: Редактирование пользователей -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
        <tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
        <tr class="content" align="left"><td>Имя:</td><td><input class="form-control" type="edit" maxlength="16" name=form[login] value="[%login%]"></td></tr>
        <tr class="content" align="left"><td>Новый пароль:</td><td><input class="form-control" type=password maxlength="16" name=form[passwd] value=""></td></tr>
        <tr class="content" align="left"><td>Полное имя:</td><td><input class="form-control" type="edit" maxlength=254 name=form[fullname] value="[%fullname%]"></td></tr>
        <tr class="content" align="left"><td>E-Mail:</td><td><input class="form-control" type="edit" maxlength=32 name=form[email] value="[%email%]"></td></tr>
        <tr class="content" align="left"><td>Флаги:</td><td>[%flags%]</td></tr>
        <tr class="content" align="left"><td>Дата регистрации:</td><td>[%regdate%]</td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href="[%PHP_SELF%]" class="btn btn-default"> << Назад</a></center>
<!--[/content]-->



<!--[title]users_flags_edit_table[/title]-->
<!--DESCRIPTION: Таблица пользовательских флагов -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td>Название</td>
	<td width=30%>Значение</td>
	<td width=30%>Описание</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b>[%row(title)%]</b></td>
	<td>[%row(value)%]</td>
	<td>[%row(comment)%]</td>
	<td width=16><a href=[%PHP_SELF%]?view=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=adding value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center>
<!--[/content]-->


<!--[title]users_flags_edit_form[/title]-->
<!--DESCRIPTION: Форма редактирования флагов пользователей -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header><td class=header colspan="2">[%form_title%]</td></tr>
<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
<tr class="content" align="left"><td>Значение</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[value] value="[%value%]"></td></tr>
<tr class="content" align="left"><td>Описание:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[comment] value="[%comment%]"></td></tr>
<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href="[%PHP_SELF%]" class="btn btn-default"><<  Назад</a></center>
<!--[/content]-->


<!--[title]vote_list_edit_table[/title]-->
<!--DESCRIPTION: Список голосований -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=60%>Тема</td>
	<td width=15%>Вариантов</td>
	<td width=15%>Активно</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td><b><a href=[%PHP_SELF%]?view_vote=1&id=[%row(id)%]>[%row(title)%]</a></b></td>
	<td align="center">[%row(variants)%]</td>
	<td align="center">[%row(active,yes_no)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_vote=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_vote=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
	<input type="hidden" name=add_vote value=1>
	<input class="btn btn-primary" type="submit" value="Добавить">
</form></center>
<!--[/content]-->


<!--[title]vote_list_edit_form[/title]-->
<!--DESCRIPTION: Редактирование голосования -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%form_type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class="content" align="left"><td>Тип:</td><td><select class="form-control" name=form[type]>[%vote_type%]</select></tr>
	<tr class="content" align="left"><td>Активно</td><td><input class="" type=checkbox maxlength="255" size="64" name=form[active] value="1" [%active%]></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>
<!--[/content]-->


<!--[title]vote_variants_edit_table[/title]-->
<!--DESCRIPTION: Список вариантов -->
<!--[content]-->
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
	<td width=15% align="center">Номер</td>
	<td width=60%>Название</td>
	<td width=15% align="center">Голосов</td>
	<td width=5% align="center">&nbsp;</td>
	<td width=5% align="center">&nbsp;</td>
</tr>
[%loop_begin%]
	<tr class="content" align="left">
	<td align="center">[%row(num)%]</td>
	<td><b>[%row(title)%]</b></td>
	<td align="center">[%row(hits)%]</td>
	<td width=16><a href=[%PHP_SELF%]?edit_variant=1&id=[%row(id)%]><img src="../images/open.gif" alt="Изменить" border="0"></a></td>
	<td width=16><a href=[%PHP_SELF%]?del_variant=1&id=[%row(id)%]><img src="../images/del.gif" alt="Удалить" border="0" onClick="return test()"></a></td>
	</tr>
[%loop_end%]
</table>
<br>
<center><form action="[%PHP_SELF%]" method=get>
<input type="hidden" name=add_variant value=1>
<input class="btn btn-primary" type="submit" value="Добавить"></form>
</center>
<center><a href=[%PHP_SELF%]?list_vote=1 class="btn btn-default"> << Назад</a></center>
<!--[/content]-->


<!--[title]vote_variants_edit_form[/title]-->
<!--DESCRIPTION: Варианты голосования -->
<!--[content]-->
<form action="[%PHP_SELF%]" method="POST">
<input type="hidden" name="id" value=[%id%]>
<input type="hidden" name=[%type%] value=1>
<table width=500  class="table table-striped table-responsive table-bordered normal-form" align="center">
	<tr class=header><td colspan="2">[%form_title%]</td></tr>
	<tr class="content" align="left"><td>Номер:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[num] value="[%num%]"></td></tr>
	<tr class="content" align="left"><td>Название:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name=form[title] value="[%title%]"></td></tr>
	<tr class=header align="left"><td align="center" colspan="2"><input class="btn btn-primary" type="submit" value="  Сохранить  "></td></tr>
</table>
</form>
<center><a href=[%PHP_SELF%] class="btn btn-default"> << Назад</a></center>       
<!--[/content]-->


<!--[title]-[/title]-->
<!--DESCRIPTION:  -->
<!--[content]-->

<!--[/content]-->

<!--[title]request_list[/title]-->
<!--DESCRIPTION: Список заказов -->
<!--[content]-->
<br><center><table width=800  class="table table-responsive table-bordered normal-form" align="center">
<tr class=header align="center">
<td>Дата</td>
<td>Заказ</td>
<td>Контактная информация</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
[%loop_begin%]
        <tr [%row(active,if,bgcolor=#ffffff,bgcolor=#dddddd)%] align="left">
        <td align="center"><b>[%row(date)%]</b></td>
        <td>[%row(item_list,nl2br)%][%row(comment,nl2br)%][%func(file_info)%]</td>
        <td>[%row(contact_info,nl2br)%]</td>
        <td width=16><a href="[%PHP_SELF%]?active=Y&id=[%row(id)%]"><img src="../images/add.gif" alt="Активно" border="0" onClick="return test()"></a></td>
        <td width=16><a href="[%PHP_SELF%]?active=N&id=[%row(id)%]"><img src="../images/sub.gif" alt="Неактивно" border="0" onClick="return test()"></a></td>
        </tr>
[%loop_end%]
</table>

<!--[/content]-->



