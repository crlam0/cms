<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;

use admin\Models\Setting;

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
        if($model->load(App::$input['form'])) {
            if($model->save()) {
                redirect($this->base_url);
            }
        }
        $tags = $model->asArray();
        $tags['this'] = $this;
        $tags['action'] = 'create';
        $tags['form_title'] = 'Добавление';
        
        return App::$template->parse('settings_form.html.twig', $tags);
    }

    public function actionUpdate(): string 
    {
        $model = new Setting(App::$input['id']);        
        if($model->load(App::$input['form'])) {
            if($model->save()) {
                redirect($this->base_url);
            } else {
                echo nl2br($model->getErrorsAsString());
            }
        }
        $tags = $model->asArray();
        $tags['this'] = $this;
        $tags['action'] = 'update';
        $tags['form_title'] = 'Изменение';
        return App::$template->parse('settings_form.html.twig', $tags);        
    }
    
    public function actionDelete(): string 
    {
        $model = new Setting(App::$input['id']);
        $model->delete();
        redirect($this->base_url);
    }    

}

