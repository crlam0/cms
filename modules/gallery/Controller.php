<?php

namespace modules\gallery;

use Classes\BaseController;
use Classes\App;
use Classes\Comments;
use Classes\Pagination;
use Classes\Image;

include 'functions.php';

/**
 * Description of Controller
 *
 * @author User
 */
class Controller extends BaseController {
    
    public function actionPartList()
    {
        $this->title = 'Галерея';
        $this->breadcrumbs[] = ['title'=>$this->title];        
        $query = "SELECT 
                gallery_list.*,count(images.id) AS images,max(images.date_add) AS last_images_date_add,
                def_img.id as def_id,def_img.file_name as def_file_name
            FROM gallery_list
            LEFT JOIN gallery_images AS images ON (images.gallery_id=gallery_list.id)
            LEFT JOIN gallery_images AS def_img ON (def_img.id=default_image_id)
            WHERE gallery_list.active='Y'
            GROUP BY gallery_list.id ORDER BY last_images_date_add DESC,gallery_list.date_add DESC";

        $result = App::$db->query($query);
        if (!$result->num_rows) {
            return App::$message->get('part_empty');
        } else {
            return App::$template->parse('gallery_part_list', [], $result);
        }
    }
    
    public function actionImagesList($alias, $page = 1)
    {
        $view_gallery = get_id_by_alias('gallery_list', $alias, true);            
        list($gallery_title, $gallery_seo_alias) = App::$db->select_row("SELECT title, seo_alias from gallery_list where id='{$view_gallery}'");
        
        $this->title = $gallery_title;
        $this->breadcrumbs[] = ['title' => 'Галерея', 'url'=>'gallery/'];        
        $this->breadcrumbs[] = ['title' => $gallery_title];
        $this->tags['INCLUDE_JS'] .= '<script type="text/javascript" src="'.App::$SUBDIR.'modules/gallery/gallery.js"></script>'."\n";

        list($total) =  App::$db->select_row("SELECT count(id) from gallery_images where gallery_id='{$view_gallery}'");
        $pager = new Pagination($total, $page, App::$settings['gallery_images_per_page']);
        $tags['pager'] = $pager;
        $tags['gallery_list_href'] = 'gallery/'.$gallery_seo_alias.'/';

        $query = "SELECT * from gallery_images where gallery_id='" . $view_gallery . "' order by id asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);
        if (!$result->num_rows) {
            $content = App::$message->get('list_empty', [], '');
        } else {
            $content = App::$template->parse('gallery_images_list', $tags, $result);
        }

        if(App::$settings['gallery_use_comments']) {
            $comments = new Comments ('gallery',$view_gallery);
            $comments->get_form_data(App::$input);
            $content.=$comments->show_list();
            $tags['action']=App::$SUBDIR . $tags['gallery_list_href'] . '#comments';
            $content.=$comments->show_form($tags);
        }
        return $content;
    }
    
    public function actionLoad()
    {
        $query = "SELECT * from gallery_images where id='".App::$input['id']."'";
        $tags = App::$db->select_row($query);
        $gallery_id = $tags['gallery_id'];

        list($tags['prev_id']) = App::$db->select_row("select id from gallery_images where gallery_id='{$gallery_id}' and id<'{$tags['id']}' order by id desc limit 1");
        list($tags['next_id']) = App::$db->select_row("select id from gallery_images where gallery_id='{$gallery_id}' and id>'{$tags['id']}' order by id asc limit 1");

        $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $tags['file_name'];
        $image = new Image($file_name, $tags['file_type']);
        $tags['IMAGE'] = $image->getHTML($tags, 'var/cache/gallery/','','modules/gallery/image.php?clientHeight='.App::$input['clientHeight'].'&id=', gallery_get_max_width());

        $json=$tags;
        $json['content'] = App::$template->parse('gallery_image_view', $tags);
        echo json_encode($json);
        exit();
    }
    
}
