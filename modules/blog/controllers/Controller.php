<?php

namespace modules\blog\controllers;

use classes\BaseController;
use classes\App;
use classes\Pagination;
use classes\Comments;
use classes\Score;

/**
 * Description of Controller
 *
 * @author BooT
 */
class Controller extends BaseController 
{
    private $MSG_PER_PAGE = 20;
    private $TABLE = 'blog_posts';
    private $comments;
    
    public function __construct() 
    {
        if(isset(App::$settings['blog_msg_per_page'])) {
            $this->MSG_PER_PAGE = App::$settings['blog_msg_per_page'];
        }
        $this->title = App::$settings['modules']['blog']['header'] ?? 'Блог';
    }
    
    public function actionIndex(int $page = 1): string 
    {
        $this->breadcrumbs[] = ['title'=>$this->title];
        
        $query = "SELECT count(id) from {$this->TABLE} where active='Y'";
        list($total) = App::$db->getRow($query);

        $pager = new Pagination($total, $page, $this->MSG_PER_PAGE);
        $tags['pager'] = $pager;
        App::addAsset('js', 'modules/blog/blog.js');

        $query = "SELECT {$this->TABLE}.*,users.fullname as author,users.avatar,
            (SELECT COUNT(id) FROM blog_posts_tags WHERE post_id = blog_posts.id) as tags_count,
            (SELECT COUNT(id) FROM comments WHERE active='Y' and comments.target_type='blog' and target_id = blog_posts.id) as comments_count,
            (SELECT COUNT(id) FROM score_log WHERE target_type='blog' and target_id = blog_posts.id) as score_count
            from {$this->TABLE} 
            left join users on (users.id=uid)
            where {$this->TABLE}.active='Y'
            group by {$this->TABLE}.id  order by {$this->TABLE}.id desc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);

        if (!$result->num_rows) {
            $content = App::$message->get('list_empty', [], '');
        } else {
            $content =$this->render('blog_posts', $tags, $result);            
        }
        return $content;
    }

    public function actionPostView(string $alias):string 
    {        
        $post_id = get_id_by_alias($this->TABLE, $alias, true);
        $query = "SELECT {$this->TABLE}.*,users.fullname as author,users.avatar,
            (SELECT COUNT(id) FROM blog_posts_tags WHERE post_id = blog_posts.id) as tags_count,
            (SELECT COUNT(id) FROM comments WHERE active='Y' and comments.target_type='blog' and target_id = blog_posts.id) as comments_count,
            (SELECT COUNT(id) FROM score_log WHERE target_type='blog' and target_id = blog_posts.id) as score_count
            from {$this->TABLE} 
            left join users on (users.id=uid)
            where {$this->TABLE}.active='Y' and {$this->TABLE}.id=?";
        $result = App::$db->query($query,['id' => $post_id]);
        $row = $result->fetch_array();
        $result->data_seek(0);

        $this->breadcrumbs[] = ['title' => $this->title, 'url'=>'blog/'];        
        $this->breadcrumbs[] = ['title' => $row['title']];
        $this->title = $row['title'];
        
        $this->comments = new Comments ('blog', $post_id);
        
        $tags['post_view'] = true;
        App::addAsset('js', 'modules/blog/blog.js');
        
        $content = $this->render('blog_posts', $tags, $result);

        $this->comments->get_form_data(App::$input['form']);
        $content .= $this->comments->show_list();
        $tags['action'] = App::$SUBDIR . App::$routing->getUrl('blog_post', null, $row) . '#comments';
        $content .= $this->comments->show_form($tags);        
        return $content;
    }
    
    public function actionByTag(string $alias):string 
    {
        
        $tag_id = get_id_by_alias('blog_tags', $alias, true);
        [$tag_name] = App::$db->getRow('select name from blog_tags where id = ?', ['id' => $tag_id]);
        $this->breadcrumbs[] = ['title' => $this->title, 'url'=>'blog/'];
        $this->title = 'Публикации по метке &quot;' . $tag_name . '&quot;';
        $this->breadcrumbs[] = ['title' => $this->title];        
        
        $query = "SELECT {$this->TABLE}.*,users.fullname as author,users.avatar,
            (SELECT COUNT(id) FROM blog_posts_tags WHERE post_id = blog_posts.id) as tags_count,
            (SELECT COUNT(id) FROM comments WHERE active='Y' and comments.target_type='blog' and target_id = blog_posts.id) as comments_count,
            (SELECT COUNT(id) FROM score_log WHERE target_type='blog' and target_id = blog_posts.id) as score_count
            from blog_posts_tags
            left join blog_posts {$this->TABLE} on ({$this->TABLE}.id = blog_posts_tags.post_id)
            left join users on (users.id=uid)
            where {$this->TABLE}.active='Y' and blog_posts_tags.tag_id = '{$tag_id}'
            group by {$this->TABLE}.id  order by {$this->TABLE}.id desc";
        $result = App::$db->query($query);
        
        $tags['post_view'] = true;
        App::addAsset('js', 'modules/blog/blog.js');

        if (!$result->num_rows) {
             $content = App::$message->get('list_empty', [], '');
        } else {
            $content = $this->render('blog_posts', $tags, $result);            
        }
        return $content;
    }
    
    public function actionAddScore (int $post_id): array 
    {
        $this->score = new Score ('blog');
        $this->score->add($post_id, 1);
        return ['result' => 'OK', 'score' => $this->score->getCount($post_id)];
    }
    
    public function getPostContent (array $row): string 
    {
        $content = replace_base_href($row['content'], false);
        return preg_replace('/height: \d+px;/', 'max-width: 100%;', $content);
    }

    public function getTags (array $row): array 
    {
        $result = App::$db->query('SELECT `name`,`seo_alias` FROM blog_posts_tags left join blog_tags ON (blog_tags.id = blog_posts_tags.tag_id) WHERE post_id=? ORDER BY name ASC',['post_id' => $row['id']]);
        $tags = $result->fetch_all(MYSQLI_ASSOC);
        return $tags;
    }
    
    public function getPostCommentsCount (array $row): string 
    {
        return $this->comments->show_count($row['id']);
    }

    public function getScore (array $row): string 
    {
        return $this->score->getCount($row['id']);
    }

}
