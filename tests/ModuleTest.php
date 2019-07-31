<?php
namespace inquid\tests;

use inquid\enhancedgii\module\Generator;
use Yii;
use yii\base\Module;

/**
 * Class ModuleTest
 * @package inquid\tests
 */
class ModuleTest extends TestCase
{
    public function testModuleName(): void
    {
        $generator = new Generator();
        $this->assertEquals('INQUID Generator (Module)', $generator->getName());
    }

    public function testModuleVersion(): void
    {
        $generator = new Generator();
        $this->assertEquals('This generator helps you to generate the skeleton code needed by a Yii module.', $generator->getDescription());
    }

    public function testRules(): void
    {
        $generator = new Generator();
        $generator->moduleID = 'Hola Mundo';
        $this->assertFalse($generator->validate());
        $this->assertArrayHasKey('moduleID', $generator->getErrors());
    }

    public function testDbConnection(): void
    {
        $this->mockWebApplication();
        $generator = new Generator();
        $generator->validateDb();

        $this->assertEmpty($generator->getErrors());
    }
}
