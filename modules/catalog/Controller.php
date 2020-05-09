<?php

namespace modules\catalog;

use classes\BaseController;
use classes\App;
use classes\Pagination;
use classes\Image;

class Controller extends BaseController
{ 
    public static $cache_path = 'var/cache/catalog/';
    
    private function parseURI(string $uri): array 
    {
        $params = explode('/', $uri);
        $part_id = 0;
        $page = 1;
        foreach ($params as $alias) {
            if(preg_match("/^page\d{1,2}$/", $alias)) {
                $page = str_replace('page', '', $alias);
            } else {
                $query = "select id from cat_part where seo_alias like '$alias' and prev_id='{$part_id}'";
                $row = App::$db->getRow($query);
                if (is_numeric($row['id'])) {
                    $part_id = $row['id'];
                }
            }
        }
        return [$part_id, $page];
    }
    
    private function prev_part(int $prev_id, int $deep, array $arr): array 
    {        
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
    
    private function getHeaderBreadCrumbs(int $part_id, string $item_title = ''): array 
    {
        $root_title = isset(App::$settings['catalog_header']) ? App::$settings['catalog_header'] : 'Каталог';
        $title = $root_title;
        if ($part_id) {
            $breadcrumbs[] = ['title' => $root_title, 'url' => 'catalog/'];
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
                    $title = $root_title . " - {$row['title']}";
                } else {
                    // add_nav_item($row['title']);
                    $breadcrumbs[] = ['title' => $row['title']];
                    $title = $root_title . " - {$row['title']}";
                }
            }
        } else {
            $breadcrumbs[] = ['title' => $title];
        }
        return([$title,$breadcrumbs] );
    }
    
    private function getBackButton(int $part_id): string 
    {
        if ($part_id) {
            list($href_id) = App::$db->getRow("select prev_id from cat_part where id='{$part_id}'");
            return '
            <div class="cat_back">
                <center><a href="' . App::$SUBDIR . get_cat_part_href($href_id) . '" class="btn btn-default"> << Назад</a></center>
            </div>
            ';
        } else {
            return '';
        }    
    }
    
    public function actionPartList(string $uri): string
    {        
        list($part_id, $page) = $this->parseURI($uri);
        list($this->title,$this->breadcrumbs) = $this->getHeaderBreadCrumbs($part_id);
        $this->tags['INCLUDE_JS'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'modules/catalog/catalog.js"></script>' . "\n";
        
        $query = "SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='{$part_id}' group by cat_part.id order by cat_part.num,cat_part.title asc";
        $result =App::$db->query($query);

        $show_empty_message = true;
        if ($result->num_rows) {
            $tags['functions'] = [];
            $tags['cat_part_href'] = get_cat_part_href($part_id);
            $tags['this'] = $this;
            $content .= App::$template->parse('cat_part_list', $tags, $result);
            $show_empty_message = false;
        } 
        $content .= $this->getPartItemsContent($part_id, $page, $show_empty_message);
        $content .= $this->getBackButton($part_id);
        return $content;
    }
    
    public function actionIndex(): string
    {
        list($this->title,$this->breadcrumbs) = $this->getHeaderBreadCrumbs(0);
        return $this->actionPartList('');
    }

    private function getPartItemsContent(int $part_id, int $page, bool $show_empty_message = true): string 
    {
        global $_SESSION;
        
        $content = '';
        
        $row_part = App::$db->getRow("select * from cat_part where id='{$part_id}'");
        
        if (isset($row_part['descr']) && strlen($row_part['descr'])) {
            $tags['part_descr'] = $row_part['descr'];
        }
        if (!isset($_SESSION['catalog_page'])){
            $_SESSION['catalog_page'] = 1;
        }
        if ($page ) {
            $_SESSION['catalog_page'] = $page;
        }
        list($total) = App::$db->getRow("SELECT count(id) from cat_item where part_id='" . $part_id . "'");

        $pager = new Pagination($total, $_SESSION['catalog_page'], App::$settings['catalog_items_per_page']);
        $tags['pager'] = $pager;

        $query = "select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
                left join cat_item_images on (cat_item_images.id=default_img)
                where part_id='" . $part_id . "'
                group by cat_item.id   
                order by cat_item.num,b_code,title asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            $tags['cat_part_href'] = get_cat_part_href($part_id);
            $tags['functions'] = [];
            $tags['this'] = $this;
            $content .= App::$template->parse('cat_item_list', $tags, $result);
        } elseif ($show_empty_message) {
            $tags['title'] = $row_part['title'];
            $tags['image_name'] = $row_part['image_name'];
            $content .= App::$template->parse('cat_item_list_empty.html.twig', $tags);
        }        
        return $content;
    }
    
    private function getItemId(int $part_id, string $item_title): int 
    {
        $query = "select id,part_id from cat_item where seo_alias = '{$item_title}' and part_id='{$part_id}'";
        $row = App::$db->getRow($query);
        if (is_numeric($row['id'])) {
            return $row['id'];
        }
        return 0;
    }
    
    private function getRelatedProducts(array $row_part)
    {        
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
                
                return App::$template->parse('cat_item_list', ['this' => $this], $result);
            }
        }
        return null;
    }
    
    public function actionItem(string $uri, string $item_title): string
    {        
        list($part_id, $page) = $this->parseURI($uri);
        list($this->title,$this->breadcrumbs) = $this->getHeaderBreadCrumbs($part_id, $item_title);

        $item_id = $this->getItemId($part_id, $item_title);

        $query = "select cat_item.*,fname,file_type,cat_item_images.descr as image_descr,cat_item_images.id as cat_item_images_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='" . $item_id . "'";
        $result = App::$db->query($query);
        
        if (!$result->num_rows) {
            $tags['Header'] = 'Ошибка 404';
            $tags['file_name'] = App::$server['REQUEST_URI'];
            header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
            return  App::$message->get('notice', [], 'Товар не найден');
        }
        $tags = $result->fetch_array();
        $row_part = App::$db->getRow("select * from cat_part where id='{$part_id}'");

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
        $tags['this'] = $this;
        return App::$template->parse('cat_item_view', $tags, $result);        
    }
    
    public function actionLoadImage(): string            
    {
        $input = App::$input;
        $query = "select default_img,fname,cat_item.title from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='{$input['item_id']}'";
        list($default_img,$default_img_fname,$title)=App::$db->getRow($query);

        $nav_ins = '';

        list($prev_id,$fname) = App::$db->getRow("select id,fname from cat_item_images where item_id='" . $input['item_id'] . "' and id<'" . $input['image_id'] . "' and id<>'{$default_img}' order by id desc limit 1");
        if ($input['image_id'] != $default_img){
            if ($prev_id){
                $nav_ins.= "<a image_id={$prev_id} item_id={$input['item_id']} file_name={$fname} class=\"cat_image_button btn btn-default\"><< Предыдущая</a>";
            }else{     
                $nav_ins.= "<a image_id={$default_img} item_id={$input["item_id"]} file_name=\"{$default_img_fname}\" class=\"cat_image_button btn btn-default\"><< Предыдущая</a>";
            }
            list($next_id,$fname) = App::$db->getRow("select id,fname from cat_item_images where item_id='" . $input['item_id'] . "' and id>'" . $input['image_id'] . "' and id<>'{$default_img}' order by id asc limit 1");
            if ($next_id) {
                $nav_ins.= "<a image_id={$next_id} item_id={$input['item_id']} file_name={$fname} class=\"cat_image_button btn btn-default\">Следующая >></a>";
            }
        }else{
            list($next_id,$fname) = App::$db->getRow("select id,fname from cat_item_images where item_id='" . $input['item_id'] . "' and id<>'{$default_img}' order by id asc limit 1");
            if ($next_id) {
                $nav_ins.= "<a image_id={$next_id} item_id={$input['item_id']} file_name={$fname} class=\"cat_image_button btn btn-default\">Следующая >></a>";
            }
        }

        $URL = $this->getImageUrl($input['file_name'], '', App::$settings['catalog_item_img_max_width'], 0);

        $content .= '<center><img src="' . APP::$SUBDIR . $URL .'" border="0" alt="' . $title . '"></center>';
        if(strlen($nav_ins)){
            $content.="<br /><center>{$nav_ins}</center>";
        }

        $json['title'] = $title;
        $json['content'] = $content;
        echo json_encode($json);
        exit;        
    }
    
    /*  ???  */
    public function getPropValue($row,$name) {
        if($props_values = my_json_decode($row['props'])) {
            $result = $props_values[$name];        
            return strlen($result)>0 ? $result : false;
        }
        return false;
    }

    /*  +++ */
    public function getPropsArray($props) {    
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

    /*  ??? */
    public function getPropName($part_id,$name) {
        $query = "select items_props from cat_part where id='{$part_id}'";
        list($items_props) = my_select_row($query, true);
        if($props_values = my_json_decode($items_props)) {
            return $props_values[$name]['name'];
        }
        return false;
    }

    /*  ??? */
    public function getPropNamesArray($part_id) {
        $query = "select items_props from cat_part where id='{$part_id}'";
        list($items_props) = my_select_row($query, true);
        if($props_values = my_json_decode($items_props)) {        
            $result=[];
            foreach($props_values as $name){
                $result[$name]=$props_values[$name]['name'];
            }
            return $result;
        }
        return false;    
    }
    
    public static function getCacheFilename($file_name, $file_type, $max_width) {
        if(!$file_type || !strlen($file_type)) {
            $file_type = Image::getFileType($file_name, '');
        }
        $file_extension = Image::getFileExt($file_type);
        $IMG_ITEM_PATH = App::$DIR . App::$settings['catalog_item_img_path'];
        $file_name = $IMG_ITEM_PATH . $file_name;
        return static::$cache_path . md5($file_name . $max_width) . '.' . $file_extension;
    }
    
    public function getImageUrl($file_name, $file_type, $width, $crop = true) {
        $cache_file_name = $this->getCacheFilename($file_name, $file_type, $width);
        if(is_file(App::$DIR . $cache_file_name)) {
            return $cache_file_name;
        } else {
            return "modules/catalog/image.php?file_name={$file_name}&preview={$width}&crop={$crop}";
        }
    }

    public function getImageFilename($file_name, $file_type, $width = 0, $crop = true) {
        $IMG_ITEM_PATH = App::$DIR . App::$settings['catalog_item_img_path'];
        if(!$width) {
            $width = App::$settings['catalog_item_img_preview'];
        }        
        if (is_file($IMG_ITEM_PATH . $file_name)) {
            return $this->getImageUrl($file_name, $file_type, $width, $crop);
        } else {
            return false;
        }
    }

    public function getListImage($row) {
        App::$input['preview']=true;
        $file_name = App::$DIR . App::$settings['catalog_item_img_path'] . $row['fname'];
        $image = new Image($file_name, $row['file_type']);
        $cript_name = 'modules/catalog/image.php?preview='.App::$settings['catalog_item_img_preview'].'&crop=1&id=' . $row['image_id'];
        return $image->getHTML($row, static::$cache_path, 'catalog_popup', $cript_name, App::$settings['catalog_item_img_preview']);
    }

    public function getPartImageFilename($fname, $width = 0) {
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

    public function getItemsCount($id) {
        global $_SESSION;
        return $_SESSION['BUY'][$id]['count'];
    }    
    
}

