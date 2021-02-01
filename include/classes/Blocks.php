<?php

namespace classes;

use classes\App;

/**
 * Implements work with simple blocks
 *
 */
class Blocks
{

    /**
     * Return menu HREF
     *
     * @param integer $row Current row
     *
     * @return array HREF and TARGET
     */
    protected function get_href(array $row) : array
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

    /**
     * Return menu content
     *
     * @param integer $menu_id Menu ID
     * @param string $attr_ul UL CSS attributes
     * @param string $attr_li LI CSS attributes
     *
     * @return string Menu content
     */
    protected function get_menu_items($menu_id, string $attr_ul = '', string $attr_li = '') : string
    {
        if (!$menu_id) {
            return '';
        }
        if (strlen(App::$user->flags)) {
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
                $output.="<li {$attr_li}><a class=\"nav-link\" href=\"{$href}\"{$target_inc} title=\"{$row['title']}\">{$row['title']}</a>";
                $output.=$this->get_menu_items($row['submenu_id']);
                $output.="</li>\n";
            }
            $output.="</ul>\n";
        }
        return $output;
    }

    protected function menu_main() : string
    {
        list($menu_id) = App::$db->getRow("SELECT id FROM menu_list WHERE root=1");
        return $this->get_menu_items($menu_id, 'id="menu-main" class="nav navbar-nav"', 'class="nav-item"');
    }

    protected function menu_top() : string
    {
        list($menu_id) = App::$db->getRow("SELECT id FROM menu_list WHERE top_menu=1");
        return $this->get_menu_items($menu_id, 'id="menu-top" class="navbar-nav navbar-left"', 'class="nav-item"');
    }

    protected function menu_bottom() : string
    {
        list($menu_id) = App::$db->getRow("SELECT id FROM menu_list WHERE bottom_menu=1");
        return $this->get_menu_items($menu_id, 'id="menu-footer" class="navbar-nav navbar-expand ml-auto"', 'class="nav-item"');
    }

    protected function slider() : string
    {
        if (App::$routing->isIndexPage()) {
            $query = "SELECT * FROM slider_images WHERE length(file_name)>0 ORDER BY pos,title ASC";
            $result = App::$db->query($query);
            return App::$template->parse('slider_items', [], $result);
        } else {
            return '';
        }
    }

    protected function news() : string
    {

        $query = "select *,date_format(date,'%d.%m.%Y') as date from news order by id desc limit ". App::$settings['news_block_count'];
        $result = App::$db->query($query);
        if ($result->num_rows) {

            function get_news_short_content($tmp, $row)
            {
                return cut_string($row['content'], 100);
            }

            return App::$template->parse('block_news', [], $result);
        }
        return null;
    }

    protected function last_posts() : string
    {
        $TABLE = 'blog_posts';
        $query = "SELECT {$TABLE}.*,date_format(date_add,'%d.%m.%Y') as date from {$TABLE} where active='Y' order by {$TABLE}.id desc limit". App::$settings['news_block_count'];
        $result = App::$db->query($query);
        if ($result->num_rows) {
            function get_news_short_content($tmp, $row)
            {
                return cut_string($row['content'], 100);
            }
            return App::$template->parse('block_last_posts', [], $result);
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
    public function rawContent(string $block_name) : string
    {
        App::debug('Parse block ' . $block_name);
        switch ($block_name) {
            case 'partners':
                $query = "SELECT * FROM partners WHERE active='Y' order by pos asc";
                $result = App::$db->query($query);
                return App::$template->parse('block_partners', [], $result);

            case 'banners':
                if (file_exists(App::$DIR . 'local/banners.php')) {
                    ob_start();
                    include_once(App::$DIR . 'local/banners.php');
                    $content = ob_get_clean();
                    return $content;
                } else {
                    return '';
                }

            case 'calendar':
                ob_start();
                show_month(date('n'), 0);
                $content = ob_get_clean();
                return $content;

            case 'menu_admin':
                ob_start();
                include_once(App::$DIR . 'admin/nav.php');
                $content = ob_get_clean();
                return $content;

            case 'debug':
                if (App::$settings['debug']) {
                    ob_start();
                    print_array(App::$DEBUG_ARRAY);
                    echo 'Total time: <b>' . sprintf('%.4F', microtime(true) - App::$DEBUG_ARRAY[0]) . ' s.</b> ';
                    echo 'Memory: <b>' . convert_bytes(memory_get_usage(true)) . '</b><br /><br />';
                    print_array(App::$db->query_log_array);
                    $content = ob_get_clean();
                    return $content;
                }
                return '';

            default:
                if (strstr($block_name, '/')) {
                    $class_name = str_replace('/', '\\', $block_name);
                    if (class_exists($class_name)) {
                        $object = new $class_name;
                        return $object->run();
                    }
                    return 'Block class ' . $class_name . ' not found';
                }

                if (\is_callable([$this,$block_name])) {
                    return $this->$block_name();
                }
                $tags['title'] = $block_name;
                return App::$message->get('block_not_found', $tags);
        }
    }
    /**
     * Return block content
     *
     * @param string $block_name Block name
     *
     * @return string Block content
     */
    public function content(string $block_name, $allow_cache = false) : string
    {
        if ($allow_cache) {
            $cache_key = $block_name . App::$user->id;
            if (App::$cache->has($cache_key)) {
                return App::$cache->get($cache_key);
            }
        }
        $content = $this->rawContent($block_name);
        if ($allow_cache) {
            App::$cache->set($cache_key, $content);
        }
        return $content;
    }
}
