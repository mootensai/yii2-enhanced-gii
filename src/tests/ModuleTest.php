<?php

namespace yiiunit\gii;

use inquid\enhancedgii\module\Generator;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleTest
 * @package yiiunit\gii
 */
class ModuleTest extends TestCase
{
    public function autoload()
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        require_once __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
    }

    public function testModuleName(): void
    {
        $this->autoload();
        $generator = new Generator();
        $this->assertEquals('INQUID Generator (Module)', $generator->getName());
    }

    public function testDbConnection(): void
    {
        $this->autoload();
        $generator = new Generator();
        $generator->validateDb();
        $this->assertCount(0, $generator->getErrors());
    }
}
