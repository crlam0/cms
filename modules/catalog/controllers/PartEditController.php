<?php

namespace modules\catalog\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\catalog\models\CatalogPart;

class PartEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Разделы каталога';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->image_path = App::$settings['modules']['catalog']['part_upload_path'] ?? 'upload/cat_part/';
        $this->image_width = App::$settings['modules']['catalog']['part_image_width'] ?? 200;
        $this->image_height = App::$settings['modules']['catalog']['part_image_height'] ?? 200;
        $this->user_flag = 'admin';
    }

    private function makeTree(&$tree, $prev_id, $deep, $exclude_id = -1): void
    {
        $query = "SELECT * from cat_part where prev_id=? order by num,title+1 asc";
        $result = App::$db->query($query, ['prev_id' => $prev_id]);
        while ($row = $result->fetch_array()) {
            if ($row['id'] == $exclude_id) {
                continue;
            }
            $spaces = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $deep);
            $strokes = str_repeat(' - ', $deep);
            $tree[] = [
                'spaces' => $spaces,
                'strokes' => $strokes,
                'id' => $row['id'],
                'title' => $row['title'],
                'data' => $row,
            ];
            $this->makeTree($tree, $row['id'], $deep + 1, $exclude_id);
        }
    }

    public function actionIndex(): string
    {
        $model = new CatalogPart;

        $tree = [];
        $this->makeTree($tree, 0, 0);

        $result = $model->findAll([], 'num ASC');
        return $this->render('catalog_edit_part_table.html.twig', ['tree' => $tree], $result);
    }

    public function actionActive(int $id, string $active): string
    {
        $model = new CatalogPart($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionCreate(): string
    {
        $model = new CatalogPart();
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_add = 'now()';
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::addFlash('success', 'Раздел успешно добавлен');
            }
            $this->redirect('index');
        }
        $tree = [];
        $this->makeTree($tree, 0, 0);

        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor_mini.js');
        App::addAsset('js', 'include/edit_area/edit_area_full.js');
        App::addAsset('js', 'include/js/editor_html.js');
        App::addAsset('header', 'X-XSS-Protection:0');

        $model->descr = replace_base_href($model->descr, false);
        return $this->render('catalog_edit_part_form.html.twig', [
            'model' => $model,
            'tree' => $tree,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string
    {
        $model = new CatalogPart($id);
        if ($model->load(App::$input['form']) && $model->validate()) {
            if (!$model->seo_alias) {
                $model->seo_alias = encodestring($model->title);
            }
            $model->descr = replace_base_href($model->descr, true);
            $model->date_change = 'now()';
            $model->uid = App::$user->id;
            if ($this->saveImage($model, $_FILES['image_file']) && $model->save(false)) {
                App::addFlash('success', 'Раздел успешно обновлён');
            }
            // $this->redirect('index');
        }

        $tree = [];
        $this->makeTree($tree, 0, 0, $id);

        App::addAsset('js', 'include/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor_mini.js');
        App::addAsset('js', 'include/edit_area/edit_area_full.js');
        App::addAsset('js', 'include/js/editor_html.js');
        App::addAsset('header', 'X-XSS-Protection:0');

        $model->descr = replace_base_href($model->descr, false);
        return $this->render('catalog_edit_part_form.html.twig', [
            'model' => $model,
            'tree' => $tree,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }

    public function actionDelete(int $id): string
    {
        if (App::$db->getRow("select id from cat_item where part_id=?", ['id' => $id])) {
            App::addFlash('danger', 'Этот раздел не пустой !');
            $this->redirect('index');
        }
        $model = new CatalogPart($id);
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
    private function saveImage(CatalogPart $model, $file)
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
        $file_name = $model->id . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, $this->image_width, $this->image_height)) {
            $model->image_name = $file_name;
            $model->image_type = $file['type'];
            return true;
        } else {
            App::addFlash('danger', 'Ошибка копирования файла ' . $file['name']);
            return false;
        }
    }

    public function actionDeleteImageFile($part_id): void
    {
        $model = new CatalogPart($part_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' => $part_id]);
    }

    /**
     * @return false|null
     */
    private function deleteImageFile(CatalogPart $model)
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

    public function getListGroupContent($part_id, $related_products)
    {
        $content = '';
        $query = "SELECT cat_item.* from cat_item where part_id=? order by num,title asc";
        $result_item = App::$db->query($query, ['part_id' => $part_id]);
        while ($row_item=$result_item->fetch_array()) {
            $state = '';
            if (array_key_exists($row_item['id'], $related_products)) {
                $state = ' checked';
            }
            $content .= '<li class="list-group-item">' . $row_item['title'] . '
                <input type="checkbox" class="related_products_input" item_id="' . $row_item['id'] . '" ' . $state . ' />
            </li>';
        }
        return $content;
    }

    public function actionGetRelatedProductsList($part_id)
    {
        list($json_row) = App::$db->getRow("select related_products from cat_part where id=?", ['id' => $part_id]);
        if (!$related_products = my_json_decode($json_row)) {
            $related_products=[];
        }
        $tree = [];
        $this->makeTree($tree, 0, 0);
        $json['content'] = $this->render('catalog_edit_part_popup_table.html.twig', [
                'related_products' => $related_products,
                'tree' => $tree,
            ]);
        $json['result'] = 'OK';
        echo json_encode($json);
        exit;
    }

    public function actionChangeRelatedProduct(int $part_id, int $item_id, $value)
    {
        list($json_row) = App::$db->getRow("select related_products from cat_part where id=?", ['id' => $part_id]);
        if (!$related_products = my_json_decode($json_row)) {
            $related_products=[];
        }
        if ($value>0) {
            $related_products[$item_id] = 'true';
        } else {
            unset($related_products[$item_id]);
        }
        $json = json_encode($related_products);
        App::$db->updateTable('cat_part', ['related_products' => $json], ['id' => $part_id]);
        echo 'OK';
        exit;
    }
}
