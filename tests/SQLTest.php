<?php

use PHPUnit\Framework\TestCase;
use Classes\SQLHelper;

require_once 'tests/bootstrap.php';

class SQLTest extends TestCase
{
    public function testUsers()
    {
        $row=my_select_row("select login from users where login='boot'");
        self::assertEquals($row['login'], 'boot');
    }

    public function testDB()
    {
        global $DBHOST, $DBUSER, $DBPASSWD, $DBNAME;
        $DB = new SQLHelper($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
        $row=$DB->select_row("select login from users where login='boot'");
        self::assertEquals($row['login'], 'boot');
    }

    public function testInsert()
    {
        $insert=db_insert_fields(['id'=>'1']);
        self::assertEquals($insert, "(id) VALUES('1')");
    }
    
    public function testUpdate()
    {
        $insert=db_update_fields(['id'=>'1']);
        self::assertEquals($insert, "id='1'");
    }
    

}

