<?php

namespace modules\catalog;

use Classes\BaseController;
use Classes\App;
use Classes\Pagination;

include 'functions.php';

class Controller extends BaseController
{ 
    
    private function parseURI($uri) {
        $params = explode('/', $uri);
        $part_id = 0;
        foreach ($params as $alias) {
            if(preg_match("/^page\d{1,2}$/", $alias)) {
                $page = str_replace('page','',$alias);
            } else {
                $query = "select id from cat_part where seo_alias like '$alias' and prev_id='{$part_id}'";
                $row = App::$db->select_row($query, true);
                if (is_numeric($row['id'])) {
                    $part_id = $row['id'];
                }
            }
        }
        return [$part_id, $page];
    }
    
    private function prev_part($prev_id, $deep, $arr) {        
        $query = "SELECT id,title,prev_id from cat_part where id='{$prev_id}' order by title asc";
        $result = App::$db->query($query);
        if (!$result->num_rows) {
            return null;
        }
        $arr[$deep] = $result->fetch_array();
        if ($arr[$deep]['prev_id']) {
            $arr = $this->prev_part($arr[$deep]['prev_id'], $deep + 1, $arr);
        }
        return $arr;
    }
    
    private function getHeaderBreadCrumbs($part_id, $item_title = '') {
        $title = isset(App::$settings['catalog_header']) ? App::$settings['catalog_header'] : 'Каталог';
        if ($part_id) {
            $breadcrumbs[] = ['title' => $title, 'url' => 'catalog/'];
            $arr = $this->prev_part($part_id, 0, []);
            $arr = array_reverse($arr);
            $max_size = sizeof($arr) - 1;
            $current_part_deep = 0;
            // print_array($arr);
            while (list ($n, $row) = @each($arr)) {
                $current_part_deep++;
                if (($n < $max_size) || (strlen($item_title))) {
                    // add_nav_item($row['title'], get_cat_part_href($row['id']));
                    $breadcrumbs[] = ['title' => $row['title'], 'url' => get_cat_part_href($row['id'])];
                    $title .= " - {$row['title']}";
                } else {
                    // add_nav_item($row['title']);
                    $breadcrumbs[] = ['title' => $row['title']];
                }
            }
        } else {
            $breadcrumbs[] = ['title' => $title];
        }
        return([$title,$breadcrumbs] );
    }
    
    private function getBackButton($part_id) {
        if ($part_id) {
            list($href_id) = App::$db->select_row("select prev_id from cat_part where id='{$part_id}'", true);
            return '
            <div class="cat_back">
                <center><a href="' . App::$SUBDIR . get_cat_part_href($href_id) . '" class="btn btn-default"> << Назад</a></center>
            </div>
            ';
        }    
    }
    
    public function actionPartList($uri)
    {        
        list($part_id, $page) = $this->parseURI($uri);
        list($this->title,$this->breadcrumbs) = $this->getHeaderBreadCrumbs($part_id);
        $this->tags['INCLUDE_JS'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'modules/catalog/catalog.js"></script>' . "\n";
        
        $query = "SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='{$part_id}' group by cat_part.id order by cat_part.num,cat_part.title asc";
        $result =App::$db->query($query);

        if ($result->num_rows) {
            $tags['functions'] = [];
            // $tags['cat_part_href'] = get_cat_part_href($part_id);
            $content .= App::$template->parse('cat_part_list', $tags, $result);
        } 
        $content .= $this->getPartItemsContent($part_id, $page);
        $content .= $this->getBackButton($part_id);
        return $content;
    }
    
    public function actionIndex()
    {
        list($this->title,$this->breadcrumbs) = $this->getHeaderBreadCrumbs(null);
        return $this->actionPartList('');
    }

    private function getPartItemsContent($part_id, $page) {
        global $_SESSION;
        
        $content = '';
        
        $row_part = App::$db->select_row("select * from cat_part where id='{$part_id}'");
        
        if (isset($row_part['descr']) && strlen($row_part['descr'])) {
            $tags['part_descr'] = $row_part['descr'];
        }
        if (!isset($_SESSION['catalog_page'])){
            $_SESSION['catalog_page'] = 1;
        }
        if ($page ) {
            $_SESSION['catalog_page'] = $page;
        }
        list($total) = App::$db->select_row("SELECT count(id) from cat_item where part_id='" . $part_id . "'");

        $pager = new Pagination($total,$_SESSION['catalog_page'],App::$settings['catalog_items_per_page']);
        $tags['pager'] = $pager;

        $query = "select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
                left join cat_item_images on (cat_item_images.id=default_img)
                where part_id='" . $part_id . "'
                group by cat_item.id   
                order by cat_item.num,b_code,title asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query, true);
        if ($result->num_rows) {
            $tags['cat_part_href'] = get_cat_part_href($part_id);
            $tags['functions'] = [];
            $content .= App::$template->parse('cat_item_list', $tags, $result);
        } else {
            $tags['title'] = $row_part['title'];
            $tags['image_name'] = $row_part['image_name'];
            $content .= App::$template->parse('cat_item_list_empty.html.twig', $tags);
        }        
        return $content;
    }
    
    private function getItemId($part_id, $item_title) {
        $query = "select id,part_id from cat_item where seo_alias = '{$item_title}' and part_id='{$part_id}'";
        $row = App::$db->select_row($query, true);
        if (is_numeric($row['id'])) {
            return $row['id'];
        }
        return 0;
    }
    
    private function getRelatedProducts($row_part) {        
        if(!$related_products = my_json_decode($row_part['related_products'])) {
            $related_products=[];
        }
        if(count($related_products)) {
            $where_str=implode(',',array_keys($related_products));
            $query ="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
                left join cat_item_images on (cat_item_images.id=default_img)
                where cat_item.id in (" . $where_str . ")
                group by cat_item.num   
                order by cat_item.num,b_code,title asc";
            $result = App::$db->query($query);
            if ($result->num_rows) {
                return App::$template->parse('cat_item_list', $tags, $result);
            }
        }
        return null;
    }
    
    public function actionItem($uri, $item_title)
    {        
        list($part_id, $page) = $this->parseURI($uri);
        list($this->title,$this->breadcrumbs) = $this->getHeaderBreadCrumbs($part_id, $item_title);

        $item_id = $this->getItemId($part_id, $item_title);

        $query = "select cat_item.*,fname,cat_item_images.descr as image_descr,cat_item_images.id as cat_item_images_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='" . $item_id . "'";
        $result = App::$db->query($query);
        
        if (!$result->num_rows) {
            $tags['Header'] = 'Ошибка 404';
            $tags['file_name'] = App::$server['REQUEST_URI'];
            header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
            return  App::$message->get('notice', [], 'Товар не найден');
        }
        $tags = $result->fetch_array();
        $row_part = App::$db->select_row("select * from cat_part where id='{$part_id}'");

        $this->title = $tags['title'];
        $this->breadcrumbs[] = ['title' => $tags['title']];
        $this->tags['INCLUDE_JS'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'modules/catalog/catalog.js"></script>' . "\n";

        $tags['related_products'] = $this->getRelatedProducts($row_part);
        $query = "select * from cat_item_images where item_id='{$tags['id']}' and id<>'{$tags['default_img']}' order by id asc";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            $tags['images'] = $result->fetch_all(MYSQLI_ASSOC);
        }
        if($_SESSION['catalog_page']>1) {
            $tags['page'] = 'page' . $_SESSION['catalog_page'] . '/';
        }
        return App::$template->parse('cat_item_view', $tags, $result);        
    }
    
    public function actionLoadImage()
    {
        list($default_img,$default_img_fname,$title)=App::$db->select_row("select default_img,fname,cat_item.title from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='".$input['item_id']."'");

        $nav_ins = '';
        $input = App::$input;

        list($prev_id,$fname) = App::$db->select_row("select id,fname from cat_item_images where item_id='" . $input['item_id'] . "' and id<'" . $input['image_id'] . "' and id<>'{$default_img}' order by id desc limit 1");
        if ($input['image_id'] != $default_img){
            if ($prev_id){
                $nav_ins.= "<a image_id={$prev_id} item_id={$input['item_id']} file_name={$fname} class=\"cat_image_button btn btn-default\"><< Предыдущая</a>";
            }else{     
                $nav_ins.= "<a image_id={$default_img} item_id={$input["item_id"]} file_name=\"{$default_img_fname}\" class=\"cat_image_button btn btn-default\"><< Предыдущая</a>";
            }
            list($next_id,$fname) = App::$db->select_row("select id,fname from cat_item_images where item_id='" . $input['item_id'] . "' and id>'" . $input['image_id'] . "' and id<>'{$default_img}' order by id asc limit 1");
            if ($next_id) {
                $nav_ins.= "<a image_id={$next_id} item_id={$input['item_id']} file_name={$fname} class=\"cat_image_button btn btn-default\">Следующая >></a>";
            }
        }else{
            list($next_id,$fname) = App::$db->select_row("select id,fname from cat_item_images where item_id='" . $input['item_id'] . "' and id<>'{$default_img}' order by id asc limit 1");
            if ($next_id) {
                $nav_ins.= "<a image_id={$next_id} item_id={$input['item_id']} file_name={$fname} class=\"cat_image_button btn btn-default\">Следующая >></a>";
            }
        }

        $URL = get_item_image_url($input['file_name'], 500, 0);

        $content = '<center><img src="'.APP::$SUBDIR . $URL .'" border="0"></center>';
        if(strlen($nav_ins)){
            $content.="<br /><center>{$nav_ins}</center>";
        }

        $json['title'] = $title;
        $json['content'] = $content;
        echo json_encode($json);
        exit;        
    }
    
}

