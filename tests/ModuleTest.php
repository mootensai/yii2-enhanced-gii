<?php
namespace inquid\tests;

use inquid\enhancedgii\BaseGenerator;
use inquid\enhancedgii\module\Generator;
use yii\gii\Generator as YiiBaseGenerator;
use yii\helpers\StringHelper;

/**
 * Class ModuleTest
 * @package inquid\tests
 */
class ModuleTest extends TestCase
{
    /** @var Generator $generator */
    public $generator;

    protected function setUp(): void
    {
        $this->generator = new Generator();
    }

    public function testValidGenerator(): void
    {
        $this->assertTrue($this->generator instanceof BaseGenerator);
        $this->assertTrue($this->generator instanceof YiiBaseGenerator);
    }

    /**
     * Test it can get the correct generator name
     */
    public function testGeneratorName(): void
    {
        $this->assertEquals('INQUID Generator (Module)',
            $this->generator->getName());
    }

    /**
     * Test it gets the correct generator Description
     */
    public function testGeneratorDescription(): void
    {
        $this->assertEquals('This generator helps you to generate
         the skeleton code needed by a Yii module.',
            $this->generator->getDescription());
    }

    /**
     * The Module ID must be a valid non space / special characters
     */
    public function testModuleIdValidation(): void
    {
        $this->generator->moduleID = 'Hello World Module';
        $this->assertFalse($this->generator->validate());
        $this->assertArrayHasKey('moduleID', $this->generator->getErrors());
        $this->assertEquals($this->generator->getErrors()['moduleID'][0],
            'Only word characters and dashes are allowed.');
    }

    /**
     * The ModuleClass must be a valid non space / special characters
     */
    public function testModuleClassValidation(): void
    {
        $this->generator->moduleClass = 'My Class';
        $this->assertFalse($this->generator->validate());
        $this->assertArrayHasKey('moduleClass', $this->generator->getErrors());
        $this->assertEquals($this->generator->getErrors()['moduleClass'][0],
            'Only word characters and backslashes are allowed.');
    }

    /**
     * Test generator db can NOT be blank
     */
    public function testDatabaseValidation(): void
    {
        $this->generator->db = null;
        $this->assertFalse($this->generator->validate());
        $this->assertArrayHasKey('db', $this->generator->getErrors());
        $this->assertEquals($this->generator->getErrors()['db'][0],
            'Database Connection ID cannot be blank.');
    }

    /**
     * Test generator db can NOT be blank
     */
    public function testHints(): void
    {
        $hints = $this->generator->hints();
        $this->assertNotEmpty($hints);
        $this->assertNotEmpty($hints['db']);
        $this->assertNotEmpty($hints['moduleID']);
        $this->assertNotEmpty($hints['moduleClass']);
    }



    /**
     * Test can get a valid DB connection
     */
    public function testDbConnection(): void
    {
        $this->mockWebApplication();
        $generator = new Generator();
        $generator->validateDb();

        $this->assertEmpty($generator->getErrors());
    }

    public function testGenerateModuleSuccessfully(){
        $this->mockWebApplication();
        $generator = new Generator();
        $generator->moduleClass = 'app\modules\testing\Test';
        $generator->moduleID = 'testingModuleId';

        $result = $generator->generate();
        $this->assertNotEmpty($result);
        $this->assertCount('5', $result);

        print_r($result);
        $this->assertEquals($generator->modulePath . '/' . StringHelper::basename($generator->moduleClass) . '.php', $result[0]->path);
    }
}
