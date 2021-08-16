<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use admin\models\MenuItem;

class MenuItemEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Меню';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->image_path = App::$settings['menu']['upload_path'] ?? 'theme/menu/';
        $this->image_width = App::$settings['menu']['image_width'] ?? 32;
        $this->image_height = App::$settings['menu']['image_height'] ?? 32;
        $this->user_flag = 'admin';
    }

    public function actionIndex(int $menu_id): string
    {
        $model = new MenuItem;
        $result = $model->findAll(['menu_id' => $menu_id], 'position ASC');

        [$list_title] = App::$db->getRow("select title from menu_list where id=?", ['id' => $menu_id]);
        $this->title = 'Пункты в меню ' . $list_title;
        $this->breadcrumbs[] = ['title' => $this->title];

        return $this->render('menu_item_table.html.twig', [], $result);
    }

    public function actionActive(int $menu_id, int $id, int $active): string
    {
        $model = new MenuItem($id);
        $model->active = $active;
        $model->save();
        echo $active;
        exit;
    }

    public function actionCreate(int $menu_id): string
    {
        $model = new MenuItem();
        App::$input['form']['menu_id'] = $menu_id;
        App::$input['form']['target_id'] = App::$input['form']['target_id'] ?? 0;
        if ($model->load(App::$input['form']) && $model->validate()) {
            $model->active = '1';
            if (!$model->href) {
                $model->href = '';
            }
            if (!$model->image_name) {
                $model->image_name = '';
                $model->image_type = '';
            }            
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::addFlash('success', 'Пункт меню успешно добавлен.');
            $this->redirect('index');
        }
        $user_flags_result = App::$db->query('select title,value from users_flags');
        $user_flags = $user_flags_result->fetch_all(MYSQLI_ASSOC);

        $menu_list_result = App::$db->query("select id,title from menu_list where id<>?", ['id' => $menu_id]);
        $menu_list = $menu_list_result->fetch_all(MYSQLI_ASSOC);

        return $this->render('menu_item_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
            'target_types' => $this->target_types,
            'user_flags' => $user_flags,
            'menu_list' => $menu_list,
        ]);
    }

    public function actionUpdate(int $menu_id, int $id): string
    {
        $model = new MenuItem($id);
        App::$input['form']['target_id'] = App::$input['form']['target_id'] ?? 0;
        if ($model->load(App::$input['form']) && $model->validate()) {
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::addFlash('success', 'Пункт меню успешно изменён.');
            $this->redirect('index');
        }

        $user_flags_result = App::$db->query('select title,value from users_flags');
        $user_flags = $user_flags_result->fetch_all(MYSQLI_ASSOC);

        $menu_list_result = App::$db->query("select id,title from menu_list where id<>?", ['id' => $menu_id]);
        $menu_list = $menu_list_result->fetch_all(MYSQLI_ASSOC);

        return $this->render('menu_item_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
            'target_types' => $this->target_types,
            'user_flags' => $user_flags,
            'menu_list' => $menu_list,
        ]);
    }

    public function actionDelete(int $menu_id, int $id): string
    {
        $model = new MenuItem($id);
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
     * @return false|string
     */
    private function saveImage(MenuItem $model, $file): string
    {
        $content = '';
        if ($file['size'] < 100) {
            return '';
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            App::addFlash('danger', 'Неверный тип файла !');
            return '';
        }
        $this->deleteImageFile($model);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($model->title) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            $model->image_name = $file_name;
            $model->image_type = $file['type'];
            App::addFlash('danger', 'Изображение успешно добавлено.');
        } else {
            App::addFlash('danger', 'Ошибка копирования файла !');
        }
        return $content;
    }

    public function actionDeleteImageFile(int $menu_id, $item_id): void
    {
        $model = new MenuItem($item_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('update', ['id' =>$item_id]);
    }

    /**
     * @return false|null
     */
    private function deleteImageFile(MenuItem $model)
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

    public $target_types = [
        [
            'type' => 'link',
            'name' => 'Ссылка'
        ],
        [
            'type' => 'article_list',
            'name' => 'Раздел статей'
        ],
        [
            'type' => 'article',
            'name' => 'Статья'
        ],
        [
            'type' => 'media_list',
            'name' => 'Раздел файлов'
        ],
        [
            'type' => 'catalog',
            'name' => 'Каталог'
        ],
        [
            'type' => 'cat_part',
            'name' => 'Раздел каталога'
        ],
        [
            'type' => 'gallery_list',
            'name' => 'Раздел галереи'
        ],

    ];

    public function actionGetTargetSelect(int $menu_id, $item_id, $target_type): void
    {
        $model = new MenuItem($item_id);
        $target_id = $model->target_id;
        $href = $model->href;

        function get_option($name, $sql, $target_id): string
        {
            $result = App::$db->query($sql);
            $output = '<td>' . $name . ':</td><td><select class="form-control" name="form[target_id]">';
            while ($row = $result->fetch_array()) {
                $output.="<option value={$row['id']}" . ($row['id'] == $target_id ? ' selected' : '') . ">{$row['title']}</option>";
            }
            $output.="</select></td>";
            return $output;
        }

        $output = '';

        switch ($target_type) {
            case 'link':
                $output = '<td>Прямая ссылка:</td><td><input class="form-control" type="edit" maxlength="255" size="64" name="form[href]" value="' . $href . '"></td>';
                break;
            case 'article':
                $output = get_option('Статья', 'select * from article_item order by title', $target_id);
                break;
            case 'article_list':
                $output = get_option('Раздел статей', 'select * from article_list order by title', $target_id);
                break;
            case 'media_list':
                $output = get_option('Раздел файлов', 'select * from media_list order by title', $target_id);
                break;
            case 'cat_part':
                $output = get_option('Раздел каталога', 'select * from cat_part where prev_id=0 order by title', $target_id);
                break;
            case 'gallery_list':
                $output = get_option('Раздел галереи', 'select * from gallery_list order by title', $target_id);
                break;
        }
        echo $output;
        exit;
    }
}
