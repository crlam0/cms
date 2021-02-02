<?php

namespace modules\gallery\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\gallery\models\GalleryList;
use modules\gallery\models\GalleryImage;

class ImagesEditController extends BaseController
{
    private $image_path;
    private $image_max_width;
    private $image_max_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Галерея';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->image_path = App::$settings['modules']['gallery']['upload_path'] ?? 'upload/gallery/';
        $this->image_max_width = App::$settings['modules']['gallery']['image_max_width'] ?? 1024;
        $this->image_max_height = App::$settings['modules']['gallery']['image_max_height'] ?? 768;
        $this->user_flag = 'admin';
    }

    public function actionIndex(int $gallery_id): string
    {
        $model = new GalleryImage;
        $result = $model->findAll(['gallery_id' => $gallery_id], 'date_add DESC');

        [$list_title, $default_image_id] = App::$db->getRow("select title,default_image_id from gallery_list where id=?", ['id' => $gallery_id]);
        $this->title = 'Изображения в разделе ' . $list_title;
        $this->breadcrumbs[] = ['title' => $this->title];

        return $this->render('gallery_item_table.html.twig', ['default_image_id' => $default_image_id], $result);
    }

    public function actionDefaultImage(int $gallery_id, int $image_id): string
    {
        $model = new GalleryList($gallery_id);
        $model->default_image_id = $image_id;
        $model->save();
        echo 'OK';
        exit;
    }

    public function actionCreate(int $gallery_id): string
    {
        $model = new GalleryImage();
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->gallery_id = $gallery_id;
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::addFlash('success', 'Изображение успешно добавлено.');
            }
            $this->redirect('index');
        }
        return $this->render('gallery_item_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $gallery_id, int $id): string
    {
        $model = new GalleryImage($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::addFlash('success', 'Изображение успешно изменено.');
            }
            $this->redirect('index');
        }
        return $this->render('gallery_item_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $gallery_id, int $id): string
    {
        $model = new GalleryImage($id);
        $this->deleteImageFile($model);
        $model->delete();
        $this->redirect('index');
    }

    public function showImage($file_name): string
    {
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="200" />';
        } else {
            return 'Отсутствует';
        }
    }

    /**
     * @return array[]
     *
     * @psalm-return array<0|positive-int, array>
     */
    private function reArrayFiles(&$file_post): array
    {

        $file_ary = [];
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }


    public function actionAddMultiple(int $gallery_id): string
    {
        if ($_FILES['files']) {
            $file_array = $this->reArrayFiles($_FILES['files']);
            foreach ($file_array as $file) {
                $model = new GalleryImage();
                $model->gallery_id = $gallery_id;
                $model->date_add = 'now()';
                $model->date_change = 'now()';
                $model->uid = App::$user->id;
                $f_info = pathinfo($file['name']);
                $model->title = $f_info['basename'];
                $this->saveImage($model, $file);
                $model->save(false);
            }
        }
        App::addFlash('success', 'Изображения успешно добавлены.');
        $this->redirect('index');
    }

    private function saveImage(GalleryImage $model, $file)
    {
        if (!$file['size']) {
            return true;
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            App::addFlash('danger', 'Неверный тип файла !');
            return false;
        }
        $this->deleteImageFile($model);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($model->title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, $this->image_max_width, $this->image_max_height)) {
            $model->file_name = $file_name;
            $model->file_type = $file['type'];
            return true;
        } else {
            App::addFlash('danger', 'Ошибка копирования файла !');
            return false;
        }
    }

    public function actionDeleteImageFile(int $gallery_id, $post_id): void
    {
        $model = new GalleryImage($post_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' =>$post_id]);
    }

    /**
     * @return false|null
     */
    private function deleteImageFile(GalleryImage $model)
    {
        if (is_file(App::$DIR . $this->image_path . $model->file_name)) {
            if (!unlink(App::$DIR . $this->image_path . $model->file_name)) {
                App::addFlash('danger', 'Ошибка удаления файла');
                return false;
            }
        }
        $model->file_name = '';
        $model->file_type = '';
    }
}
