<?php

namespace modules\article\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\article\models\ArticleItem;

class ItemEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Статьи';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->image_path = App::$settings['modules']['article']['item_upload_path'] ?? 'upload/article/';
        $this->image_width = App::$settings['modules']['article']['item_image_width'] ?? 640;
        $this->image_height = App::$settings['modules']['article']['item_image_height'] ?? 480;
        $this->user_flag = 'admin';
    }

    public function actionIndex(int $list_id): string
    {
        $model = new ArticleItem;
        $result = $model->findAll(['list_id' => $list_id], 'date_add DESC');

        [$list_title] = App::$db->getRow("select title from article_list where id=?", ['id' => $list_id]);
        $this->title = 'Статьи в разделе ' . $list_title;
        $this->breadcrumbs[] = ['title' => $this->title];

        return $this->render('article_item_table.html.twig', [], $result);
    }

    public function actionActive(int $list_id, int $id, string $active): string
    {
        $model = new ArticleItem($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionCreate(int $list_id): string
    {
        $model = new ArticleItem();
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->list_id = $list_id;
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->content = replace_base_href($model->content, true);
            $model->active = 'Y';
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::setFlash('success', 'Статья успешно добавлена.');
            }
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->content = replace_base_href($model->content, false);
        return $this->render('article_item_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $list_id, int $id): string
    {
        $model = new ArticleItem($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->content = replace_base_href($model->content, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::setFlash('success', 'Статья успешно изменена.');
            }
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->content = replace_base_href($model->content, false);
        return $this->render('article_item_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $list_id, int $id): string
    {
        $model = new ArticleItem($id);
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

    private function saveImage(ArticleItem $model, $file)
    {
        if (!$file['size']) {
            return true;
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            App::setFlash('danger', 'Неверный тип файла !');
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
            App::setFlash('danger', 'Ошибка копирования файла !');
            return false;
        }
    }

    public function actionDeleteImageFile(int $list_id, $post_id): void
    {
        $model = new ArticleItem($post_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' =>$post_id]);
    }

    /**
     * @return false|null
     */
    private function deleteImageFile(ArticleItem $model)
    {
        if (is_file(App::$DIR . $this->image_path . $model->image_name)) {
            if (!unlink(App::$DIR . $this->image_path . $model->image_name)) {
                App::setFlash('danger', 'Ошибка удаления файла');
                return false;
            }
        }
        $model->image_name = '';
        $model->image_type = '';
    }
}
