<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;

class FaqEditController extends BaseController
{

    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'faq';
        $this->title = 'Вопрос/ответ';
        $this->breadcrumbs[] = ['title' => $this->title];
    }

    public function actionIndex(): string
    {
        $result = App::$db->findAll($this->table, [], 'date desc,author asc');
        // $query = "SELECT * from {$this->table} order by date,author asc";
        // $result = App::$db->query($query);

        return $this->render('faq_table.html.twig', [], $result);
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
        $content = '';
        if (is_array(App::$input['form'])) {
            App::$input['form']['ans_uid'] = App::$user->id;
            App::$input['form']['date'] = App::$input['form']['date'] ?? 'now()';
            App::$db->insertTable($this->table, App::$input['form']);
            redirect('index');
        }
        $tags = [
            'action' => 'create',
            'form_title' => 'Добавление',
            'id' => '',
            'active' => '',
            'date' => '',
            'author' => '',
            'email' => '',
            'txt' => '',
            'ans' => '',
        ];

        return $this->render('faq_edit_form.html.twig', $tags);
    }

    public function actionUpdate(int $id): string
    {
        global $_FILES;
        if (is_array(App::$input['form'])) {
            App::$input['form']['ans_uid'] = App::$user->id;
            App::$db->updateTable($this->table, App::$input['form'], ['id' => $id]);
            redirect('index');
        }
        $tags = App::$db->getRow("select * from {$this->table} where id=?", ['id' => $id]);
        $tags['action'] = $this->getUrl('update', ['id' => $id]);
        $tags['form_title'] = 'Изменение';

        return $this->render('faq_edit_form.html.twig', $tags);
    }

    public function actionDelete(int $id): string
    {
        App::$db->deleteFromTable($this->table, ['id' => $id]);
        return $this->actionIndex();
    }
}
