<?php

use classes\DB;

class DBTest extends \Codeception\Test\Unit
{
    public function testUsers(): void
    {
        $row = classes\App::$db->getRow("select login from users where login='boot'");
        self::assertEquals($row['login'], 'boot');
    }

    public function testDB(): void
    {
        require dirname(__FILE__) . '/../../local/config.php';
        $DB = new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
        $row = $DB->getRow("select login from users where login='boot'");
        self::assertEquals($row['login'], 'boot');
    }

    public function testInsert(): void
    {
        $insert = classes\App::$db->insertFields(['id'=>'1']);
        self::assertEquals($insert, "(id) VALUES('1')");
    }

    public function testUpdate(): void
    {
        $insert = classes\App::$db->updateFields(['id'=>'1']);
        self::assertEquals($insert, "id='1'");
    }
}
