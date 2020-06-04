<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

class SliderEditController extends BaseController
{    
    
    private $TABLE;
    private $image_path;
    private $image_width;
    private $image_height;
    
    public function __construct() {
        parent::__construct();
        $this->TABLE = 'slider_images';
        $this->image_path = App::$settings['slider']['upload_path'];
        $this->image_width = App::$settings['slider']['image_width'];
        $this->image_height = App::$settings['slider']['$image_height'];
    }

    public function actionIndex(): string
    {
        $this->title = 'Картинки для слайдера';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $query = "SELECT * from {$this->TABLE} order by pos,title asc";
        $result = App::$db->query($query);
        
        return App::$template->parse('slider_images_table.html.twig', ['this' => $this], $result);        
    }
    
    public function actionCreate(): string 
    {
        global $_FILES;
        $content = '';
        if(is_array(App::$input['form'])) {
            $form = App::$input['form'];
            $query = "insert into {$this->TABLE} " . App::$db->insertFields($form);
            App::$db->query($query);
            $image_id = App::$db->insert_id();
            $file = $_FILES['image_file'];    
            $content .= $this->saveImage($file, $image_id, $form['title']);
        }
        $tags = [
            'this' => $this,
            'action' => 'create',
            'id' => '',
            'form_title' => 'Добавление',
            'pos' => '1',
            'title' => '',
            'descr' => '',
            'url' => '',
            'file_name' => null,
        ];
        $content .= App::$template->parse('slider_images_form.html.twig', $tags);
        return $content;
    }

    public function actionUpdate(): string 
    {
        global $_FILES;
        $content = '';
        if(is_array(App::$input['form'])) {
            $form = App::$input['form'];
            $query = "update {$this->TABLE} set " . App::$db->updateFields($form) . ' where id=?';
            App::$db->query($query, ['id' => App::$input['id']]);
            $image_id = App::$input['id'];
            $file = $_FILES['image_file'];    
            $content .= $this->saveImage($file, $image_id, $form['title']);
        }
        $tags = App::$db->getRow("select * from {$this->TABLE} where id=?", ['id' => App::$input['id']]);
        $tags['this'] = $this;
        $tags['action'] = 'update';
        $tags['form_title'] = 'Изменение';
      
        $content .= App::$template->parse('slider_images_form.html.twig', $tags);
        return $content;        
    }
    
    public function actionDelete(): string 
    {
        $content = $this->deleteImageFile(App::$input['id']);
        $query = "delete from {$this->TABLE} where id=?";
        App::$db->query($query , ['id' => App::$input['id']]);  
        $content .= $this->actionIndex();
        return $content;
    }

    public function showImage($file_name){
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="200" />';
        } else {
            return "Отсутствует";
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

