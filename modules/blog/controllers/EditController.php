<?php

namespace modules\blog\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\blog\models\BlogPost;

class EditController extends BaseController
{
    private $image_path;
    private $image_width;

    public function __construct() {
        parent::__construct();
        $this->title = 'Блог';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->image_path = App::$settings['blog_img_path'];
        $this->image_width = App::$settings['blog_img_max_width'];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new BlogPost;
        $result = $model->findAll([], 'date_add DESC');        
        return $this->render('blog_post_table.html.twig', [], $result);        
    }

    public function actionActive(int $id, string $active): string 
    {
        $model = new BlogPost($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }    
    
    public function actionCreate(): string 
    {
        $model = new BlogPost();
        if($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias){
                $model->seo_alias = encodestring($model->title);
            }
            $model->content = replace_base_href($model->content, true);
            $model->active = 'Y';
            $model->date_add = 'now()';
            $model->uid = App::$user->id;
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Пост успешно добавлен.');
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->content = replace_base_href($model->content, false);
        return $this->render('blog_post_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
            'target_types' => $this->target_types,
        ]);
    }

    public function actionUpdate(int $id): string 
    {
        $model = new BlogPost($id); 
        if($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias){
                $model->seo_alias = encodestring($model->title);
            }
            $model->content = replace_base_href($model->content, true);
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Пост успешно изменён.');
            $this->redirect('update', ['id' =>$model->id]);
        } 
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->content = replace_base_href($model->content, false);
        return $this->render('blog_post_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
            'target_types' => $this->target_types,
        ]);
    }
    
    public function actionDelete(int $id): string 
    {
        $model = new BlogPost($id);
        $this->deleteImageFile($model);
        $model->delete();
        $this->redirect('index');
    }    

    public function showImage($file_name): string{
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="200" />';
        } else {
            return 'Отсутствует';
        }        
    }
    
    private function saveImage(BlogPost $model, $file): string 
    {        
        $content = '';        
        if ($file['size'] < 100) {
            return '';
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            return App::$message->get('error', [], 'Неверный тип файла !');
        }         
        $this->deleteImageFile($model);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($model->title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            $model->image_name = $file_name;
            $model->image_type = $file['type'];
            $content .= App::$message->get('', [], 'Изображение успешно добавлено.');
        } else {
            $content .= App::$message->get('error', [], 'Ошибка копирования файла !');
        }            
        return $content;
    }
    
    public function actionDeleteImageFile($post_id): void 
    {
        $model = new BlogPost($post_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' =>$post_id]);
    }
    
    /**
     * @return null|string
     */
    private function deleteImageFile(BlogPost $model) 
    {
        if (is_file(App::$DIR . $this->image_path . $model->image_name)) {
            if (!unlink(App::$DIR . $this->image_path . $model->image_name)) {
                return App::$message->get('error', [], 'Ошибка удаления файла');
            }
        }
        $model->image_name = '';
        $model->image_type = '';
    }
    
    public $target_types = [
        [
            'type' => 'link',
            'name' => 'Ссылка'
        ],
        [
            'type' => 'article_list',
            'name' => 'Раздел статей'
        ],
        [
            'type' => 'article',
            'name' => 'Статья'
        ],
        [
            'type' => 'media_list',
            'name' => 'Раздел файлов'
        ],
        [
            'type' => 'cat_part',
            'name' => 'Раздел каталога'
        ],
        [
            'type' => 'gallery_list',
            'name' => 'Раздел галереи'
        ],
        
    ];
    
    public function actionGetTargetSelect($post_id, $target_type): void 
    {
        $model = new BlogPost($post_id);        
        $target_id = $model->target_id;
        $href = $model->href;
        
        function get_option($name, $sql, $target_id): string {
            $result = my_query($sql);
            $output = '<td>' . $name . ':</td><td><select class="form-control" name="form[target_id]">';
            while ($row = $result->fetch_array()) {
                $output.="<option value={$row['id']}" . ($row['id'] == $target_id ? ' selected' : '') . ">{$row['title']}</option>";
            }
            $output.="</select></td>";
            return $output;
        }
        $output = '';

        switch ($target_type) {
            case 'link':
                $output = '<td>Прямая ссылка:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name="form[href]" value=" ' . $href . '"></td>';
                break;
            case 'article':
                $output = get_option('Статья', 'select * from article_item order by title', $target_id);
                break;
            case 'article_list':
                $output = get_option('Раздел статей', 'select * from article_list order by title', $target_id);
                break;
            case 'media_list':
                $output = get_option('Раздел файлов', 'select * from media_list order by title', $target_id);
                break;
            case 'cat_part':
                $output = get_option('Раздел каталога', 'select * from cat_part where prev_id=0 order by title', $target_id);
                break;
            case 'gallery_list':
                $output = get_option('Раздел галереи', 'select * from gallery_list order by title', $target_id);
                break;
        }
        echo $output;
        exit;
        
    }
    
    public function actionGetTagsPopup($post_id): void 
    {
        $result = App::$db->findAll('blog_posts_tags', ['post_id'=>$post_id]);
        $post_tags = [];
        while($row = $result->fetch_array()){
            $post_tags[] = $row['tag_id'];
        }        
        $result = App::$db->findAll('blog_tags', [], 'name ASC');
        $content = App::$template->parse('blog_post_tags.html.twig', ['post_id' => $post_id, 'post_tags' => $post_tags], $result);
        $json['content'] = $content;
        $json['result'] = 'OK';
        echo json_encode($json);
        exit;
    }
    
    public function actionAddNewTag($new_tag_name, $post_id): void 
    {
        App::$db->insertTable('blog_tags', ['name' => $new_tag_name, 'seo_alias' => encodestring($new_tag_name)]);
        $tag_id = App::$db->insert_id();
        if($tag_id) {
            App::$db->insertTable('blog_posts_tags', ['post_id'=>$post_id, 'tag_id' => $tag_id]);
        } else {
            echo App::$db->error();
        }
        echo 'OK';
        exit;
    }
    
    public function actionTagChange($post_id, $tag_id, $value): void 
    {
        if(strlen($value)>0) {
            App::$db->insertTable('blog_posts_tags', ['post_id'=>$post_id, 'tag_id' => $tag_id]);
        } else {
            App::$db->deleteFromTable('blog_posts_tags', ['post_id'=>$post_id, 'tag_id' => $tag_id]);
        }
        echo 'OK';
        exit;
    }
    
    public function actionTagDelete($tag_id): void 
    {
        App::$db->deleteFromTable('blog_posts_tags', ['tag_id' => $tag_id]);
        App::$db->deleteFromTable('blog_tags', ['id' => $tag_id]);
        echo 'OK';
        exit;
    }
    
}

