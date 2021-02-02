<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;

use admin\models\Setting;

class SettingsEditController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->title = 'Настройки';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $model = new Setting;
        $result = $model->findAll([], 'name ASC');
        return $this->render('settings_table.html.twig', [], $result);
    }

    public function actionCreate(): string
    {
        $model = new Setting();
        if ($model->load(App::$input['form']) && $model->save()) {
            App::addFlash('success', 'Настройка добавлена');
            $this->redirect('index');
        }
        return $this->render('settings_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string
    {
        $model = new Setting($id);
        if ($model->load(App::$input['form']) && $model->save()) {
            App::addFlash('success', 'Настройка сохранена');
            $this->redirect('index');
        }
        return $this->render('settings_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $id): string
    {
        $model = new Setting($id);
        $model->delete();
        $this->redirect('index');
    }
}
