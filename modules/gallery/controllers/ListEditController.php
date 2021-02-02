<?php

namespace modules\gallery\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\gallery\models\GalleryList;
use modules\gallery\models\GalleryImage;

class ListEditController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Галерея';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new GalleryList;
        $result = $model->findAll([], 'date_add DESC');
        return $this->render('gallery_list_table.html.twig', [], $result);
    }

    public function actionActive(int $id, string $active): string
    {
        $model = new GalleryList($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function showImage($image_id): string
    {
        $image = new GalleryImage($image_id);
        $image_path = App::$settings['modules']['gallery']['upload_path'] ?? 'upload/gallery/';
        if (is_file(App::$DIR . $image_path . $image->file_name)) {
            return '<img src="' . App::$SUBDIR . $image_path . $image->file_name . '" border="0" width="200" />';
        } else {
            return 'Отсутствует';
        }
    }

    public function actionCreate(): string
    {
        $model = new GalleryList();
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->active = 'Y';
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            $model->save(false);
            App::addFlash('success', 'Раздел успешно добавлен');
            $this->redirect('index');
        }
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('gallery_list_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string
    {
        $model = new GalleryList($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            $model->save(false);
            App::addFlash('success', 'Раздел успешно обновлён');
            // $this->redirect('index');
        }
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('gallery_list_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $id): string
    {
        if (App::$db->getRow("select id from gallery_images where gallery_id=?", ['id' => $id])) {
            App::addFlash('danger', 'Этот раздел не пустой !');
            $this->redirect('index');
        }
        $model = new GalleryList($id);
        $model->delete();
        $this->redirect('index');
    }
}
