<?php

namespace modules\users\controllers;

use classes\BaseController;
use classes\App;

class LogoutController extends BaseController
{

    public function actionIndex(): void
    {
        global $COOKIE_NAME;
        App::$user->delRememberme($COOKIE_NAME);
        App::$user->logout();
        App::$session['UID']=0;
        App::$session['FLAGS']='';
        redirect(App::$SUBDIR);
    }
}
