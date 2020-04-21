<?php

use PHPUnit\Framework\TestCase;
use classes\DB;

class DBTest extends TestCase
{
    public function testUsers()
    {
        $row = classes\App::$db->getRow("select login from users where login='boot'");
        self::assertEquals($row['login'], 'boot');
    }

    public function testDB()
    {
        global $DBHOST, $DBUSER, $DBPASSWD, $DBNAME;
        $DB = new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
        $row = $DB->getRow("select login from users where login='boot'");
        self::assertEquals($row['login'], 'boot');
    }

    public function testInsert()
    {
        $insert = classes\App::$db->insert_fields(['id'=>'1']);
        self::assertEquals($insert, "(id) VALUES('1')");
    }
    
    public function testUpdate()
    {
        $insert = classes\App::$db->update_fields(['id'=>'1']);
        self::assertEquals($insert, "id='1'");
    }
    

}

