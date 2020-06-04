<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;

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
        $query = "SELECT * from {$this->TABLE} order by name asc";
        $result = App::$db->query($query);
        // throw new \InvalidArgumentException('test');
        
        return App::$template->parse('settings_table.html.twig', ['this' => $this], $result);        
    }
    
    public function actionCreate(): string 
    {
        if(is_array(App::$input['form'])) {
            App::$db->insertTable($this->TABLE, App::$input['form']);
            return $this->actionIndex();
        }
        $tags = [
            'this' => $this,
            'action' => 'create',
            'id' => '',
            'form_title' => 'Добавление',
            'name' => '',
            'value' => '',
            'comment' => '',
        ];
        
        return App::$template->parse('settings_form.html.twig', $tags);
    }

    public function actionUpdate(): string 
    {
        if(is_array(App::$input['form'])) {
            App::$db->updateTable($this->TABLE, App::$input['form'], ['id' => App::$input['id']]);
            return $this->actionIndex();
        }        
        $tags = App::$db->getRow("select * from {$this->TABLE} where id=?", ['id' => App::$input['id']]);
        $tags['this'] = $this;
        $tags['action'] = 'update';
        $tags['form_title'] = 'Изменение';
        return App::$template->parse('settings_form.html.twig', $tags);        
    }
    
    public function actionDelete(): string 
    {
        $query = "delete from {$this->TABLE} where id=?";
        App::$db->query($query , ['id' => App::$input['id']]);  
        return $this->actionIndex();
    }    

}

