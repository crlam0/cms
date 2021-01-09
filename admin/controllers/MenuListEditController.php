<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use admin\models\MenuList;

class MenuListEditController extends BaseController
{
    private $image_path;
    private $image_width;

    public function __construct() {
        parent::__construct();
        $this->title = 'Разделы меню';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new MenuList;
        $result = $model->findAll([], 'title ASC');        
        return $this->render('menu_list_table.html.twig', [], $result);        
    }

    public function actionCreate(): string 
    {
        $model = new MenuList();
        if($model->load(App::$input['form']) && $model->validate()) {
            $model->root = isset(App::$input['form']['root']) ? 1 : 0;
            $model->top_menu = isset(App::$input['form']['top_menu']) ? 1 : 0;
            $model->bottom_menu = isset(App::$input['form']['bottom_menu']) ? 1 : 0;
            $model->save(false);
            App::setFlash('success', 'Раздел успешно добавлен');
            $this->redirect('index');
        }
        return $this->render('menu_list_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string 
    {
        $model = new MenuList($id);        
        if($model->load(App::$input['form']) && $model->validate()) {
            $model->root = isset(App::$input['form']['root']) ? 1 : 0;
            $model->top_menu = isset(App::$input['form']['top_menu']) ? 1 : 0;
            $model->bottom_menu = isset(App::$input['form']['bottom_menu']) ? 1 : 0;
            $model->save(false);
            App::setFlash('success', 'Раздел успешно обновлён');
            $this->redirect('index');
        }
        
        return $this->render('menu_list_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }
    
    public function actionDelete(int $id): string 
    {
        $model = new MenuList($id);
        $model->delete();
        $this->redirect('index');
    }    

    
}

