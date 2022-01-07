<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;

class PartsEditController extends BaseController
{

    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'parts';
        $this->title = 'Разделы';
        $this->breadcrumbs[] = ['title'=>$this->title];
    }

    public function actionIndex(): string
    {
        $query = "SELECT * from {$this->table} order by title asc";
        $result = App::$db->query($query);

        return $this->render('parts_table.html.twig', [], $result);
    }

    public function actionCreate(): string
    {
        if (is_array(App::$input['form'])) {
            App::$db->insertTable($this->table, App::$input['form']);
            redirect('index');
        }
        $tags = [
            'action' => 'create',
            'form_title' => 'Добавление',
            'id' => '',
            'title' => '',
            'uri' => '',
            'user_flag' => '',
            'tpl_name' => '',
        ];
        $flags = App::$db->findAll('users_flags', [], 'title asc');
        $tags['flags'] = $flags->fetch_all(MYSQLI_ASSOC);
        return $this->render('parts_form.html.twig', $tags);
    }

    public function actionUpdate(int $id): string
    {
        if (is_array(App::$input['form'])) {
            App::$db->updateTable($this->table, App::$input['form'], ['id' => $id]);
            redirect('index');
        }
        $tags = App::$db->getRow("select * from {$this->table} where id=?", ['id' => $id]);
        $tags['action'] = $this->getUrl('update', ['id' => $id]);
        $tags['form_title'] = 'Изменение';
        $flags = App::$db->findAll('users_flags', [], 'title asc');
        $tags['flags'] = $flags->fetch_all(MYSQLI_ASSOC);
        return $this->render('parts_form.html.twig', $tags);
    }

    public function actionDelete(int $id): string
    {
        App::$db->deleteFromTable($this->table, ['id' => $id]);
        return $this->actionIndex();
    }
}
