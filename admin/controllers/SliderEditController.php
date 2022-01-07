<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

class SliderEditController extends BaseController
{


    private $table;
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'slider_images';
        $this->image_path = App::$settings['slider']['upload_path'];
        $this->image_width = App::$settings['slider']['image_width'];
        $this->image_height = App::$settings['slider']['image_height'];

        $this->title = 'Картинки для слайдера';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $query = "SELECT * from {$this->table} order by pos,title asc";
        $result = App::$db->query($query);

        return $this->render('slider_images_table.html.twig', [], $result);
    }

    public function actionCreate(): string
    {
        global $_FILES;
        $content = '';
        if (is_array(App::$input['form'])) {
            App::$db->insertTable($this->table, App::$input['form']);
            $image_id = App::$db->insert_id();
            $file = $_FILES['image_file'];
            $content .= $this->saveImage($file, $image_id, $image_id);
        }
        $tags = [
            'action' => 'create',
            'id' => '',
            'form_title' => 'Добавление',
            'pos' => '1',
            'title' => '',
            'descr' => '',
            'url' => '',
            'file_name' => null,
        ];
        $content .= $this->render('slider_images_form.html.twig', $tags);
        return $content;
    }

    public function actionUpdate(int $id): string
    {
        global $_FILES;
        $content = '';
        if (is_array(App::$input['form'])) {
            App::$db->updateTable($this->table, App::$input['form'], ['id' => $id]);
            $file = $_FILES['image_file'];
            $content .= $this->saveImage($file, $id, $id);
        }
        $tags = App::$db->getRow("select * from {$this->table} where id=?", ['id' => $id]);
        $tags['action'] = $this->getUrl('update', ['id' => $id]);
        $tags['form_title'] = 'Изменение';

        $content .= $this->render('slider_images_form.html.twig', $tags);
        return $content;
    }

    public function actionDelete(int $id): string
    {
        $content = $this->deleteImageFile($id);
        App::$db->deleteFromTable($this->table, ['id' => $id]);
        $content .= $this->actionIndex();
        return $content;
    }

    public function showImage($file_name): string
    {
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="200" />';
        } else {
            return "Отсутствует";
        }
    }

    private function saveImage($file, int $image_id, int $title): string
    {
        $content = '';

        if ($file['size'] < 100) {
            return '';
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            return App::$message->get('error', [], 'Неверный тип файла !');
        }
        $this->deleteImageFile($image_id);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            App::$db->updateTable($this->table, ['file_name' => $file_name, 'file_type' => $file['type']], ['id' => $item_id]);
            $content .= App::$message->get('', [], 'Изображение успешно добавлено.');
        } else {
            $content .= App::$message->get('error', [], 'Ошибка копирования файла !');
        }
        return $content;
    }

    private function deleteImageFile(int $image_id): string
    {
        list($image_old) = App::$db->getRow("select file_name from {$this->table} where id=?", ['id' => $image_id]);
        if (is_file(App::$DIR . $this->image_path . $image_old)) {
            if (!unlink(App::$DIR . $this->image_path . $image_old)) {
                return  App::$message->get('error', [], 'Ошибка удаления файла');
            }
        }
        return '';
    }
}
