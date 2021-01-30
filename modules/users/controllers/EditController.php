<?php

namespace modules\users\controllers;

use classes\App;
use classes\BaseController;
use classes\Image;

use modules\users\models\User;

class EditController extends BaseController
{
    private $image_path;
    private $image_width;    
    private $image_height;

    public function __construct() {
        parent::__construct();
        $this->title = 'Пользователи';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->image_path = App::$settings['modules']['users']['avatar_upload_path'];
        $this->image_width = App::$settings['modules']['users']['avatar_image_width'];
        $this->image_height = App::$settings['modules']['users']['avatar_image_height'];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $model = new User();
        $result = $model->findAll([], 'id ASC');        
        return $this->render('users_table.html.twig', [], $result);        
    }


    public function isActive(int $id): string 
    {
        $model = new User($id);
        return $model->haveFlag('active') ? 'Y' : 'N';
    }    
    
    public function actionActive(int $id, string $active): string 
    {
        $model = new User($id);
        if($active == 'Y') {
            $model->addFlag('active');
        } else {
            $model->delFlag('active');
        }            
        $model->save();
        echo $active;
        exit;
    }    
    
    public function actionCreate(): string 
    {
        $model = new User();
        if($model->load(App::$input['form']) &&  $model->validate()) {            
            $model->salt = $model->generateSalt();
            $model->passwd = $model->encryptPassword(App::$input['form']['passwd'], $model->salt);            
            $model->flags = App::$settings['default_flags'];
            $model->regdate = 'now()';
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Пользователь успешно добавлен.');
            $this->redirect('update', ['id' => $model->id]);
        }
        return $this->render('users_form.html.twig', [
            'model' => $model,
            'action' => 'create',
            'form_title' => 'Добавление',
        ]);
    }

    public function actionUpdate(int $id): string 
    {
        $model = new User($id);
        if($model->load(App::$input['form']) && $model->validate()){ 
            $model->salt = $model->generateSalt();
            $model->passwd = $model->encryptPassword(App::$input['form']['passwd'], $model->salt);            
            $this->saveImage($model, $_FILES['image_file']);
            $model->save(false);
            App::setFlash('success', 'Пользователь успешно изменён.');
            $this->redirect('update', ['id' => $model->id]);
        } 
        return $this->render('users_form.html.twig', [
            'model' => $model,
            'action' => $this->getUrl('update', ['id' => $id]),
            'form_title' => 'Изменение',
        ]);
    }
    
    public function actionDelete(int $id): string 
    {
        $model = new User($id);
        $this->deleteImageFile($model);
        $model->delete();
        $this->redirect('index');
    }    

    public function showImage($file_name): string{
        if (is_file(App::$DIR . $this->image_path . $file_name)) {
            return '<img src="' . App::$SUBDIR . $this->image_path . $file_name . '" border="0" width="'.$this->image_width.'" />';
        } else {
            return 'Отсутствует';
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
        $this->redirect('update', ['id' =>$user_id]);
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
    
    public function actionGetFlagsPopup($user_id): void 
    {
        $model = new User($user_id);
        $flags = $model->getFlagsAsArray();
        $result = App::$db->findAll('users_flags', [], 'title ASC');
        $content = App::$template->parse('users_flags.html.twig', ['user_id' => $user_id, 'flags' => $flags], $result);
        $json['content'] = $content;
        $json['result'] = 'OK';
        echo json_encode($json);
        exit;
    }
    
    public function actionAddNewFlag($new_flag_name, $user_id): void 
    {
        $flag_value = encodestring($new_flag_name);
        App::$db->insertTable('users_flags', ['title' => $new_flag_name, 'value' => $flag_value]);
        $flag_id = App::$db->insert_id();
        if($flag_id) {
            $model = new User($user_id);
            $model->addFlag($flag_value);
            $model->save(false);
        } else {
            echo App::$db->error();
        }
        echo 'OK';
        exit;
    }
    
    public function actionFlagChange($user_id, $flag_value, $value): void 
    {
        $model = new User($user_id);
        if(strlen($value)>0) {
            $model->addFlag($flag_value);
        } else {
            $model->delFlag($flag_value);
        }
        $model->save(false);
        echo 'OK';
        exit;
    }
    
    public function actionFlagDelete($flag_id): void 
    {
        App::$db->deleteFromTable('users_flags', ['id' => $flag_id]);
        echo 'OK';
        exit;
    }
    
}

