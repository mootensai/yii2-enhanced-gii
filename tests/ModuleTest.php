<?php
namespace inquid\tests;

use inquid\enhancedgii\module\Generator;

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

    public function testDbConnection(): void
    {
        $generator = new Generator();
        $generator->validateDb();
        $this->assertCount(0, $generator->getErrors());
    }
}
