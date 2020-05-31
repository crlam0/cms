<!-- ================== MENU ================== -->
<?php

use Classes\App;

function print_menu_item($href, $title, $img, $flag) {
    if ((strlen($flag)) && (!App::$user->haveFlag($flag))) {
        return '';
    }    
    return "<tr valign=middle><td align=center style='padding-bottom: 2px;'>"
    . "<a href=\"{$href}\"><img src=\"../images/{$img}\" border=0></a>"
    . "</td></tr>"
    . "<tr><td align=center style='padding-bottom: 5px;'><a href=\"{$href}\">{$title}</a></td></tr>\n";
}

function print_menu_adv_item($href, $title, $img, $flag) {
    if ((strlen($flag)) && (!App::$user->haveFlag($flag))) {
        return "";
    }
    return "<tr><td align=center style='padding-bottom: 5px;'><a href=\"{$href}\">{$title}</a></td></tr>\n";
}

echo "<table border=0 cellspacing=1 cellpadding=1 align=center width=135>";
echo print_menu_item("../", "На сайт", "admin_back.gif", "");
echo print_menu_item("index.php", "На Главную", "admin_banners.gif", "");
echo print_menu_item("cat_part_edit.php", "Каталог", "admin_cat.gif", "");
echo print_menu_item("cat_items_props_edit.php", "Прайс-лист", "admin_cat.gif", "");
echo print_menu_item("discount_edit.php", "Скидки", "admin_cat.gif", "");
echo print_menu_item("news_edit.php", "Новости", "admin_news.gif", "");
echo print_menu_item("offers_edit.php", "Акции", "admin_request.gif", "");
echo print_menu_item("reviews_edit.php", "Отзывы", "admin_users.gif", "");
echo print_menu_item("blog_edit.php", "Блог", "admin_text.gif", "");
echo print_menu_item("comments_edit.php", "Комментарии", "admin_menu.png", "");
echo print_menu_item("faq_edit.php", "Гостевая книга", "admin_faq.gif", "");
echo print_menu_item("article_edit.php", "Статьи", "admin_text.gif", "");
echo print_menu_item("partners_edit.php", "Партнеры", "admin_vacancy.gif", "");
echo print_menu_item("gallery_edit.php", "Фотографии", "admin_banners.gif", "");
echo print_menu_item("media_edit.php", "Файлы", "admin_files.gif", "");
echo print_menu_item("vote_edit.php", "Голосования", "admin_subscribe.gif", "");
echo print_menu_item("menu_edit.php", "Меню", "admin_menu.png", "");
echo print_menu_item("view_stats.php", "Статистика", "admin_discount.gif", "");
echo print_menu_item("users_edit.php", "Пользователи", "admin_users.gif", "global");
echo print_menu_item("../logout/", "Выход", "admin_exit.gif", "");
if (App::$user->haveFlag('global')){
    echo "<tr><td align=center><hr width=100%>Тонкие настройки</td></tr>";
}    
echo print_menu_adv_item("templates_edit.php", "Шаблоны", "admin_menu.gif", "global");
echo print_menu_adv_item("settings_edit.php", "Настройки", "admin_menu.gif", "global");
echo print_menu_adv_item("messages_edit.php", "Сообщения", "admin_menu.gif", "global");
echo print_menu_adv_item("parts_edit.php", "Разделы", "admin_menu.gif", "global");
echo print_menu_adv_item("users_flags_edit.php", "Флаги доступа", "admin_menu.gif", "global");
echo print_menu_adv_item("slider_edit.php", "Картинки в шапке", "admin_menu.gif", "global");
echo print_menu_adv_item("index.php?clear_cache=1", "Очистка кэша", "admin_menu.gif", "global");
echo print_menu_adv_item("sitemap_gen.php", "Генерация Sitemap", "admin_menu.gif", "global");

echo "</table><br>";
