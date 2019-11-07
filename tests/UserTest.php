<?php

use PHPUnit\Framework\TestCase;
use Classes\User;

class UserTest extends TestCase
{
    
    private $User;
    
    public function setUp() {
        parent::setUp();
        $this->User = new User;
    }
    
    public function tearDown() {
        unset($this->User);
    }            

    public function testAuthByIdFlags()
    {
        $this->User->authByIdFlags(1,'test');
        self::assertEquals(1, $this->User->id);
        self::assertEquals('test', $this->User->flags);
    }
    
    public function testAuthByArray()
    {
        $this->User->authByArray(['id'=>1,'flags'=>'test']);
        self::assertEquals('1', $this->User->id);
        self::assertEquals('test', $this->User->flags);
    }
    
    public function testAuthBySession()
    {
        $this->User->authBySession(['UID'=>1,'FLAGS'=>'test']);
        self::assertEquals('1', $this->User->id);
        self::assertEquals('test', $this->User->flags);
    }
    
    public function testLogout()
    {
        $this->User->authByIdFlags(1,'test');
        $this->User->logout();
        self::assertEquals(0, $this->User->id);
        self::assertEquals('', $this->User->flags);
    }
    
    public function testHaveFlag()
    {
        $this->User->authByIdFlags(1,'test');        
        self::assertTrue($this->User->haveFlag('test'));
    }
    
    public function testCheckAccess()
    {
        $this->User->authByIdFlags(1,'global');        
        self::assertTrue($this->User->checkAccess('admin'));
    }
    
    public function testGenerateSalt()
    {
        $salt = $this->User->generateSalt();
        self::assertEquals(22, strlen($salt));
    }
    
    public function testEncryptPassword()
    {
        $salt = $this->User->generateSalt();
        $password = $this->User->encryptPassword('test', $salt);
        $password2 = $this->User->encryptPassword('test', $salt);
        $password3 = $this->User->encryptPassword('test2', $salt);
        
        self::assertStringStartsWith('$2a$',$password);
        self::assertEquals($password, $password2);
        self::assertNotEquals($password, $password3);
    }
    
   
}

