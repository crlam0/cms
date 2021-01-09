<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;

use admin\models\Template;

class TemplatesEditController extends BaseController
{    
    
    private $TABLE;
    
    public function __construct() {
        parent::__construct();
        $this->TABLE = 'templates';     
        $this->title = 'Шаблоны';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->user_flag = 'global';
    }

    public function actionIndex(): string
    {
        $model = new Template;
        $result = $model->findAll([], 'name ASC');        
        return $this->render('templates_table.html.twig', [], $result);    
    }
    
    public function actionCreate(): string 
    {
        $model = new Template();
        if($model->load(App::$input['form']) && $model->save()) {
            $this->redirect('update', ['id' =>$model->id]);
        }
        
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        
        return $this->render('templates_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('create'),
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string 
    {
        $model = new Template($id);
        if(!App::$input['revert'] && $model->load(App::$input['form']) && $model->save()) {
            if(!$this->twigTplSave(App::$input['form'])) {
                echo App::$message->get('error', [], 'Ошибка сохранения файла шаблона.');
            } elseif(App::$input['update_and_exit']) {
                $this->redirect('index');
            } else {
                $this->redirect('update', ['id' => $id]);
            }
        }

        if($model->template_type==='twig' && strlen($model->file_name)) {
            $model->content = $this->twigTplLoad($model->file_name);
        }         

        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        
        return $this->render('templates_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);        
    }
    
    public function actionDelete(int $id): string 
    {
        $model = new Template($id);
        $model->delete();
        $this->redirect('index');
    }
    
    private function twigTplLoad($filename){
        if(!strstr($filename,'.html.twig')) {
            $filename.='.html.twig';
        }
        $filename = App::$DIR . 'templates/' . $filename;
        if(file_exists($filename)) {
            return file_get_contents($filename);
        } else {
            return '';
        }    
    }

    private function twigTplSave($form){
        if($form['template_type']!='twig' || !strlen($form['content']) || !strlen($form['file_name'])) {
            return true;
        }
        if(!strstr($form['file_name'],'.html.twig')) {
            $form['file_name'].='.html.twig';
        }
        $filename = App::$DIR . 'templates/' . $form['file_name'];
        return file_put_contents($filename, stripcslashes($form['content'])) && clear_cache_dir('twig');
    }

}

