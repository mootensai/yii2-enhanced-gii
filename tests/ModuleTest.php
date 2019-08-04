<?php declare(strict_types=1);

namespace inquid\tests;

use inquid\enhancedgii\BaseGenerator;
use inquid\enhancedgii\module\Generator;
use yii\base\UserException;
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
    public $generatedFiles;
    public $modulePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new Generator();
        $this->generatedFiles = $this->generateFiles();
    }

    /**
     * @return array
     */
    protected function generateFiles(): array {
        $this->mockWebApplication();
        $this->modulePath = 'myModule';
        $this->generator->moduleClass = "app\\modules\\{$this->modulePath}\\MyCustomClass1";
        $this->generator->moduleID = 'myCustomModuleId';
        return $this->generator->generate();
    }

    public function testValidGenerator(): void
    {
        $this->assertInstanceOf(BaseGenerator::class, $this->generator);
        $this->assertInstanceOf(YiiBaseGenerator::class, $this->generator);
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
        $this->assertEquals('This generator helps you to generate'.
         ' the skeleton code needed by a Yii module.',
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
        $this->generator->validateDb();

        $this->assertEmpty($this->generator->getErrors());
    }

    public function testFailsWhenNoValidDatabaseGiven(): void
    {
        $this->expectException(UserException::class);
        try {
            $this->mockWebApplicationInvalid();
            $this->generator->generate();
        } catch (UserException $userException) {
            throw $userException;
        }
    }

    public function testGenerateModuleSuccessfully(): void
    {
        $this->setUp();
        $this->mockWebApplication();
        $this->generator->generate();

        $this->assertNotEmpty($this->generateFiles());
        $this->assertCount(5, $this->generateFiles());

        $this->assertEquals(
            $this->generator->modulePath .
            '/' .
            StringHelper::basename($this->generator->moduleClass) .
            '.php', $this->generateFiles()[0]->path);

        $this->assertEquals(
            $this->generator->modulePath .
            '/' .
            StringHelper::dirname($this->generator->baseModelClass)
            .'controllers/DefaultController.php',
            $this->generateFiles()[1]->path);

        $this->assertEquals(
            $this->generator->modulePath .
            '/' .
            StringHelper::dirname($this->generator->baseModelClass)
            .'views/default/index.php',
            $this->generateFiles()[2]->path);

        $this->assertEquals(
            $this->generator->modulePath .
            '/' .
            StringHelper::dirname($this->generator->baseModelClass)
            .'menu_items.php',
            $this->generateFiles()[3]->path);

        $this->assertEquals(
            $this->generator->modulePath .
            '/' .
            StringHelper::dirname($this->generator->baseModelClass)
            .'config/config.php',
            $this->generateFiles()[4]->path);
    }

    public function testContentGeneratedCorrectly(): void
    {
        $this->setUp();
        $this->mockWebApplication();
        $this->generator->generate();

        $this->assertNotEmpty($this->generateFiles());
        $this->assertCount(5, $this->generateFiles());

        $this->assertEquals(
            "<?php

namespace app\\modules\\".$this->modulePath.";

use Yii;

/**
 * {$this->generator->moduleID} module definition class
 */
class ".StringHelper::basename($this->generator->moduleClass)." extends \\yii\\base\\Module
{
    public \$menu = [];

    /**
     * {@inheritdoc}
     */
    public \$controllerNamespace = 'app\\modules\\".$this->modulePath."\\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        Yii::configure(\$this, require(__DIR__ . '/config/config.php'));
        if (isset(Yii::\$app->user->identity))
            \$this->menu = ['label' => 'Opciones',
                'visible' => !Yii::\$app->user->isGuest && Yii::\$app->user->identity->isAdmin,
                'url' => ['/{$this->generator->moduleID}/default/index'],
                'template' => '<a href=\"{url}\">{label}<i class=\"fa fa-angle-left pull-right\"></i></a>',
                'items' => 
                    include('menu_items.php')                ,
            ];
        // custom initialization code goes here
    }
}
",
            $this->generateFiles()[0]->content
        );

        $this->assertEquals(
            "<?php

namespace app\\modules\\".$this->modulePath."\\controllers;

use yii\\web\\Controller;

/**
 * Default controller for the `{$this->generator->moduleID}` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return \$this->render('index');
    }
}
",
            $this->generateFiles()[1]->content
        );

        $this->assertEquals(
            "<?php 
\$this->title = '{$this->generator->moduleID}';
?><div class=\"{$this->generator->moduleID}-default-index\">
    <h1>Inquid</h1>
    <p>
        <?= \$this->title ?>    </p>
</div>
",
            $this->generateFiles()[2]->content);

        $this->assertEquals(
            "<?php
/**
 * List all the controllers and actions you need in your module
 */
return [
  ['label' => 'Index', 'url' => [\"/{$this->generator->moduleID}/index\"], 'visible' => Yii::\$app->user->identity->isAdmin]
];",
            $this->generateFiles()[3]->content);

        $this->assertEquals(
            '<?php
return [];
',
            $this->generateFiles()[4]->content);

        print_r($this->generateFiles());
    }
}
