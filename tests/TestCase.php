<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2019-07-06
 * Time: 01:44
 */

namespace inquid\tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase as PHPUnitBaseTestCase;
use Yii;
use yii\db\Schema;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends PHPUnitBaseTestCase
{
    /** @var array */
    protected $env;

    protected function setUp(): void
    {
        $this->env = Dotenv::create(__DIR__)->load();
        parent::setUp();
    }

    /**
     * Clean up after test.yml.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->destroyApplication();
    }

    public function generateTestDatabase(): void
    {
        $query = /** @lang mysql */
            'CREATE DATABASE ';
        Yii::$app->my_db->createCommand($query)
            ->bindValue(':ID', 1)
            ->queryOne();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
        ], $config));
    }

    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => "mysql:host=db;dbname=generator",
                    'username' => $this->env['DB_USERNAME'],
                    'password' => $this->env['DB_PASSWORD'],
                    'charset' => 'utf8',
                ],
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
        ], $config));
    }

    /**
     * @param array $config
     * @param string $appClass
     * @return Application
     */
    protected function mockWebApplicationInvalid($config = [], $appClass = '\yii\web\Application'): Application
    {
        return new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'mysql:host=invalidhost;dbname=invaliddtabase',
                    'username' => 'root',
                    'password' => '123456',
                    'charset' => 'utf8',
                ],
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
        ], $config));
    }

    /**
     *
     */
    protected function createTestDatabase(): void
    {
        Yii::$app->db->createCommand()->createTable(
            'inquid_params',
            [
                'id' => Schema::TYPE_PK,
                'key_param' => Schema::TYPE_STRING,
                'value_param' => Schema::TYPE_TEXT
            ]
        )
            ->addUnique('name_uq', 'inquid_params', 'key_param')
            ->insert('inquid_params', [
                'key_param' => 'database_nickname',
                'value_param' => 'Inquid Test Database',
            ]);
    }

    /**
     *
     */
    protected function destroyDatabase(): void
    {

    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication(): void
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }
}