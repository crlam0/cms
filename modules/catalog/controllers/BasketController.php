<?php

namespace modules\catalog\controllers;

use classes\BaseController;
use classes\App;
use classes\SummToStr;

class BasketController extends Controller
{

    public function actionAddBuy(): string
    {
        if (!isset(App::$session['BUY'][App::$input['item_id']]['count'])) {
            App::$session['BUY'][App::$input['item_id']]['count'] = 0;
        }
        $cnt = (int) App::$input['cnt'];
        if ($cnt > 0 && $cnt < 99) {
            App::$session['BUY'][App::$input['item_id']]['count'] += $cnt;
            $json['result'] = 'OK';
            $json['count'] = count(App::$session['BUY']);
        } else {
            $json['result'] = 'ERR';
        }
        echo json_encode($json);
        exit;
    }

    private function getDiscount(int $summ): int
    {
        $query="SELECT discount from discount where summ<='{$summ}' order by summ desc";
        $result=App::$db->query($query);
        if ($result->num_rows) {
            list($discount)=$result->fetch_array();
        } else {
            $discount=0;
        }
        return $discount;
    }

    private function calcDiscount(int $summ, int $discount): float
    {
        return $summ*(1-$discount/100);
    }

    private function getBasketData(): array
    {
        $where='';
        foreach (App::$session['BUY'] as $item_id => $cnt) {
            $where.= !strlen($where) ? " id='{$item_id}'" : " or id='{$item_id}'" ;
        }
        $query = "select * from cat_item where $where order by b_code,title asc";
        $result = App::$db->query($query);
        $summ = 0;
        $cnt = 0;
        $item_list = '';
        if ($result->num_rows) {
            while ($row = $result->fetch_array()) {
                $summ += $row['price'] * App::$session['BUY'][$row['id']]['count'];
                $cnt += App::$session['BUY'][$row['id']]['count'];

                $item_list.="Наименовние: {$row['title']}\t Кол-во:" . App::$session["BUY"][$row['id']]['count'] . "\t  Цена: {$row['price']}\n";
            }
        }
        return ['summ' => $summ, 'cnt' => $cnt, 'item_list' => $item_list, 'result' => $result];
    }

    public function actionGetSummary(): void
    {
        if (count(App::$session['BUY'])) {
            $data = $this->getBasketData();
            $summ = $data['summ'];
            $result = $data['result'];
            $result->data_seek(0);
            $tags['summ'] = add_zero($summ);
            $tags['discount'] = $this->getDiscount($summ);
            $tags['summ_with_discount'] = add_zero($this->calcDiscount($summ, $tags['discount']));
            $content = $this->render('basket_summary.html.twig', $tags, $result);
            echo $content;
        } else {
            echo App::$message->get('notice', [], "Корзина пуста !");
        }
        exit();
    }

    /**
     * @return bool|string
     */
    private function checkInput(array $input)
    {
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

    private function requestDone(array $form) : string
    {
        $data = $this->getBasketData();
        $summ = $data['summ'];
        $item_list = $data['item_list'];
        $result = $data['result'];
        $result->data_seek(0);

        $tags['summ'] = add_zero($summ);
        $tags['discount'] = $this->getDiscount($summ);
        $tags['summ_with_discount'] = add_zero($this->calcDiscount($summ, $tags['discount']));
        $tags['form'] = $form;
        $content = $this->render('basket_mail.html.twig', $tags, $result);
        $remote_host=(check_key('REMOTE_HOST', App::$server) ? App::$server['REMOTE_HOST'] : gethostbyaddr(App::$server['REMOTE_ADDR']) );
        if (!App::$debug) {
            App::$message->mail(App::$settings['email_to_addr'], 'Запрос с сайта ' . $remote_host, $content, 'text/html');
        }

        unset($data);
        $data['date'] = 'now()';
        $data['item_list'] = $item_list;
        $data['contact_info']='ФИО: ' . $form['lastname'] . ' ' . $form['firstname'] . PHP_EOL;
        $data['contact_info'].='E-Mail: ' . $form['email'] . PHP_EOL;
        $data['contact_info'].='Телефон: ' . $form['phone'] . PHP_EOL;
        $data['contact_info'].='IP адрес: ' . App::$server['REMOTE_ADDR'] . PHP_EOL;
        $data['item_list'] = $item_list;
        $data['comment'] = $form['comment'];

        App::$db->insertTable('request', $data);
        
        unset(App::$session['BUY']);
        return App::$message->get('', [], 'Ваш заказ принят! В ближайшее время с Вами свяжется наш менеджер для подтверждения  и уточнения по замене, если на данный период времени некоторые позиции отсутствуют.');
    }

    public function actionRequest() : string
    {
        $this->title = 'Оформить заказ';
        $this->breadcrumbs[] = [ 'title' => 'Корзина', 'url' => 'basket/' ];
        $this->breadcrumbs[] = [ 'title' => $this->title ];

        if (!isset(App::$session['BUY']) || !is_array(App::$session['BUY']) ||  !count(App::$session['BUY'])) {
            return App::$message->get('notice', [], 'Корзина пуста !');
        }

        $content = '';
        if (App::$input['request_done']) {
            $input_result = $this->checkInput(App::$input['form']);
            if ($input_result === true) {
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

    public function actionClear(): void
    {
        App::$session['BUY'] = [];
        redirect(App::$SUBDIR . 'basket/');
        exit;
    }

    public function actionDel(): string
    {
        $item_id = App::$input['item_id'];
        unset(App::$session['BUY'][$item_id]);
        return $this->actionIndex();
    }

    private function buttonClick(string $type) : void
    {
        foreach (App::$input['buy_cnt'] as $item_id => $item_cnt) {
            if (is_numeric($item_cnt) && $item_cnt > 0 && $item_cnt < 99) {
                App::$session['BUY'][$item_id]['count'] = $item_cnt;
            }
        }
        if ($type == 'request') {
            redirect(App::$SUBDIR . 'basket/request');
        }
    }

    public function actionIndex() : string
    {

        $this->title = 'Корзина';
        $this->breadcrumbs[] = ['title' => $this->title ];

        if (App::$input['button']) {
            $this->buttonClick(App::$input['button']);
        }

        if (!isset(App::$session['BUY']) || !is_array(App::$session['BUY']) ||  !count(App::$session['BUY'])) {
            return App::$message->get('notice', [], 'Корзина пуста !');
        }
        $where = '';
        $count = 0;
        foreach (App::$session["BUY"] as $item_id => $cnt) {
            $where.=(!strlen($where) ? " cat_item.id='$item_id'" : " or cat_item.id='$item_id'");
            $count = $count + (int)$cnt;
        }
        $query = "select cat_item.*,file_name,file_type,cat_item_images.id as cat_item_images_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where $where order by b_code,title asc";
        $result = App::$db->query($query);
        $summ = 0;
        $cnt = 0;
        while ($row = $result->fetch_array()) {
            $summ += $row['price'] * App::$session['BUY'][$row['id']]['count'];
            $cnt += App::$session['BUY'][$row['id']]['count'];
        }
        $result->data_seek(0);
        $tags['summ'] = add_zero($summ);
        $tags['discount'] = $this->getDiscount($summ);
        $tags['summ_with_discount'] = add_zero($this->calcDiscount($summ, $tags['discount']));
        $tags['summ_with_discount_str'] = SummToStr::get($this->calcDiscount($summ, $tags['discount']));

        $content = $this->render('basket_index.html.twig', $tags, $result);
        return $content;
    }
}
