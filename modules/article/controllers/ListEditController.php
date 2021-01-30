<?php

namespace modules\article\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\article\models\ArticleList;

class ListEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct() {
        parent::__construct();
        $this->title = 'Разделы статей';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->image_path = App::$settings['modules']['article']['list_upload_path'] ?? 'upload/article/';
        $this->image_width = App::$settings['modules']['article']['list_image_width'] ?? 200;
        $this->image_height = App::$settings['modules']['article']['list_image_height'] ?? 200;
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new ArticleList;
        $result = $model->findAll([], 'date_add DESC');        
        return $this->render('list_table.html.twig', [], $result);        
    }

    public function actionCreate(): string 
    {
        $model = new ArticleList();
        if($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias){
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Раздел успешно добавлен');
            $this->redirect('index');
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('list_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string 
    {
        $model = new ArticleList($id); 
        if($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias){
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Раздел успешно обновлён');
            $this->redirect('index');
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('list_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }
    
    public function actionDelete(int $id): string 
    {
        if(App::$db->getRow("select id from article_item where list_id=?", ['id' => $id])) {
            App::setFlash('danger', 'Этот раздел не пустой !');
            $this->redirect('index');
        }
        $model = new ArticleList($id);
        $this->deleteImageFile($model);
        $model->delete();
        $this->redirect('index');
    }    

    public function showImage($file_name): string{
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="' . $this->image_width . '" />';
        } else {
            return 'Отсутствует';
        }        
    }
    
    /**
     * @return false|null
     */
    private function saveImage(ArticleList $model, $file) 
    {
        if ($file['size'] < 100) {
            return false;
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            App::setFlash('danger', 'Неверный тип файла !');
        }         
        $this->deleteImageFile($model);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($model->title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            $model->image_name = $file_name;
            $model->image_type = $file['type'];
        } else {
            App::setFlash('danger', 'Ошибка копирования файла !');
        }
    }
    
    public function actionDeleteImageFile($post_id): void 
    {
        $model = new ArticleList($post_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' => $post_id]);
    }
    
    /**
     * @return false|null
     */
    private function deleteImageFile(ArticleList $model) 
    {
        if (is_file(App::$DIR . $this->image_path . $model->image_name)) {
            if (!unlink(App::$DIR . $this->image_path . $model->image_name)) {
                App::setFlash('danger', 'Ошибка удаления файла');
                return false;
            }
        }
        $model->image_name = '';
        $model->image_type = '';
    }
    
}

