<?php

namespace modules\blog\controllers;

use classes\App;
use classes\BaseController;

use modules\blog\models\Comment;

class CommentsEditController extends BaseController
{
    private $image_path;
    private $image_width;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Коментарии';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new Comment;
        $result = $model->findAll([], 'date_add DESC');
        return $this->render('comment_table.html.twig', [], $result);
    }

    public function actionActive(int $id, string $active): string
    {
        $model = new Comment($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionUpdate(int $id): string
    {
        $model = new Comment($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            if ($model->save(false)) {
                App::addFlash('success', 'Пост успешно изменён.');
            }
            $this->redirect('index');
        }
        return $this->render('comment_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $id): string
    {
        $model = new Comment($id);
        $model->delete();
        $this->redirect('index');
    }


}
