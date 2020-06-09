<?php

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    private $model;
    
    public function setUp()
    {
        parent::setUp();
        $this->model = new tests\TestModel;
    }

    public function testGetterAndSetter() {
        $this->model->name = 'Test 1';
        self::assertEquals('Test 1', $this->model->name);
        self::assertEquals('Test 1', $this->model['name']);
    }

    public function testLoad() {
        $this->model->load(['name' => 'Test 1']);
        self::assertEquals('Test 1', $this->model->name);
        self::assertEquals('Test 1', $this->model['name']);
    }
    
    public function testCheckRulesString() {
        $this->model->value = 5;

        $this->model->name = '';
        self::assertFalse($this->model->checkRules());
        $this->model->name = '0123456789';
        self::assertFalse($this->model->checkRules());
        $this->model->name = 'Test 1';
        self::assertTrue($this->model->checkRules());
    }
    
    public function testCheckRulesInteger() {
        $this->model->name = 'Test 1';

        $this->model->value = 0;
        self::assertFalse($this->model->checkRules());
        $this->model->value = 10;
        self::assertFalse($this->model->checkRules());
        $this->model->value = 5;
        self::assertTrue($this->model->checkRules());
    }
    
    public function testCheckRequired() {
        $this->model->value = 5;
        $this->model->name = '';
        self::assertFalse($this->model->checkRules());
        $this->model->value = 0;
        $this->model->name = 'Test 1';
        self::assertFalse($this->model->checkRules());
        $this->model->value = 5;
        $this->model->name = 'Test 1';
        self::assertTrue($this->model->checkRules());
    }
    
}
