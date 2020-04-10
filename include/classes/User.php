<?php

namespace Classes;

class User 
{
    /**
    * @var integer User id
    */
    private $id;
    /**
    * @var string User flags
    */
    private $flags;
    /**
    * @var array User data
    */
    private $data;
    
    /**
    * @const Use password hash to create token.
    */
    const TOKEN_PASSWORD_HASH = 0;

    /**
    * @const Use salt to create token.
    */
    const TOKEN_SALT = 1;

    /**
    * @const Remove token.
    */
    const TOKEN_NULL = 2;
    
    public function __construct() 
    {
        $this->id = 0;
        $this->flags = '';
        $this->data=null;
    }
    
    public function __get(string $name)
    {
        switch ($name) {
            case 'id':
                return $this->id;
            case 'flags':
                return $this->flags;
            default:
                if(!$this->data) {
                    $this->getData($this->id);
                }
                if(array_key_exists($name, $this->data)) {
                    return $this->data[$name];
                }
                throw new \InvalidArgumentException('Unknown property: ' . $name);
        }
    }


    /**
     * Get user's data
     *
     * @param integer $id User ID
     *
     * @return boolean False if ID not found
     */
    private function getData(int $id) : bool
    {
        $query = "select * from users where id='" . $id . "'";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            $this->data = $row = $result->fetch_array();
            return true;
        }
        return false;
    }
    
    /**
     * Load data from variables.
     *
     * @param int $id User ID
     * @param string $flags User flags
     *
     */
    public function authByIdFlags(int $id, string $flags) : bool
    {
        $this->id = $id;
        $this->flags = $flags;
        return true;
    }

    /**
     * Load data from array
     *
     * @param array $arr 
     *
     */
    public function authByArray(array $arr) : bool
    {
        return $this->authByIdFlags($arr['id'], $arr['flags']);
    }    

    /**
     * Load data from _SESSION.
     *
     * @param array $session
     *
     */
    public function authBySession(array $session) : bool
    {
        if(array_key_exists('UID', $session)) {
            if(!(int)$session['UID']>0) {
                return false;
            }
            $id = $session['UID'];
        } else {
            return false;
        }
        if(array_key_exists('FLAGS', $session)) {
            $flags = $session['FLAGS'];
        } else {
            return false;
        }
        App::debug('Auth by session');
        return $this->authByIdFlags($id, $flags);
    }    
    
    /**
     * Check user access
     *
     * @param string $login User login
     * @param string $password User password
     *
     * @return array|false False if auth failed
     */
    public function authByLoginPassword(string $login, string $password) 
    {
        $query = "select id,flags,passwd,salt from users where login='" . $login . "' and flags like '%active%'";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            $row = $result->fetch_array();        
            if(password_verify($password, $row['passwd'])) {
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
    public function logout() : void
    {
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
    public function haveFlag(string $flag) 
    {
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
    public function checkAccess(string $flag) : bool
    {
        return (!strlen($flag)) || ($this->haveFlag($flag)) || ($this->haveFlag('global'));
    }
    
    /**
     * Generate salt for user account
     *
     * @return string Generated salt
     */
    public function generateSalt() : string 
    {
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
    public function getSalt(int $uid) : string
    {
        list($salt) = App::$db->getRow("SELECT salt FROM users WHERE id='{$uid}'");
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
    public function encryptPassword(string $passwd, string $salt) : string 
    {
        if (mb_strlen($salt) === 22) {
            return crypt($passwd, '$2a$13$' . $salt);
        } else {
            return md5($passwd);
        }
    }
    
    
    /**
     * Generate token and put it to DB
     *
     * @param integer $user_id
     * @param integer $expire_days
     * @param string $type password_hash, salt or null
     *
     * @return string Generated token
     */
    public function makeToken(int $user_id, int $expire_days, int $type = 0) : string
    {
        $expire=time() + $expire_days*24*3600;
        switch ($type) {
            case static::TOKEN_SALT:
                $token=$this->generateSalt();
                break;
            case static::TOKEN_NULL:
                $token='';
                $expire=0;
                break;
            default:
                $token=$this->encryptPassword($this->generateSalt(), $this->generateSalt());
        }
        
        $query = "update users set token='" . $token . "', token_expire='.$expire.' where id='".$user_id."'";
        App::$db->query($query);
        return $token;
    }
    
    /**
     * Check token
     *
     * @param string $token
     *
     * @return false|array User data or false
     */
    public function checkToken(string $token) 
    {
        $query = "select id,flags,token_expire from users where token='" . $token . "'";
        $result = App::$db->query($query);
        if(!$result->num_rows) {
            return false;
        }
        $data=$result->fetch_array();
        if($data['token_expire'] > time()) {
            return $data;
        } else {
            $this->makeToken($data['id'], 0, static::TOKEN_NULL);
        }
    }
    
    /**
     * Generate RememberMe cookie and token.
     *
     */
    public function setRememberme(int $user_id, string $COOKIE_NAME) 
    {
        if(!$user_id || !$COOKIE_NAME) {
            return false;        
        }
        $expire=time()+31*24*3600;
        $token= $this->makeToken($user_id, 31);
        setcookie($COOKIE_NAME.'_REMEMBERME', $token, $expire, App::$SUBDIR);
    }

    /**
     * Return users id and flags if he have valid RememberMe cookie. 
     *
     * @return mixed Array if complete, false if error.
     */
    public function authByRememberme(string $COOKIE_NAME) : bool
    {
        if(!$value = filter_input(INPUT_COOKIE, $COOKIE_NAME.'_REMEMBERME')) {
            return false;
        }
        $token = App::$db->test_param($value);
        if($data = $this->checkToken($token)){
            App::debug('Auth by Rememberme cookie');
            list($_SESSION['UID'],$_SESSION['FLAGS']) = $data;
            return $this->authByIdFlags($data['id'],$data['flags']);
        }
        return false;
    }

    /**
     * Delete RememberMe cookie and token.
     *
     */
    public function delRememberme(int $user_id, string $COOKIE_NAME) : void
    {
        $value = filter_input(INPUT_COOKIE, $COOKIE_NAME.'_REMEMBERME');
        if(strlen($value)){
            setcookie($COOKIE_NAME.'_REMEMBERME', '', time(), App::$SUBDIR);
            $this->makeToken($user_id, 0, static::TOKEN_NULL);
        }
    }
    
}
