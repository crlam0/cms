<?php

use PHPUnit\Framework\TestCase;
use classes\User;

class UserTest extends TestCase
{

    private $User;

    public function setUp() : void
    {
        parent::setUp();
        $this->User = new User;
    }

    public function tearDown() : void
    {
        unset($this->User);
    }

    public function testAuthByIdFlags() : void
    {
        $this->User->authByIdFlags(10, 'active');
        self::assertEquals(10, $this->User->id);
        self::assertStringContainsString('active', $this->User->flags);
    }

    public function testAuthByArray() : void
    {
        $this->User->authByArray(['id'=>10, 'flags'=>'active']);
        self::assertEquals('10', $this->User->id);
        self::assertStringContainsString('active', $this->User->flags);
    }

    public function testAuthBySession() : void
    {
        $this->User->authBySession(['UID'=>10, 'FLAGS'=>'active']);
        self::assertEquals('10', $this->User->id);
        self::assertStringContainsString('active', $this->User->flags);
    }

    public function testLogout() : void
    {
        $this->User->authByIdFlags(10, 'active');
        $this->User->logout();
        self::assertEquals(00, $this->User->id);
        self::assertEquals('', $this->User->flags);
    }

    public function testHaveFlag() : void
    {
        $this->User->authByIdFlags(10, 'test');
        self::assertTrue($this->User->haveFlag('test'));
    }

    public function testCheckAccess() : void
    {
        $this->User->authByIdFlags(10, 'global');
        self::assertTrue($this->User->checkAccess('admin'));
    }

    public function testGenerateSalt() : void
    {
        $salt = $this->User->generateSalt();
        self::assertEquals(22, strlen($salt));
    }

    public function testEncryptPassword() : void
    {
        $salt = $this->User->generateSalt();
        $password = $this->User->encryptPassword('test', $salt);
        $password2 = $this->User->encryptPassword('test', $salt);
        $password3 = $this->User->encryptPassword('test2', $salt);

        self::assertStringStartsWith('$2a$', $password);
        self::assertEquals($password, $password2);
        self::assertNotEquals($password, $password3);
    }
}
