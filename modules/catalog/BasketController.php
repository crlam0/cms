<?php

namespace modules\catalog;

use Classes\BaseController;
use Classes\App;
use Classes\SummToStr;

include 'functions.php';

class BasketController extends BaseController
{ 
    public function actionAddBuy()
    {
        global $_SESSION;
        if (!isset($_SESSION['BUY'][App::$input['item_id']]['count'])) {
            $_SESSION['BUY'][App::$input['item_id']]['count'] = 0;
        }
        $cnt = (int) App::$input['cnt'];
        if ($cnt > 0 && $cnt < 99) {
            $_SESSION['BUY'][App::$input['item_id']]['count'] += $cnt;
            $json['result'] = 'OK';
            $json['count'] = count($_SESSION['BUY']);
        } else {
            $json['result'] = 'ERR';
        }
        echo json_encode($json);
        exit;        
    }
    
    private function getDiscount($summ){        
        $query="SELECT discount from discount where summ<='{$summ}' order by summ desc";
        $result=my_query($query);
        if($result->num_rows){
            list($discount)=$result->fetch_array();
        }else{
            $discount=0;
        }
        return $discount;
    }

    private function calcDiscount($summ,$discount){
        return $summ*(1-$discount/100);
    }    
    
    private function getBasketData()
    {
        global $_SESSION;        
        $where='';
        foreach ($_SESSION['BUY'] as $item_id => $cnt) {
            $where.= !strlen($where) ? " id='{$item_id}'" : " or id='{$item_id}'" ;
        } 
        $query = "select * from cat_item where $where order by b_code,title asc";
        $result = App::$db->query($query, true);
        $summ = 0;
        $cnt = 0;
        $item_list = '';
        if ($result->num_rows) {
            while ($row = $result->fetch_array()) {
                $summ+=$row['price'] * $_SESSION['BUY'][$row['id']]['count'];
                $cnt+=$_SESSION['BUY'][$row['id']]['count'];
                $item_list.="Наименовние: {$row['title']}\t Кол-во:" . $_SESSION["BUY"][$row['id']]['count'] . "\t  Цена: {$row['price']}\n";
            }
        }
        return ['summ' => $summ, 'cnt' => $cnt, 'item_list' => $item_list, 'result' => $result];
    }
    
    public function actionGetSummary()
    {
        global $_SESSION;
        if (count($_SESSION['BUY'])) {
            $data = $this->getBasketData();
            $summ = $data['summ'];
            $result = $data['result'];
            $result->data_seek(0);
            $tags['summ'] = add_zero($summ);
            $tags['discount'] = $this->getDiscount($summ);
            $tags['summ_with_discount'] = add_zero($this->calcDiscount($summ, $tags['discount']));
            $content = App::$template->parse('basket_summary.html.twig', $tags, $result);
            echo $content;
        } else {
            echo App::$message->get('notice',[],"Корзина пуста !");
        }
        exit();
    }
    
    private function checkInput($input) {        
        if (strlen($input['lastname'])<3) {
            return App::$message->get('error', [], 'Неверно заполнено поле &quot;Фамилия&quot;');
        }
        if (strlen($input['firstname'])<2) {
            return App::$message->get('error', [], 'Неверно заполнено поле &quot;Имя&quot;');
        }
        if (!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input['email'])) {
            return App::$message->get('error', [], 'Анкета заполнена неверно ! Неверный адрес E-Mail');
        }
        if (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $input['phone'])) {
            return App::$message->get('error', [], 'Анкета заполнена неверно ! Неверный номер мобильного телефона. Формат: +7-xxx-xxx-xxxx или xxx-xx-xx');
        }
        return true;
    }
    
    private function requestDone($form) 
    {        
        global $BASE_HREF, $_SESSION;
        $data = $this->getBasketData();
        $summ = $data['summ'];
        $item_list = $data['item_list'];
        $result = $data['result'];
        $result->data_seek(0);

        $tags['summ'] = add_zero($summ);
        $tags['discount'] = $this->getDiscount($summ);
        $tags['summ_with_discount'] = add_zero($this->calcDiscount($summ, $tags['discount']));
        $tags['form'] = $form;
        $content = App::$template->parse('basket_mail.html.twig', $tags, $result);
        if(!App::$settings['debug']){
            App::$message->mail(App::$settings['email_to_addr'], 'Запрос с сайта ' . $BASE_HREF, $content, 'text/html');
        }
        
        $contact_info='ФИО: ' . $form['lastname'] . ' ' . $form['firstname'] . PHP_EOL;
        $contact_info.='E-Mail: ' . $form['email'] . PHP_EOL;
        $contact_info.='Телефон: ' . $form['phone'] . PHP_EOL;
        $contact_info.='IP адрес: ' . App::$server['REMOTE_ADDR'] . PHP_EOL;
        
        $query = "insert into request(date,item_list,contact_info,comment) values(now(),'" . $item_list . "','" . $contact_info . "','" . $form['comment']."')";
        App::$db->query($query, true);
        unset($_SESSION['BUY']);
        return my_msg_to_str('',[],'Ваш заказ принят! В ближайшее время с Вами свяжется наш менеджер для подтверждения  и уточнения по замене, если на данный период времени некоторые позиции отсутствуют.');
    }
    
    public function actionRequest() 
    {        
        $this->title = 'Оформить заказ';
        $this->breadcrumbs[] = [ 'title' => 'Корзина', 'url' => 'basket/' ];
        $this->breadcrumbs[] = [ 'title' => $this->title ];
        
        if ( !isset($_SESSION['BUY']) || !is_array($_SESSION['BUY']) ||  !count($_SESSION['BUY'])) {
            return App::$message->get('notice',[],'Корзина пуста !');
        }
        
        $content = '';
        if(App::$input['request_done']) {
            $input_result = $this->checkInput(App::$input['form']);
            if($input_result === true) {
                return $this->requestDone(App::$input['form']);
            } else {
                $content .= $input_result;
                $tags = App::$input['form'];
            }
        } else {
            $tags = [
                'lastname' => '',
                'firstname' => '',
                'phone' => '',
                'email' => '',
                'comment' => '',
            ];
        }
        $content .= App::$template->parse('basket_request.html.twig', $tags);
        return $content;
    }
    
    public function actionClear()
    {
        unset($_SESSION['BUY']);
        redirect(App::$SUBDIR . 'basket/');
        exit;
    }
    
    public function actionDel()
    {
        $item_id = App::$input['item_id'];
        unset($_SESSION['BUY'][$item_id]);
        return $this->actionIndex();
    }    
    
    private function buttonClick($type) {
        foreach(App::$input['buy_cnt'] as $item_id => $item_cnt) {
            if (is_numeric($item_cnt) && $item_cnt > 0 && $item_cnt < 99 ) {
                $_SESSION['BUY'][$item_id]['count'] = $item_cnt;
            }
        }
        if($type == 'request') {
            redirect(App::$SUBDIR . 'basket/request');
        }        
    }
    
    public function actionIndex()
    {
        global $_SESSION;
        
        $this->title = 'Корзина';
        $this->breadcrumbs[] = ['title' => $this->title ];
        
        if(App::$input['button']) {
            $this->buttonClick(App::$input['button']);
        }
        
        if ( !isset($_SESSION['BUY']) || !is_array($_SESSION['BUY']) ||  !count($_SESSION['BUY'])) {
            return App::$message->get('notice',[],'Корзина пуста !');
        }
        $where = '';
        $count = 0;
        foreach ($_SESSION["BUY"] as $item_id => $cnt) {
            $where.=(!strlen($where) ? " cat_item.id='$item_id'" : " or cat_item.id='$item_id'");
            $count = $count + (int)$cnt;
        }
        $query = "select cat_item.*,fname,cat_item_images.id as cat_item_images_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where $where order by b_code,title asc";
        $result = App::$db->query($query, true);
        $summ = 0;
        $cnt = 0;
        while ($row = $result->fetch_array()) {
            $summ+=$row['price'] * $_SESSION['BUY'][$row['id']]['count'];
            $cnt+=$_SESSION['BUY'][$row['id']]['count'];
        }
        $result->data_seek(0);
        $tags['summ'] = add_zero($summ);
        $tags['discount'] = $this->getDiscount($summ);
        $tags['summ_with_discount'] = add_zero($this->calcDiscount($summ, $tags['discount']));
        $tags['summ_with_discount_str'] = SummToStr::get($this->calcDiscount($summ, $tags['discount']));
        $content = App::$template->parse('basket_index.html.twig', $tags, $result);
        return $content;
    }
    
}
