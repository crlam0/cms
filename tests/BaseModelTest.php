<?php

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    private $model;

    public function setUp() : void
    {
        parent::setUp();
        $this->model = new tests\TestModel;
    }

    public function testGetterAndSetter(): void
    {
        $this->model->name = 'Test 1';
        self::assertEquals('Test 1', $this->model->name);
        self::assertEquals('Test 1', $this->model['name']);
    }

    public function testLoad(): void
    {
        $this->model->load(['name' => 'Test 1']);
        self::assertEquals('Test 1', $this->model->name);
        self::assertEquals('Test 1', $this->model['name']);
    }

    public function testCheckRequired(): void
    {
        $this->model->value = 5;
        $this->model->name = null;
        self::assertFalse($this->model->validate());
        $this->model->value = null;
        $this->model->name = 'Test 1';
        self::assertFalse($this->model->validate());
        $this->model->value = 5;
        $this->model->name = 'Test 1';
        self::assertTrue($this->model->validate());
    }
    
    public function testCheckRulesString(): void
    {
        $this->model->value = 5;

        $this->model->name = '';
        self::assertFalse($this->model->validate());
        $this->model->name = '0123456789';
        self::assertFalse($this->model->validate());
        $this->model->name = 'Test 1';
        self::assertTrue($this->model->validate());
    }

    public function testCheckRulesInteger(): void
    {
        $this->model->name = 'Test 1';

        $this->model->value = 0;
        self::assertFalse($this->model->validate());
        $this->model->value = 10;
        self::assertFalse($this->model->validate());
        $this->model->value = 5;
        self::assertTrue($this->model->validate());
    }

}
