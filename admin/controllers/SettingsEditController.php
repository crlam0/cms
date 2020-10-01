<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;

use admin\models\Setting;

class SettingsEditController extends BaseController
{        
    private $TABLE;
    
    public function __construct() {
        parent::__construct();
        $this->TABLE = 'settings';        
        $this->title = 'Настройки';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $model = new Setting;
        $result = $model->findAll([], 'name ASC');        
        return App::$template->parse('settings_table.html.twig', ['this' => $this], $result);        
    }
    
    public function actionCreate(): string 
    {
        $model = new Setting();
        if($model->load(App::$input['form']) && $model->save()) {
            $this->redirect('index');
        }
        $errors = App::$message->getErrorsFromArray($model->getErrors());
        return $errors . App::$template->parse('settings_form.html.twig', [
            'this' => $this,
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',            
        ]);
    }

    public function actionUpdate(): string 
    {
        $model = new Setting(App::$input['id']);        
        if($model->load(App::$input['form']) && $model->save()) {
            $this->redirect('index');
        } 
        $errors = App::$message->getErrorsFromArray($model->getErrors());        
        return $errors . App::$template->parse('settings_form.html.twig', [
            'this' => $this,
            'model' => $model,
            'action' => 'update',
            'form_title' => 'Изменение',            
        ]);
    }
    
    public function actionDelete(): string 
    {
        $model = new Setting(App::$input['id']);
        $model->delete();
        $this->redirect('index');
    }    

}

