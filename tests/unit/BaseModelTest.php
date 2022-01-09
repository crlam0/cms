<?php

use classes\App;

class ModelTest extends \Codeception\Test\Unit
{

    public function setUp() : void
    {
        parent::setUp();
    }

    public function tearDown() : void
    {
        App::$db->deleteFromTable('test', ['1' => '1']);
        parent::tearDown();
    }


    public function testGetterAndSetter(): void
    {
        $model = new tests\unit\TestModel;
        $model->name = 'Test 1';
        self::assertEquals('Test 1', $model->name);
        self::assertEquals('Test 1', $model['name']);
    }

    public function testLoad(): void
    {
        $model = new tests\unit\TestModel;
        $model->load(['name' => 'Test 2']);
        self::assertEquals('Test 2', $model->name);
        self::assertEquals('Test 2', $model['name']);
    }

    public function testCheckRequired(): void
    {
        $model = new tests\unit\TestModel;
        $model->value = 5;
        $model->name = null;
        self::assertFalse($model->validate());
        $model->value = null;
        $model->name = 'Test 3';
        self::assertFalse($model->validate());
        $model->value = 5;
        $model->name = 'Test 4';
        self::assertTrue($model->validate());
    }

    public function testCheckRulesString(): void
    {
        $model = new tests\unit\TestModel;
        $model->value = 5;
        $model->name = '';
        self::assertFalse($model->validate());
        $model->name = '0123456789';
        self::assertFalse($model->validate());
        $model->name = 'Test 5';
        self::assertTrue($model->validate());
    }

    public function testCheckRulesInteger(): void
    {
        $model = new tests\unit\TestModel;
        $model->name = 'Test 6';
        $model->value = 0;
        self::assertFalse($model->validate());
        $model->value = 10;
        self::assertFalse($model->validate());
        $model->value = 5;
        self::assertTrue($model->validate());
    }

    public function testSave(): void
    {
        $model = new tests\unit\TestModel;
        $model->name = 'Test 7';
        $model->value = 2;
        $model->save();
        $id = $model->id;
        self::assertIsInt($id);
        $test_model = new tests\unit\TestModel($id);
        self::assertNotNull($test_model);
        self::assertEquals($id, $test_model->id);
        self::assertEquals($model->name, $test_model->name);
        self::assertEquals($model->value, $test_model->value);
        $model->delete();
    }


    public function testGetOne(): void
    {
        $model = new tests\unit\TestModel;
        $model->name = 'Test 8';
        $model->value = 3;
        $model->save();
        $test_model = $model::getOne(['name' => $model->name]);
        self::assertIsInt($test_model->id);
        self::assertEquals($model->id, $test_model->id);
        $model->delete();
    }

    public function testGetById(): void
    {
        $model = new tests\unit\TestModel;
        $model->name = 'Test 9';
        $model->value = 4;
        $model->save();
        $id = $model->id;
        self::assertIsInt($id);
        $test_model = $model::getById($id);
        self::assertIsInt($test_model->id);
        self::assertEquals($id, $test_model->id);
        $model->delete();
    }

    public function testGelAll(): void
    {
        $model = new tests\unit\TestModel;
        $model->name = 'Test 10';
        $model->value = 5;
        $model->save();
        $models = $model::getAll();
        self::assertEquals($model->id, $models[0]['id']);
        $model->delete();
    }
}
