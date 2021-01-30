<?php

namespace modules\users;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void 
    {
        App::$template->addPath('modules/users/views');
            
        App::$routing->addRoutes([
            'login' => [
                'pattern' => '^login\/?$',
                'controller' => 'modules\users\controllers\LoginController'
            ],    
            'logout' => [
                'pattern' => '^logout\/?$',
                'controller' => 'modules\users\controllers\LogoutController'
            ],    
            'passwd-change' => [
                'pattern' => '^passwd-change\/?$',
                'controller' => 'modules\users\controllers\PasswdChangeController'
            ],    
            'passwd-recovery' => [
                'pattern' => '^passwd-recovery\/?[\w\-]*$',
                'controller' => 'modules\users\controllers\PasswdRecoveryController'
            ],    
            'signup' => [
                'pattern' => '^signup\/?[\w\-]*$',
                'controller' => 'modules\users\controllers\SignupController'
            ],    
            'profile' => [
                'pattern' => '^profile\/?[\w\-]*$',
                'controller' => 'modules\users\controllers\ProfileController'
            ],    

            /* For admin module */
            
            'users-edit' => [
                'pattern' => '^admin\/users\-edit\/[\w\-]*$',
                'controller' => 'modules\users\controllers\EditController',
            ],  
        ]);
    }
}
