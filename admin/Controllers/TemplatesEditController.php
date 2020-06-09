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
    }

    public function actionIndex(): string
    {
        $model = new Template;
        $result = $model->findAll([], 'name ASC');        
        return App::$template->parse('templates_table.html.twig', ['this' => $this], $result);    
    }
    
    public function actionCreate(): string 
    {
        $model = new Template();
        if($model->load(App::$input['form']) && $model->save()) {
            $this->redirect('update?id=' . $model->id);
        } else {
            echo nl2br($model->getErrorsAsString());
        }
        
        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/edit_area/edit_area_full.js"></script>' . "\n";
        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/js/editor_html.js"></script>' . "\n";
        
        return App::$template->parse('templates_form.html.twig', [
            'this' => $this,
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',            
        ]);
    }

    public function actionUpdate(): string 
    {
        $model = new Template(App::$input['id']);
        if(!App::$input['revert'] && $model->load(App::$input['form']) && $model->save()) {
            if(!$this->twigTplSave(App::$input['form'])) {
                echo App::$message->get('error', [], 'Ошибка сохранения файла шаблона.');
            }
        } else {
            echo nl2br($model->getErrorsAsString());
        }
        
        if(App::$input['update_and_exit']) {
            return $this->actionIndex();
        }        

        if($model->template_type==='twig' && strlen($model->file_name)) {
            $model->content = $this->twigTplLoad($model->file_name);
        }         

        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/edit_area/edit_area_full.js"></script>' . "\n";
        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/js/editor_html.js"></script>' . "\n";
        
        return App::$template->parse('templates_form.html.twig', [
            'this' => $this,
            'model' => $model,
            'action' => 'update',
            'form_title' => 'Изменение',            
        ]);        
    }
    
    public function actionDelete(): string 
    {
        $model = new Template(App::$input['id']);
        $model->delete();
        redirect($this->base_url);
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

