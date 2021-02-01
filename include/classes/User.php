<?php

namespace classes;

/**
 * This is the model class for user.
 *
 * @property integer $id
 * @property string $login
 * @property string $fullname
 *
 */




class User extends BaseModel
{
    
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
    
    private $data_loaded;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }
    
    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'login',
            'passwd',
            'salt',
            'email',
            'fullname',
            'regdate',
            'flags',
            'token',
            'token_expire',
            'avatar',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'passwd'], 'required'],
            [['login'], 'string', ['min' => 1, 'max' => 64]],
            [['salt', 'email'], 'string', ['min' => 0, 'max' => 32]],
            [['passwd'], 'string', ['min' => 8, 'max' => 64]],
            [['fullname', 'flags', 'token'], 'string', ['min' => 0, 'max' => 255]],
            [['id', 'token_expire'], 'integer'],
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
            'passwd' => 'Пароль',
            'email' => 'E-Mail',
            'fullname' => 'Полное имя',
        ];
    }
    
    public function __construct($id = null, $flags = '') 
    {
        if($id !== null) {
            parent::__construct($id);
            $this->data_loaded = true;
        } else {
            parent::__construct();
            $this->id = 0;
            $this->flags = $flags;
            $this->data_loaded = false;
        }
    }
    
    public function __get(string $name)
    {
        switch ($name) {
            case 'id':
                return parent::__get('id');
            case 'flags':
                return parent::__get('flags');
            default:
                if(!$this->data_loaded) {
                    $this->loadFromDb($this->id);
                    $this->data_loaded = true;
                }
                return parent::__get($name);
        }
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
        $row = App::$db->getRow("select id,flags,passwd,salt from users where login=? and flags like '%active%'", ['login' => $login]);
        if ($row) {
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
     * Return user's flags as array
     *
     * @return array
     */
    public function getFlagsAsArray() 
    {
        if(strlen($this->flags) > 0) {
            return explode(';', $this->flags);            
        } else {
            return [];
        }
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
        // echo $salt;
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
        // list($salt) = App::$db->getRow("SELECT salt FROM users WHERE id='{$uid}'");
        // return $salt;
        return $this->salt;
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
     * @param integer $expire_days
     * @param int $type
     *
     * @return string Generated token
     */
    public function makeToken(int $expire_days, int $type = 0) : string
    {
        if(!$this->id){
            return null;
        }
        $token_expire = time() + $expire_days*24*3600;
        switch ($type) {
            case static::TOKEN_SALT:
                $token = $this->generateSalt();
                break;
            case static::TOKEN_NULL:
                $token = '';
                $token_expire = 0;
                break;
            default:
                $token = $this->encryptPassword($this->generateSalt(), $this->generateSalt());
        }
        App::$db->updateTable($this::tableName(), ['token'=>$token, 'token_expire'=>$token_expire], ['id'=>$this->id]);
        return $this->token;
    }
    
    /**
     * Check token
     *
     * @param string $token
     *
     * @return array|false|null User data or false
     */
    public function checkToken(string $token) 
    {
        $data = App::$db->getRow('select id,flags,token_expire from users where token=?', ['token' => $token]);
        if(!$data) {
            return false;
        }
        if($data['token_expire'] > time()) {
            return $data;
        } else {
            $this->makeToken($data['id'], 0, static::TOKEN_NULL);
        }
    }
    
    /**
     * Generate RememberMe cookie and token.
     *
     * @return false|null
     */
    public function setRememberme(string $COOKIE_NAME) 
    {
        if(!$this->id || !$COOKIE_NAME) {
            return false;        
        }
        $expire = time()+31*24*3600;
        $token = $this->makeToken(31);
        setcookie($COOKIE_NAME.'_REMEMBERME', $token, $expire, App::$SUBDIR);
    }

    /**
     * Return users id and flags if he have valid RememberMe cookie.
     *
     * @return bool
     */
    public function authByRememberme(string $COOKIE_NAME) : bool
    {
        if(!$value = filter_input(INPUT_COOKIE, $COOKIE_NAME.'_REMEMBERME')) {
            return false;
        }
        $token = App::$db->testParam($value);
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
    public function delRememberme(string $COOKIE_NAME) : void
    {
        $value = filter_input(INPUT_COOKIE, $COOKIE_NAME.'_REMEMBERME');
        if(strlen($value)){
            setcookie($COOKIE_NAME.'_REMEMBERME', '', time(), App::$SUBDIR);
            $this->makeToken(0, static::TOKEN_NULL);
        }
    }
    
}
