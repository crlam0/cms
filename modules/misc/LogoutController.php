<?php

namespace modules\misc;

use classes\BaseController;
use classes\App;

class LogoutController extends BaseController
{    
    public function actionIndex(): void
    {
        global $_SESSION, $COOKIE_NAME;
        App::$user->delRememberme(App::$user->id,$COOKIE_NAME);
        App::$user->logout();
        $_SESSION['UID']=0;
        $_SESSION['FLAGS']='';
        redirect(App::$SUBDIR);    
    }
}

