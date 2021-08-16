<?php

use PHPUnit\Framework\TestCase;
use classes\DB;

class DBTest extends TestCase
{
    public function testUsers(): void
    {
        $row = classes\App::$db->getRow("select login from users where login='admin'");
        self::assertEquals($row['login'], 'admin');
    }

    public function testDB(): void
    {
        global $DBHOST, $DBUSER, $DBPASSWD, $DBNAME;
        $DB = new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
        $row = $DB->getRow("select login from users where login='admin'");
        self::assertEquals($row['login'], 'admin');
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
