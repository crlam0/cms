<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;

class TemplatesEditController extends BaseController
{    
    
    private $TABLE;
    public $path;
    
    public function __construct() {
        parent::__construct();
        $this->TABLE = 'templates';
        $this->path = App::$SUBDIR . 'admin/templates-edit/';        
        $this->title = 'Шаблоны';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $query = "SELECT * from {$this->TABLE} order by name asc";
        $result = App::$db->query($query);
        
        return App::$template->parse('templates_table.html.twig', ['this' => $this], $result);        
    }
    
    public function actionCreate(): string 
    {
        if(is_array(App::$input['form'])) {
            $query = "insert into {$this->TABLE} " . App::$db->insertFields(App::$input['form']);
            App::$db->query($query);
        }
        $tags = [
            'this' => $this,
            'action' => 'create',
            'id' => '',
            'form_title' => 'Добавление',
            'name' => '',
            'content' => '',
            'comment' => '',
            'uri' => '',
            'file_name' => null,
            'template_type' => 'twig',
        ];
        
        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/edit_area/edit_area_full.js"></script>' . "\n";
        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/js/editor_html.js"></script>' . "\n";
        return App::$template->parse('templates_form.html.twig', $tags);
    }

    public function actionUpdate(): string 
    {
        if(is_array(App::$input['form']) && !App::$input['revert']) {
            if(App::$input['form']['template_type']==='twig' && strlen(App::$input['form']['file_name'])) {
                if(!$this->twigTplSave(App::$input['form']['file_name'], App::$input['form']['content'])) {
                    echo App::$message->get('error', [], 'Ошибка сохранения файла шаблона.');
                }
            }    
            $query = "update {$this->TABLE} set " . App::$db->updateFields(App::$input['form']) . ' where id=?';
            App::$db->query($query, ['id' => App::$input['id']]);
        }
        
        if(App::$input['update_and_exit']) {
            return $this->actionIndex();
        }
        
        $tags = App::$db->getRow("select * from {$this->TABLE} where id=?", ['id' => App::$input['id']]);
        $tags['this'] = $this;
        $tags['action'] = 'update';
        $tags['form_title'] = 'Изменение';
        if($tags['template_type']==='twig' && strlen($tags['file_name'])) {
            $tags['content'] = $this->twigTplLoad($tags['file_name']);
        }         

        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/edit_area/edit_area_full.js"></script>' . "\n";
        $this->tags['INCLUDE_HEAD'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'include/js/editor_html.js"></script>' . "\n";
        return App::$template->parse('templates_form.html.twig', $tags);        
    }
    
    public function actionDelete(): string 
    {
        $query = "delete from {$this->TABLE} where id=?";
        App::$db->query($query , ['id' => App::$input['id']]);  
        return $this->actionIndex();
    }
    
    private function twigTplLoad($filename){
        if(!strstr($filename,'.html.twig')) {
            $filename.='.html.twig';
        }
        $filename = App::$DIR . 'templates/' . $filename;
        if(file_exists($filename)) {
            return file_get_contents($filename);
        } else {
            return '';
        }    
    }

    private function twigTplSave($filename, $content){
        if(!strstr($filename,'.html.twig')) {
            $filename.='.html.twig';
        }
        $filename = App::$DIR . 'templates/' . $filename;
        if(!strlen($content)) {
            if(file_exists($filename)) {
                return true;
            }
            $content = PHP_EOL;
        }
        return file_put_contents($filename, stripcslashes($content)) && clear_cache_dir('twig');
    }

}

