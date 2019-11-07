<?php

namespace Classes;

class User {
    private $id;
    private $flags;
    
    public function __construct() {
        $this->id = 0;
        $this->flags = '';
    }
    
    public function __get($name){
        switch ($name) {
            case 'id':
                return $this->id;
            case 'flags':
                return $this->flags;
            default:
                throw new \InvalidArgumentException('Unknown property: ' . $name);
        }
    }


    /**
     * Load data from variables.
     *
     * @param int $id User ID
     * @param string $flags User flags
     *
     */
    public function authByIdFlags(int $id, string $flags) {
        $this->id = $id;
        $this->flags = $flags;
    }

    /**
     * Load data from array
     *
     * @param array $arr 
     *
     */
    public function authByArray(array $arr) {
        $this->id = $arr['id'];
        $this->flags = $arr['flags'];
    }    

    /**
     * Load data from _SESSION.
     *
     * @param array $session
     *
     */
    public function authBySession(array $session) {
        if(array_key_exists('UID', $session)) {
            $this->id = $session['UID'];
        }
        if(array_key_exists('FLAGS', $session)) {
            $this->flags = $session['FLAGS'];
        }
    }    
    
    /**
     * Check user access
     *
     * @param string $login User login
     * @param string $password User password
     *
     * @return array|false False if auth failed
     */
    public function authByLoginPassword(string $login, string $password) {
        $query = "select id,flags,passwd,salt from users where login='" . $login . "' and flags like '%active%'";
        $result = App::$db->query($query, true);
        if ($result->num_rows) {
            $row = $result->fetch_array();        
            if(strcmp($this->encryptPassword($password, $row['salt']),$row['passwd'])==0){
                $this->authByArray($row);
                return $row;
            }
        }
        return false;
    }    
    
    /**
     * Clear user data
     *
     */
    public function logout() {
        $this->id = 0;
        $this->flags = '';
    }

    /**
     * Check user access
     *
     * @param string $flag Flag title
     *
     * @return boolean true if user have flag
     */
    function haveFlag($flag) {
        if (!strlen($flag)) {
            return true;
        }
        return strpos($this->flags, $flag) > -1;
    }    
    
    /**
     * Check user access
     *
     * @param string $flag Flag title
     *
     * @return boolean true if user have access
     */
    function checkAccess($flag) {
        return (!strlen($flag)) || ($this->haveFlag($flag)) || ($this->haveFlag('global'));
    }
    
    /**
     * Generate salt for user account
     *
     * @return string Generated salt
     */
    function generateSalt() {
        $salt = '';
        for ($i = 0; $i < 22; $i++) {
            do {
                $chr = rand(48, 122);
            } while (in_array($chr, range(58, 64)) or in_array($chr, range(91, 96)));

            $salt .= chr($chr);
        }
        return $salt;
    }

    /**
     * Get salt for user account
     *
     * @param integer $uid User ID
     *
     * @return string User's salt
     */
    function getSalt($uid) {
        list($salt) = my_select_row("SELECT salt FROM users WHERE id='{$uid}'");
        return $salt;
    }

    /**
     * Encrypt input password with salt
     *
     * @param string $passwd Input password
     * @param string $salt Input salt
     *
     * @return string Generated hash
     */
    function encryptPassword($passwd, $salt) {
        if (mb_strlen($salt) === 22) {
            return crypt($passwd, '$2a$13$' . $salt);
        } else {
            return md5($passwd);
        }
    }
    
    /**
     * Generate RememberMe cookie and token.
     *
     */
    function setRememberme($user_id, $COOKIE_NAME) {
        if(!$user_id || !$COOKIE_NAME) {
            return false;        
        }    
        $token=$this->encryptPassword($this->generateSalt(), $this->generateSalt());
        $query = "update users set token='" . $token . "' where id='".$user_id."'";
        App::$db->query($query);
        setcookie($COOKIE_NAME.'_REMEMBERME', $token, time()+31*24*3600, App::$SUBDIR);
    }

    /**
     * Return users id and flags if he have valid RememberMe cookie. 
     *
     * @return mixed Array if complete, false if error.
     */
    function getRememberme($COOKIE_NAME) {
        $value = filter_input(INPUT_COOKIE, $COOKIE_NAME.'_REMEMBERME');
        if(strlen($value)){
            $token = App::$db->test_param($value);
            $query = "select id,flags from users where token='" . $token . "'";
            $result = App::$db->query($query);
            if($result->num_rows) {
                $row=$result->fetch_array();
                return [$row['id'],$row['flags']];
            }
        }
        return false;
    }

    /**
     * Delete RememberMe cookie and token.
     *
     */
    function delRememberme($user_id, $COOKIE_NAME) {
        $value = filter_input(INPUT_COOKIE, $COOKIE_NAME.'_REMEMBERME');
        if(strlen($value)){
            setcookie($COOKIE_NAME.'_REMEMBERME', '', time(), App::$SUBDIR);
            $query = "update users set token='' where id='".$user_id."'";
            App::$db->query($query);
        }
    }
    
}
