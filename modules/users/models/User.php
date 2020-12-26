<?php

namespace modules\users\models;

use classes\App;
use classes\User as ClassesUser;

/**
 * Model for table users.
 *
 * @author BooT
 */
class User extends ClassesUser {
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['login'], 'string', ['min' => 1, 'max' => 64]],
            [['salt', 'email'], 'string', ['min' => 0, 'max' => 32]],
            [['passwd'], 'string', ['min' => 0, 'max' => 64]],
            [['fullname', 'flags', 'token'], 'string', ['min' => 0, 'max' => 255]],
            [['id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'passwd' => 'Новый пароль',
            'email' => 'E-Mail',
            'fullname' => 'Полное имя',
            'avatar' => 'Аватар',
        ];
    }

    
    public function findByEmail($email) {
        $row = App::$db->getRow("select id,flags from users where email=? and flags like '%active%'", ['email' => $email]);
        if($row) {
            $this->authByArray($row);
        }
    }    

    public function findByToken($token) {
        $row = App::$db->getRow("select id,flags from users where token=? and flags like '%active%'", ['token' => $token]);
        if($row) {
            $this->authByArray($row);
        }
    }   
    
    /**
     * Add flag
     *
     * @param string $flag Flag title
     *
     * @return boolean true if complete
     */
    public function addFlag(string $flag) 
    {
        if (!strlen($flag) || $this->haveFlag($flag)) {
            return false;
        }
        $array = $this->getFlagsAsArray();
        $array[] = $flag;
        $this->flags=implode(';', $array);
        return true;
    }    
    
    /**
     * Remove flag
     *
     * @param string $flag Flag title
     *
     * @return boolean true if complete
     */
    public function delFlag(string $flag) 
    {
        if (!strlen($flag) || !$this->haveFlag($flag)) {
            return false;
        }
        $array = $this->getFlagsAsArray();
        array_splice($array, array_search($flag, $array ), 1);
        $this->flags=implode(';', $array);
        return true;
    }        
    
}
