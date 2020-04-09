<?php


namespace modules\misc;
use Classes\BaseController;
use Classes\App;

/**
 * Description of NewsController
 *
 * @author BooT
 */
class OffersController extends BaseController 
{
    private $TABLE = 'offers';
    
    public function actionIndex(): string
    {
        $this->title = 'Новости';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "select * from {$this->TABLE} order by date desc";
        $result = App::$db->query($query);
        $this->tags['content-cut'] = 'cut';
        return App::$template->parse('offers_table', ['this' => $this], $result);        
    }
    
    public function actionItemView(string $alias): string
    {
        $item_id = get_id_by_alias($this->TABLE, $alias, true);
        $this->title = 'Новости';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "select * from {$this->TABLE} where id='{$item_id}' order by date desc";
        $result = App::$db->query($query);
        return App::$template->parse($this->TABLE.'offers_table', ['this' => $this], $result);        
    }    
    
    public function getFullContent(array $row): string 
    {
        if($this->tags['content-cut']==='cut') {
            $this->tags['content'] = strip_tags($row['content']);
            $this->tags['content'] = cut_string($this->tags['content'], 250);
        } else {
            $this->tags['content'] = $this->tags['content'];
        }
        return replace_base_href($this->tags['content']);
    }
    
}
