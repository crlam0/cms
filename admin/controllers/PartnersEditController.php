<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

class PartnersEditController extends BaseController
{


    private $table;
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'partners';
        $this->image_path = App::$settings['partners']['upload_path'] ?? 'upload/';
        $this->image_width = App::$settings['partners']['image_width'] ?? 512;

        $this->title = 'Партнёры';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $query = "SELECT * from {$this->table} order by pos,title asc";
        $result = App::$db->query($query);

        return $this->render('partners_table.html.twig', [], $result);
    }

    public function actionActive(int $id, string $active): string
    {
        App::$db->updateTable($this->table, ['active' => $active], ['id' => $id]);
        echo $active;
        exit;
    }

    public function actionCreate(): string
    {
        global $_FILES;
        if (is_array(App::$input['form'])) {
            App::$db->insertTable($this->table, App::$input['form']);
            $item_id = App::$db->insert_id();
            if ($this->saveImage($_FILES['image_file'], $item_id, App::$input['form']['title'])) {
                App::addFlash('success', 'Партнёр успешно добавлен');
            }
            redirect('index');
        }
        $tags = [
            'action' => 'create',
            'form_title' => 'Добавление',
            'id' => '',
            'pos' => '',
            'active' => '',
            'title' => '',
            'descr' => '',
            'url' => '',
            'file_name' => null,
            'file_type' => null,
        ];

        App::addAsset('js', 'vendor/ckeditor/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');

        return $this->render('partners_form.html.twig', $tags);
    }

    public function actionUpdate(int $id): string
    {
        global $_FILES;
        if (is_array(App::$input['form'])) {
            App::$db->updateTable($this->table, App::$input['form'], ['id' => $id]);
            if ($this->saveImage($_FILES['image_file'], $id, App::$input['form']['title'])) {
                App::addFlash('success', 'Партнёр успешно обновлён');
            }
            redirect('index');
        }
        $tags = App::$db->getRow("select * from {$this->table} where id=?", ['id' => $id]);
        $tags['action'] = $this->getUrl('update', ['id' => $id]);
        $tags['form_title'] = 'Изменение';

        App::addAsset('js', 'vendor/ckeditor/ckeditor/ckeditor.js');
        App::addAsset('js', 'include/js/editor.js');
        App::addAsset('header', 'X-XSS-Protection:0');

        return $this->render('partners_form.html.twig', $tags);
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
            return 'Отсутствует';
        }
    }

    private function saveImage($file, int $item_id, string $title): string
    {
        if (!$file['size']) {
            return true;
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            App::addFlash('danger', 'Неверный тип файла !');
            return false;
        }
        $this->deleteImageFile($item_id);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, $this->image_width)) {
            App::$db->updateTable($this->table, ['file_name' => $file_name, 'file_type' => $file['type']], ['id' => $item_id]);
            return true;
        } else {
            App::addFlash('danger', 'Ошибка копирования файла !');
            return false;
        }
    }

    public function actionDeleteImageFile($item_id): void
    {
        $this->deleteImageFile($item_id);
        App::$db->updateTable($this->table, ['file_name' => '', 'file_type' => ''], ['id' => $item_id]);
        $this->redirect('update', ['id' => $item_id]);
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
