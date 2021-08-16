<?php

namespace modules\media\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\media\models\MediaList;

class ListEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Разделы файлов';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->image_path = App::$settings['modules']['media']['list_upload_path'] ?? 'upload/media/';
        $this->image_width = App::$settings['modules']['media']['list_image_width'] ?? 200;
        $this->image_height = App::$settings['modules']['media']['list_image_height'] ?? 200;
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new MediaList;
        $result = $model->findAll([], 'date_add DESC');
        return $this->render('media_list_table.html.twig', [], $result);
    }

    public function actionActive(int $id, string $active): string
    {
        $model = new MediaList($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionCreate(): string
    {
        $model = new MediaList();
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            if (!$model->image_name) {
                $model->image_name = '';
                $model->image_type = '';
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            $model->active = 'Y';
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::addFlash('success', 'Раздел успешно добавлен');
            }
            $this->redirect('index');
        }
        App::addAsset('js', 'vendor/ckeditor/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('media_list_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string
    {
        $model = new MediaList($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            if (!$model->image_name) {
                $model->image_name = '';
                $model->image_type = '';
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::addFlash('success', 'Раздел успешно обновлён');
            }
            $this->redirect('index');
        }
        App::addAsset('js', 'vendor/ckeditor/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('media_list_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $id): string
    {
        if (App::$db->getRow("select id from media_files where list_id=?", ['id' => $id])) {
            App::addFlash('danger', 'Этот раздел не пустой !');
            $this->redirect('index');
        }
        $model = new MediaList($id);
        $this->deleteImageFile($model);
        $model->delete();
        $this->redirect('index');
    }

    public function showImage($file_name): string
    {
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="' . $this->image_width . '" />';
        } else {
            return 'Отсутствует';
        }
    }

    /**
     * @return bool
     */
    private function saveImage(MediaList $model, $file): bool
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
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            $model->image_name = $file_name;
            $model->image_type = $file['type'];
            return true;
        } else {
            App::addFlash('danger', 'Ошибка копирования файла !');
            return false;
        }
    }

    public function actionDeleteImageFile($post_id): void
    {
        $model = new MediaList($post_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' => $post_id]);
    }

    /**
     * @return false|null
     */
    private function deleteImageFile(MediaList $model)
    {
        if (is_file(App::$DIR . $this->image_path . $model->image_name)) {
            if (!unlink(App::$DIR . $this->image_path . $model->image_name)) {
                App::addFlash('danger', 'Ошибка удаления файла');
                return false;
            }
        }
        $model->image_name = '';
        $model->image_type = '';
    }
}
