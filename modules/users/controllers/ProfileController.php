<?php

namespace modules\users\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\users\models\User;

class ProfileController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Профиль';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->image_path = App::$settings['modules']['users']['avatar_upload_path'];
        $this->image_width = App::$settings['modules']['users']['avatar_image_width'];
        $this->image_height = App::$settings['modules']['users']['avatar_image_height'];
        $this->user_flag = 'passwd';
    }

    public function actionIndex(): string
    {
        $model = new User(App::$user->id);
        if ($model->load(App::$input['form']) && $model->validate() && check_csrf_token()) {
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Профиль успешно сохранён');
            $this->redirect('');
        }
        return $this->render('user_profile.html.twig', [
            'model' => $model,
            'action' => $this->getUrl(''),
            'form_title' => 'Изменение',
        ]);
    }

    /**
     * @return null|string
     */
    public function showImage($file_name)
    {
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="'.$this->image_width.'" />';
        }
    }

    private function saveImage(User $model, $file): string
    {
        $content = '';
        if ($file['size'] < 100) {
            return '';
        }
        if (!in_array($file['type'], Image::$validImageTypes)) {
            return App::$message->get('error', [], 'Неверный тип файла !');
        }
        $this->deleteImageFile($model);
        $f_info = pathinfo($file['name']);
        $file_name = encodestring($model->login) . '.' . $f_info['extension'];
        if (move_uploaded_image($file, App::$DIR . $this->image_path . $file_name, null, null, $this->image_width, $this->image_height)) {
            $model->avatar = $file_name;
            $content .= App::$message->get('', [], 'Изображение успешно добавлено.');
        } else {
            $content .= App::$message->get('error', [], 'Ошибка копирования файла !');
        }
        return $content;
    }

    public function actionDeleteImageFile($user_id): void
    {
        $model = new User($user_id);
        $this->deleteImageFile($model);
        $model->save(false);
        $this->redirect('');
    }

    /**
     * @return null|string
     */
    private function deleteImageFile(User $model)
    {
        if (is_file(App::$DIR . $this->image_path . $model->avatar)) {
            if (!unlink(App::$DIR . $this->image_path . $model->avatar)) {
                return App::$message->get('error', [], 'Ошибка удаления файла');
            }
        }
        $model->avatar = '';
    }
}
