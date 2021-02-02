<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;

use admin\models\Message;

class MessagesEditController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->title = 'Сообщения';
        $this->breadcrumbs[] = ['title' => $this->title];
    }

    public function actionIndex(): string
    {
        $model = new Message;
        $result = $model->findAll([], 'name ASC');
        return $this->render('messages_table.html.twig', [], $result);
    }

    public function actionCreate(): string
    {
        $model = new Message();
        if ($model->load(App::$input['form']) && $model->save()) {
            App::addFlash('success', 'Сообщение добавлено');
            $this->redirect('index');
        }
        return $this->render('messages_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string
    {
        $model = new Message($id);
        if ($model->load(App::$input['form']) && $model->save()) {
            App::addFlash('success', 'Сообщение сохранено');
            $this->redirect('index');
        }
        return $this->render('messages_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $id): string
    {
        $model = new Message($id);
        $model->delete();
        $this->redirect('index');
    }
}
