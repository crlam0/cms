<?php

namespace modules\media\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\media\models\MediaFile;

class FileEditController extends BaseController
{
    private $file_path;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Файлы';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->file_path = App::$settings['modules']['media']['item_upload_path'] ?? 'upload/media/';
        $this->user_flag = 'admin';
    }

    public function actionIndex(int $list_id): string
    {
        $model = new MediaFile;
        $result = $model->findAll(['list_id' => $list_id], 'date_add DESC');

        [$list_title] = App::$db->getRow("select title from media_list where id=?", ['id' => $list_id]);
        $this->title = 'Файлы в разделе ' . $list_title;
        $this->breadcrumbs[] = ['title' => $this->title];

        return $this->render('media_item_table.html.twig', [], $result);
    }

    public function actionActive(int $list_id, int $id, string $active): string
    {
        $model = new MediaFile($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionCreate(int $list_id): string
    {
        $model = new MediaFile();
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->list_id = $list_id;
            $model->descr = replace_base_href($model->descr, true);
            $model->active = 'Y';
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveFile($model, $_FILES['upload_file']) && $model->save(false)) {
                App::addFlash('success', 'Файл успешно добавлен.');
            }
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('media_item_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $list_id, int $id): string
    {
        $model = new MediaFile($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->descr = replace_base_href($model->descr, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveFile($model, $_FILES['upload_file']) && $model->save(false)) {
                App::addFlash('success', 'Файл успешно изменен.');
            }
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        return $this->render('media_item_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $list_id, int $id): string
    {
        $model = new MediaFile($id);
        $this->deleteFile($model);
        $model->delete();
        $this->redirect('index');
    }

    public function showFile($file_name): string
    {
        if (is_file(App::$DIR . $this->file_path . $file_name)) {
            return '<a href="' . App::$SUBDIR . $this->file_path . $file_name . '" target="_blank">'.$file_name.'</a>';
        } else {
            return 'Отсутствует';
        }
    }

    private function saveFile(MediaFile $model, $file) : bool
    {
        if (!$file['size']) {
            return true;
        }
        $this->deleteFile($model);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($model->title) . '.' . $f_info['extension'];
        if (move_uploaded_file($file['tmp_name'], App::$DIR . $this->file_path . $file_name)) {
            $model->file_name = $file_name;
            return true;
        } else {
            App::addFlash('danger', 'Ошибка копирования файла !');
            return false;
        }
    }

    public function actionDeleteFile(int $list_id, $item_id): void
    {
        $model = new MediaFile($item_id);
        $this->deleteFile($model);
        $model->save(false);
        $this->redirect('update', ['id' =>$item_id]);
    }

    /**
     * @return false|null
     */
    private function deleteFile(MediaFile $model)
    {
        if (is_file(App::$DIR . $this->file_path . $model->file_name)) {
            if (!unlink(App::$DIR . $this->file_path . $model->file_name)) {
                App::addFlash('danger', 'Ошибка удаления файла');
                return false;
            }
        }
        $model->file_name = '';
    }
}
