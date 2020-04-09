<?php


namespace modules\misc;
use Classes\BaseController;
use Classes\App;

/**
 * Description of NewsController
 *
 * @author BooT
 */
class NewsController extends BaseController {
    private $TABLE = 'news';
    
    public function actionIndex()
    {
        $this->title = 'Новости';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "select * from {$this->TABLE} order by date desc";
        $result = App::$db->query($query);
        $this->tags['content-cut'] = 'cut';
        return App::$template->parse('news_table', ['this' => $this], $result);        
    }
    
    public function actionItemView($alias)
    {
        $item_id = get_id_by_alias($this->TABLE, $alias, true);
        $this->title = 'Новости';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "select * from {$this->TABLE} where id='{$item_id}' order by date desc";
        $result = App::$db->query($query);
        return App::$template->parse($this->TABLE.'news_table', ['this' => $this], $result);        
    }    
    
    public function getFullContent($row) {
        if($this->tags['content-cut']==='cut') {
            $this->tags['content'] = strip_tags($row['content']);
            $this->tags['content'] = cut_string($this->tags['content'], 250);
        } else {
            $this->tags['content'] = $this->tags['content'];
        }
        return replace_base_href($this->tags['content']);
    }
    
}
