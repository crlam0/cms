<?php

namespace modules\gallery\controllers;

use classes\App;
use classes\BaseController;
use classes\Comments;
use classes\Pagination;
use classes\Image;

/**
 * Description of Controller
 *
 * @author User
 */
class Controller extends BaseController
{

    public static $cache_path = 'var/cache/gallery/';

    public function actionPartList(): string
    {
        $this->title = 'Галерея';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "SELECT 
                gallery_list.*,count(images.id) AS images,max(images.date_add) AS last_images_date_add,
                def_img.id as def_id,def_img.file_name as def_file_name,def_img.file_type as def_file_type 
            FROM gallery_list
            LEFT JOIN gallery_images AS images ON (images.gallery_id=gallery_list.id)
            LEFT JOIN gallery_images AS def_img ON (def_img.id=default_image_id)
            WHERE gallery_list.active='Y'
            GROUP BY gallery_list.id ORDER BY last_images_date_add DESC,gallery_list.date_add DESC";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            return $this->render('gallery_part_list', [], $result);
        } else {
            return App::$message->get('list_empty');
        }
    }

    public function actionImagesList(string $alias, int $page = 1): string
    {
        $view_gallery = get_id_by_alias('gallery_list', $alias, true);
        list($gallery_title, $gallery_seo_alias) = App::$db->getRow("SELECT title, seo_alias from gallery_list where id='{$view_gallery}'");

        $this->title = $gallery_title;
        $this->breadcrumbs[] = ['title' => 'Галерея', 'url'=>'gallery/'];
        $this->breadcrumbs[] = ['title' => $gallery_title];
        App::addAsset('js', 'modules/gallery/gallery.js');

        list($total) =  App::$db->getRow("SELECT count(id) from gallery_images where gallery_id='{$view_gallery}'");
        $pager = new Pagination($total, $page, App::$settings['gallery_images_per_page']);
        $tags['pager'] = $pager;
        $tags['gallery_list_href'] = 'gallery/'.$gallery_seo_alias.'/';

        $query = "SELECT * from gallery_images where gallery_id='" . $view_gallery . "' order by id asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);
        if (!$result->num_rows) {
            $content = App::$message->get('list_empty');
        } else {
            $content = $this->render('gallery_images_list', $tags, $result);
        }

        if (App::$settings['gallery_use_comments']) {
            $comments = new Comments('gallery', $view_gallery);
            $comments->get_form_data(App::$input['form']);
            $content.=$comments->show_list();
            $tags['action']=App::$SUBDIR . $tags['gallery_list_href'] . '#comments';
            $content.=$comments->show_form($tags);
        }
        return $content;
    }

    public function actionLoad(): array
    {
        $query = "SELECT * from gallery_images where id='".App::$input['id']."'";
        $tags = App::$db->getRow($query);
        $gallery_id = $tags['gallery_id'];

        list($tags['prev_id']) = App::$db->getRow("select id from gallery_images where gallery_id='{$gallery_id}' and id<'{$tags['id']}' order by id desc limit 1");
        list($tags['next_id']) = App::$db->getRow("select id from gallery_images where gallery_id='{$gallery_id}' and id>'{$tags['id']}' order by id asc limit 1");

        $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $tags['file_name'];
        $image = new Image($file_name, $tags['file_type']);
        $tags['IMAGE'] = $image->getHTML($tags, 'var/cache/gallery/', '', 'modules/gallery/image.php?clientHeight='.App::$input['clientHeight'].'&id=' . $tags['id'], $this->getMaxWidth());

        $json = $tags;
        $json['content'] = $this->render('gallery_image_view', $tags);
        return $json;
    }

    public static function getMaxWidth()
    {
        if (App::$input['preview']) {
            $max_width = App::$settings['gallery_max_width_preview'];
        } else {
            $max_width = App::$settings['gallery_max_width'];
        }
        if (App::$input['icon'] && App::$settings['gallery_icon_width']) {
            $max_width = App::$settings['gallery_icon_width'];
        }
        if (App::$input['width'] && is_integer(App::$input['width'])) {
            $max_width = App::$input['width'];
        }
        return $max_width;
    }

    public function getImage($row): string
    {
        App::$input['preview']=true;
        $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $row['file_name'];
        $image = new Image($file_name, $row['file_type']);
        return $image->getHTML($row, static::$cache_path, 'gallery_popup', 'modules/gallery/image.php?preview=1&id=' . $row['id'], $this->getMaxWidth());
    }

    public function getListImage($row): string
    {
        if (!$row['def_file_name']) {
            return 'Изображение отсутствует';
        }
        App::$input['icon']=true;
        $row['file_name'] = $row['def_file_name'];
        $row['id'] = $row['def_id'];
        $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $row['file_name'];
        $image = new Image($file_name, $row['def_file_type']);
        return $image->getHTML($row, static::$cache_path, '', 'modules/gallery/image.php?icon=1&id=' . $row['id'], $this->getMaxWidth());
    }

    public function getIcons($row): string
    {
        $content='';
        App::$input['icon']=true;
        $query="select * from gallery_images where gallery_id='{$row['id']}' limit 6";
        $result = App::$db->query($query);
        while ($row = $result->fetch_array()) {
            $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $row['file_name'];
            $image = new Image($file_name, $row['file_type']);
            $content .= $image->getHTML($row, static::$cache_path, 'list_icon', 'modules/gallery/image.php?preview=1&id=' . $row['id'], $this->getMaxWidth());
        }
        return $content;
    }
}
