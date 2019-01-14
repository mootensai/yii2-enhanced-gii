<?php

namespace yiiunit\gii\generators;

use yii\db\ColumnSchema;
use yii\gii\generators\crud\Generator;
use yiiunit\gii\TestCase;

class CrudGeneratorTest extends TestCase
{
    public function testGenerateColumnFormat()
    {
        $g = new Generator();

        $c = new ColumnSchema(['phpType' => 'boolean', 'type' => 'boolean', 'name' => 'is_enabled']);
        $this->assertEquals('boolean', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'text', 'name' => 'description']);
        $this->assertEquals('ntext', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'integer', 'type' => 'integer', 'name' => 'create_time']);
        $this->assertEquals('datetime', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'email_address']);
        $this->assertEquals('email', $g->generateColumnFormat($c));

        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'email_address']);
        $this->assertEquals('email', $g->generateColumnFormat($c));

        // url type and false positive checks for URL
        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'hourly']);
        $this->assertEquals('text', $g->generateColumnFormat($c));
        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'some_hourly_check']);
        $this->assertEquals('text', $g->generateColumnFormat($c));
        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'some_url']);
        $this->assertEquals('url', $g->generateColumnFormat($c));
        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'my_url_string']);
        $this->assertEquals('url', $g->generateColumnFormat($c));
        $c = new ColumnSchema(['phpType' => 'string', 'type' => 'string', 'name' => 'url_lalala']);
        $this->assertEquals('url', $g->generateColumnFormat($c));
    }
}