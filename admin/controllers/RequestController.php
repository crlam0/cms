<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;

class RequestController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Заказы';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $query = "SELECT * from request order by id desc";
        $result = App::$db->query($query);
        return $this->render('request_list.html.twig', [], $result);
    }
    
    public function actionActive(int $id, string $active): string
    {
        App::$db->updateTable('request', ['active' => $active], ['id' => $id]);
        echo $active;
        exit;
    }
    
    public function actionDelete(int $id): void
    {
        App::$db->deleteFromTable('request', ['id' => $id]);
        $this->redirect('index');
    }
    
}
