<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

class OffersEditController extends BaseController
{    
    
    private $TABLE;
    private $image_path;
    private $image_width;
    private $image_height;
    
    public function __construct() {
        parent::__construct();
        $this->TABLE = 'offers';
        $this->image_path = App::$settings['offers']['upload_path'];
        $this->image_width = App::$settings['offers']['image_width'];
        $this->image_height = App::$settings['offers']['image_height'];

        $this->title = 'Специальные предложения';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $query = "SELECT * from {$this->TABLE} order by date,title asc";
        $result = App::$db->query($query);
        
        return $this->render('news_table.html.twig', [], $result);        
    }
    
    public function actionCreate(): string 
    {
        global $_FILES;
        $content = '';
        if(is_array(App::$input['form'])) {
            App::$input['form']['date'] = App::$input['form']['date'] ?: 'now()';
            App::$input['form']['seo_alias'] = App::$input['form']['seo_alias'] ?: encodestring(App::$input['form']['title']);
            App::$db->insertTable($this->TABLE, App::$input['form']);
            $image_id = App::$db->insert_id();
            $file = $_FILES['image_file'];    
            $content .= $this->saveImage($file, $image_id, $image_id);
            redirect('index');
        }
        $tags = [
            'action' => 'create',
            'form_title' => 'Добавление',
            'id' => '',
            'date' => '',
            'title' => '',
            'content' => '',
            'seo_alias' => '',
            'author' => '',
            'url' => '',
            'file_name' => null,
            'file_type' => null,
        ];
        
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        
        $content .= $this->render('news_form.html.twig', $tags);
        return $content;
    }

    public function actionUpdate(int $id): string 
    {
        global $_FILES;
        $content = '';
        if(is_array(App::$input['form'])) {
            App::$input['form']['date'] = App::$input['form']['date'] ?: 'now()';
            App::$input['form']['seo_alias'] = App::$input['seo_alias'] ?: encodestring(App::$input['form']['title']);
            App::$db->updateTable($this->TABLE, App::$input['form'], ['id' => $id]);
            $file = $_FILES['image_file'];    
            $content .= $this->saveImage($file, $id, $id);
            redirect('index');
        }
        $tags = App::$db->getRow("select * from {$this->TABLE} where id=?", ['id' => $id]);
        $tags['action'] = $this->getUrl('update', ['id' => $id]);
        $tags['form_title'] = 'Изменение';
      
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');

        $content .= $this->render('news_form.html.twig', $tags);
        return $content;        
    }
    
    public function actionDelete(int $id): string 
    {
        $content = $this->deleteImageFile($id);
        $query = "delete from {$this->TABLE} where id=?";
        App::$db->query($query , ['id' => $id]);  
        $content .= $this->actionIndex();
        return $content;
    }

    public function showImage($file_name){
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="200" />';
        } else {
            return 'Отсутствует';
        }        
    }
    
    private function saveImage($file, $image_id, $title) 
    {        
        $content = '';
        
        if ($file['size'] < 100) {
            return '';
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            return App::$message->get('error', [], 'Неверный тип файла !');
        }         
        $this->deleteImageFile($image_id);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            $query = "update {$this->TABLE} set file_name=?, file_type=? where id=?";
            App::$db->query($query , ['file_name' => $file_name, 'file_type' => $file['type'], 'id' => $image_id]);
            $content .= App::$message->get('', [], 'Изображение успешно добавлено.');
        } else {
            $content .= App::$message->get('error', [], 'Ошибка копирования файла !');
        }            
        return $content;
    }
    
    public function actionDeleteImageFile($item_id) 
    {
        $this->deleteImageFile($item_id);
        $this->redirect('update', ['id' => $item_id]);
    }

    private function deleteImageFile($image_id) {
        list($image_old) = App::$db->getRow("select file_name from {$this->TABLE} where id=?", ['id' => $image_id]);
        if (is_file(App::$DIR . $this->image_path . $image_old)) {
            if (!unlink(App::$DIR . $this->image_path . $image_old)) {
                return  App::$message->get('error', [], 'Ошибка удаления файла');
            }
        }
        return '';
    }
    
}

