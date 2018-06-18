<?php

use PHPUnit\Framework\TestCase;

require_once 'tests/misc.php';

class SQLTest extends TestCase
{
    public function testUsers()
    {
        $row=my_select_row("select id from users where login='boot'");
        self::assertEquals($row['id'], '7');
    }

    public function testDB()            
    {
        global $DB;
        $row=$DB->select_row("select id from users where login='boot'");
        self::assertEquals($row['id'], '7');
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

