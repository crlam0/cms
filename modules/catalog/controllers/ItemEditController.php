<?php

namespace modules\catalog\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\catalog\models\CatalogItem;

class ItemEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Наименования';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->image_path = App::$settings['modules']['catalog']['item_upload_path'] ?? 'upload/cat_item/';
        $this->image_width = App::$settings['modules']['catalog']['item_image_width'] ?? 640;
        $this->image_height = App::$settings['modules']['catalog']['item_image_height'] ?? 480;
        $this->user_flag = 'admin';
    }

    public function actionIndex(int $part_id): string
    {
        $model = new CatalogItem;
        $result = $model->findAll(['part_id' => $part_id], 'date_add DESC');

        [$list_title] = App::$db->getRow("select title from cat_part where id=?", ['id' => $part_id]);
        $this->title = 'Наименования в разделе ' . $list_title;
        $this->breadcrumbs[] = ['title' => $this->title];

        return $this->render('catalog_edit_item_table.html.twig', [], $result);
    }

    public function actionActive(int $part_id, int $id, string $active): string
    {
        $model = new CatalogItem($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionCreate(int $part_id): string
    {
        $model = new CatalogItem();
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->part_id = $part_id;
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->descr_full = replace_base_href($model->descr_full, true);
            $model->active = 'Y';
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($model->save(false)) {
                App::addFlash('success', 'Наименовение успешно добавлено.');
            }
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        // App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->num = 1;
        return $this->render('catalog_edit_item_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $part_id, int $id): string
    {
        $model = new CatalogItem($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->descr_full = replace_base_href($model->descr_full, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($model->save(false)) {
                App::addFlash('success', 'Наименование успешно изменено.');
            }
            $this->redirect('update', ['id' =>$model->id]);
        }
        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('js', 'include/js/jquery.form.js');
        App::addAsset('header', 'X-XSS-Protection:0');
        $model->descr = replace_base_href($model->descr, false);
        $model->descr_full = replace_base_href($model->descr_full, false);
        $main_form = $this->render('catalog_edit_item_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
        $images_form = $this->render('catalog_edit_item_images_form.html.twig', [
            'item_id' => $id,
        ]);
        return $main_form . $images_form;
    }

    public function actionDelete(int $part_id, int $id): void
    {
        $model = new CatalogItem($id);
        foreach ($model->getImages() as $image) {
            $this->deleteImageFile($image['file_name']);
            App::$db->deleteFromTable('cat_item_images', ['id' => $image['id']]);
        }
        $model->delete();
        $this->redirect('index');
    }

    public function showImage($image_id): string
    {
        [$file_name] = App::$db->getRow('select file_name from cat_item_images where id = ?', ['id' => $image_id]);
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="200" />';
        } else {
            return 'Отсутствует';
        }
    }

    public function actionGetImagesList(int $part_id, int $item_id)
    {
        $IMG_URL = App::$SUBDIR . $this->image_path;
        $query = "select cat_item_images.*,default_img,cat_item.id as item_id from cat_item_images left join cat_item on (cat_item.id=item_id) where item_id=?";
        $result = App::$db->query($query, ['item_id' => $item_id]);
        echo $this->render('catalog_edit_item_images_table.html.twig', ['image_path' => $this->image_path], $result);
        exit;
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

    public function actionAddMultipleImages(int $part_id, int $item_id): void
    {
        $model = new CatalogItem($item_id);
        if ($_FILES['files']) {
            $file_array = $this->reArrayFiles($_FILES['files']);
            foreach ($file_array as $file) {
                $this->saveImage($model, $file);
            }App::addFlash('success', 'Изображения успешно добавлены.');
        }
        $this->redirect('update', ['id' =>$model->id]);
    }

    private function saveImage(CatalogItem $model, $file)
    {
        if (!$file['size']) {
            return true;
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            App::addFlash('danger', 'Неверный тип файла !');
            return false;
        }
        $f_info = pathinfo($file['name']);
        $file_name = $model->id . '_' . encodestring($f_info['filename']) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, $this->image_width, $this->image_height)) {
            App::$db->insertTable('cat_item_images', [
                'item_id' => $model->id,
                'date_add' => 'now()',
                'file_name' => $file_name,
                'file_type' => $file['type'],
                'descr' => App::$input['descr'] ?? '',
            ]);
            $image_id = App::$db->insert_id();
            if (!$model->default_img) {
                $model->default_img = $image_id;
                $model->save(false);
            }
            return true;
        } else {
            App::addFlash('danger', 'Ошибка копирования файла ' . $f_info['filename']);
            return false;
        }
    }

    public function actionUploadImageFile(int $part_id, int $item_id)
    {
        $item = new CatalogItem($item_id);
        if ($this->saveImage($item, $_FILES['img_file'])) {
            echo 'OK';
        } else {
            echo 'upload error';
        }
        exit;
    }

    public function actionSetDefaultImage(int $part_id, int $item_id, int $image_id)
    {
        $query = "update cat_item set default_img=? where id=?";
        App::$db->query($query, ['default_img'=>$image_id, 'id'=>$item_id]);
        echo 'OK';
        exit;
    }

    public function actionDeleteImageFile(int $part_id, $image_id)
    {
        list($file_name) = App::$db->getRow('select file_name from cat_item_images where id=?', ['id' => $image_id]);
        App::$db->deleteFromTable('cat_item_images', ['id' => $image_id]);
        if ($this->deleteImageFile($file_name)) {
            echo 'OK';
        } else {
            echo 'Error delete file';
        }
        exit;
    }

    /**
     * @return false|null
     */
    private function deleteImageFile($file_name)
    {
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            if (!unlink(App::$DIR . $this->image_path . $file_name)) {
                App::addFlash('danger', 'Ошибка удаления файла');
                return false;
            }
        }
        return true;
    }
}
