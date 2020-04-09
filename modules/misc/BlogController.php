<?php

namespace modules\misc;

use Classes\BaseController;
use Classes\App;
use Classes\Pagination;
use Classes\Comments;

/**
 * Description of Controller
 *
 * @author BooT
 */
class BlogController extends BaseController 
{
    private $MSG_PER_PAGE = 20;
    private $TABLE = 'blog_posts';
    private $comments;
    
    public function __construct() 
    {
        if(isset(App::$settings['blog_msg_per_page'])) {
            $this->MSG_PER_PAGE = App::$settings['blog_msg_per_page'];
        }
    }
    
    public function getPostContent (array $row): string 
    {
        $content = replace_base_href($row['content'], false);
        $content = preg_replace('/height: \d+px;/', 'max-width: 100%;', $content);

        if(strlen($row['target_type'])){
            $href=(strlen($row['target_type'] == 'link') ? $row['href'] : App::$SUBDIR . get_menu_href([], $row) );
            $content.="<br><a href=\"{$href}\">Перейти >></a>";
        }
        return $content;
    }

    public function getPostCommentsCount (array $row): string 
    {
        return $this->comments->show_count($row['id']);
    }

    public function actionIndex(int $page = 1): string 
    {
        $this->title = 'Блог';
        $this->breadcrumbs[] = ['title'=>$this->title];
        
        $this->comments = new Comments ('blog', null);

        $query = "SELECT count(id) from {$this->TABLE} where active='Y'";
        list($total) = App::$db->select_row($query);

        $pager = new Pagination($total, $page, $this->MSG_PER_PAGE);
        $tags['pager'] = $pager;        

        $query = "SELECT {$this->TABLE}.*,users.fullname as author,date_format(date_add,'%Y-%m-%dT%H:%i+06:00') as timestamp
            from {$this->TABLE} left join users on (users.id=uid)
            where {$this->TABLE}.active='Y'
            group by {$this->TABLE}.id  order by {$this->TABLE}.id desc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);

        if (!$result->num_rows) {
             $content = App::$message->get('list_empty', [], '');
        } else {    
            $tags['this'] = $this;
            $content = App::$template->parse('blog_posts', $tags, $result);            
        }
        return $content;
    }

    public function actionPostView(string $alias):string 
    {        
        $post_id = get_id_by_alias($this->TABLE, $alias, true);
        $query = "select {$this->TABLE}.*,users.fullname as author from {$this->TABLE} left join users on (users.id=uid) where {$this->TABLE}.id='{$post_id}'";
        $result = App::$db->query($query);
        $row = $result->fetch_array();
        $result->data_seek(0);

        $this->title = $row['title'];
        $this->breadcrumbs[] = ['title' => 'Блог', 'url'=>'blog/'];        
        $this->breadcrumbs[] = ['title' => $this->title];        
        
        $this->comments = new Comments ('blog', $post_id);
        
        $tags['this'] = $this;
        $tags['post_view'] = true;
        $content .= App::$template->parse('blog_posts', $tags, $result);

        $this->comments->get_form_data(App::$input);
        $content .= $this->comments->show_list();
        $tags['action'] = App::$SUBDIR.get_post_href(null,$row) . '#comments';
        $content .= $this->comments->show_form($tags);        
        return $content;
    }
    
}
