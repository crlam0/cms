<?php

namespace Classes;
use Classes\MyGlobal;

/**
 * Implements work with simple blocks
 *
 */
class Blocks {
    /**
     * Return menu content
     *
     * @param integer $menu_id Menu ID
     * @param string $attr_ul UL CSS attributes
     * @param string $attr_li LI CSS attributes
     *
     * @return string Menu content
     */
    private function get_menu_items($menu_id, $attr_ul = '', $attr_li = '') {
        global $_SESSION, $SUBDIR;
        if (!$menu_id){
            return '';
        }
        if(array_key_exists('FLAGS',$_SESSION)){
            $where_add="'" . $_SESSION['FLAGS'] . "' LIKE concat('%',flag,'%') AND ";
        } else {
            $where_add = '';
        }
        $query = "SELECT * FROM menu_item WHERE {$where_add} menu_id='{$menu_id}' AND active=1 ORDER BY position ASC";
        $result = MyGlobal::get('DB')->query($query, true);
        $output = '';
        if ($result->num_rows) {
            $output.="<ul {$attr_ul}>\n";
            while ($row = $result->fetch_array()) {
                $tmp = null;
                $href = get_menu_href($tmp, $row);
                if (preg_match('/^http.?:\/\/.+$/', $href)) {
                    $target_inc = ' target="_blank"';
                } else {
                    $href = $SUBDIR . $href;
                    $target_inc = '';
                }
                $output.="<li {$attr_li}><a href=\"{$href}\"{$target_inc} title=\"{$row['title']}\">{$row['title']}</a>";
                $output.=$this->get_menu_items($row['submenu_id']);
                $output.="</li>\n";
            }
            $output.="</ul>\n";
        }
        return $output;
    }
    
    private function menu_main () {
        list($menu_id) = my_select_row("SELECT id FROM menu_list WHERE root=1", 1);
        $tags['menu_content'] = $this->get_menu_items($menu_id, 'id="mainmenu"', '');
        return get_tpl_by_title('block_menu', $tags);
    }
    private function menu_top () {
        list($menu_id) = my_select_row("SELECT id FROM menu_list WHERE top_menu=1", 1);
        return $this->get_menu_items($menu_id, 'class="menu"', 'class="menu-item"');
    }
    private function menu_bottom () {
        list($menu_id) = my_select_row("SELECT id FROM menu_list WHERE bottom_menu=1", 1);
        return $this->get_menu_items($menu_id, 'id="menu-footer" class="menu"', 'class="menu-item"');
    }
    
    private function vote () {
        $query = "SELECT id,title,type FROM vote_list WHERE active=1 limit 1";
        $result = MyGlobal::get('DB')->query($query, true);
        if ($result->num_rows) {
            list($vote_id, $title, $type) = $result->fetch_array();
            $tags['vote_title'] = $title;
            $tags['variants'] = '';
            $query = "SELECT * FROM vote_variants WHERE vote_id='{$vote_id}'";
            $result = MyGlobal::get('DB')->query($query, true);
            if (!$result->num_rows){
                return null;
            }    
            $i = 0;
            while ($row = $result->fetch_array()) {
                $tags['variants'].="
                    <div class=vote_variant>
                    <input type={$type} name=vote[] value={$row['id']}" . ((!$i) && ($type == "radio") ? " checked" : "") . ">{$row['title']}
                    </div>\n";
                $i++;
            }
            return get_tpl_by_title('block_vote', $tags);
        }
        return null;
    
    }
    
    private function slider () {
        global $server, $SUBDIR;
        
        $SCRIPT = $server['SCRIPT_NAME'];
        if (strlen($SUBDIR) > 1){
            $SCRIPT = str_replace($SUBDIR, "/", $SCRIPT);
        }

        if ($SCRIPT == '/index.php') {
            $query = "SELECT * FROM slider_images WHERE length(file_name)>0 ORDER BY pos,title ASC";
            $result = MyGlobal::get('DB')->query($query, true);
            return get_tpl_by_title('slider_items', [], $result);
        } else {
            return '';
        }
    }
    
    private function news() {
        global $settings;
        
        $query = "select *,date_format(date,'%d.%m.%Y') as date from news order by id desc limit {$settings['news_block_count']}";
        $result = MyGlobal::get('DB')->query($query);
        if ($result->num_rows) {

            function get_news_short_content($tmp, $row) {
                return cut_string($row['content'], 100);
            }

            return get_tpl_by_title('block_news', [], $result);
        }
        return null;
    }

    private function last_posts () {
        global $settings;
        $TABLE = 'blog_posts';
        $query = "SELECT {$TABLE}.*,date_format(date_add,'%d.%m.%Y') as date from {$TABLE} where active='Y' order by {$TABLE}.id desc limit {$settings['news_block_count']}";
        $result = MyGlobal::get('DB')->query($query, true);
        if ($result->num_rows) {
            function get_news_short_content($tmp, $row) {
                return cut_stringing($row['content'], 100);
            }
            return get_tpl_by_title('block_last_posts', [], $result);
        }
        return null;
    }
    
    
    /**
     * Return block content
     *
     * @param string $block_name Block name
     *
     * @return string Block content
     */
    public function content($block_name) {
        global $settings, $DEBUG, $DIR, $SUBDIR;
        
        add_to_debug('Parse block ' . $block_name);        
        switch ($block_name) {
            
            case 'partners':
                $query = "SELECT * FROM partners order by pos asc";
                $result = MyGlobal::get('DB')->query($query, null);
                return get_tpl_by_title('block_partners', [], $result);

            case 'banners':
                if(file_exists($DIR . 'bannners.local.php')) {
                    ob_start();
                    include_once($DIR . 'bannners.local.php');
                    $content = ob_get_contents();
                    ob_end_clean();
                return $content;
                } else {
                    return null;
                }

            case 'calendar':
                ob_start();
                show_month(date('n'), 0);
                $content = ob_get_contents();
                ob_end_clean();
                return $content;

            case 'menu_admin':
                ob_start();
                include_once($DIR . 'admin/nav.php');
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
                
            case 'debug':
                if ($settings['debug']) {
                    ob_start();
                    print_array($DEBUG);
                    $content = ob_get_contents();
                    ob_end_clean();
                    return $content;
                }
                return '';
                
            default:
                if(\is_callable([$this,$block_name])) {
                   return $this->$block_name();
                }                
                $tags['title'] = $block_name;
                return my_msg_to_str('block_not_found', $tags);
            
        }
    
    }
    
}
