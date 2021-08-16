<?php

namespace local;

use classes\App;

final class MainMenuWidgetSample {
    
    public function run() {
        list($menu_id) = App::$db->getRow("SELECT id FROM menu_list WHERE root=1");
        return $this->get_menu_items($menu_id, 'id="menu-main" class="nav navbar-nav"', 'class="nav-item"');
    }
    
    private function get_submenu($menu_id,$where_add) {
        $query = "SELECT * FROM menu_item WHERE {$where_add} menu_id='{$menu_id}' AND active=1 ORDER BY position ASC";
        $result = App::$db->query($query);
        $output = '';
        $output .= '<ul class="sub-menu collapse" id="submenu'.$menu_id.'">' . PHP_EOL;
        if ($result->num_rows) {
            while ($row = $result->fetch_array()) {
                list($href,$target_inc) = $this->get_href($row);
                $output .= '<li><a href="'.$href.'" '.$target_inc.'>' .$row['title']. '</a></li>' . PHP_EOL;
            }
        }
        $output .= '</ul>' . PHP_EOL;
        return $output;
    }
    
    private function get_menu_items($menu_id, string $attr_ul = '', string $attr_li = '') : string
    {
        if (!$menu_id){
            return '';
        }
        if(strlen(App::$user->flags)){
            $where_add="'" . App::$user->flags . "' LIKE concat('%',flag,'%') AND ";
        } else {
            $where_add = "flag='' AND";
        }
        $query = "SELECT * FROM menu_item WHERE {$where_add} menu_id='{$menu_id}' AND active=1 ORDER BY position ASC";
        $result = App::$db->query($query);
        $output = '';
        if ($result->num_rows) {
            $output.="<ul {$attr_ul}>\n";
            while ($row = $result->fetch_array()) {
                list($href,$target_inc) = $this->get_href($row);
                $fa_inc = '';
                if(strlen($row['css_class'])>0 && $row['css_class']<>'default') {
                    $fa_inc = '<i class="fa fa-' . $row['css_class'] . '"></i> ';
                }
                if($row['submenu_id']>0) {
                    $output.='<li class="have-submenu collapsed active" data-toggle="collapse" data-target="#submenu'.$row['submenu_id'].'">' . PHP_EOL;
                    $output.='<a  href="'.$href.'">'.$fa_inc.$row['title']. '<span class="arrow"></span></a> ' . PHP_EOL;
                    $output.=$this->get_submenu($row['submenu_id'],$where_add);    
                    $output.='</li>' . PHP_EOL;
                } else {                    
                    $output.="<li {$attr_li}><a class=\"nav-link\" href=\"{$href}\"{$target_inc} title=\"{$row['title']}\">{$fa_inc}{$row['title']}</a></li>\n";
                }
            }
            $output.="</ul>\n";
        }
        return $output;
    }
    
        /**
     * Return menu HREF
     *
     * @param array $row Current row
     *
     * @return array HREF and TARGET
     */
    private function get_href(array $row) : array
    {
        $href = App::$routing->getUrl($row['target_type'], $row['target_id'], $row);
        if (preg_match('/^http.?:\/\/.+$/', $href)) {
            $target_inc = ' target="_blank"';
        } else {
            $href = App::$SUBDIR . $href;
            $target_inc = '';
        }
        return [$href,$target_inc];        
    }

}
