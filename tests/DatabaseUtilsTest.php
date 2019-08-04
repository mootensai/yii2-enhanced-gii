<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2019-07-31
 * Time: 22:30
 */

namespace inquid\tests;


use inquid\enhancedgii\utils\DatabaseUtils;
use yii\base\UserException;

class DatabaseUtilsTest extends TestCase
{
    /** @var DatabaseUtils $databaseUtils */
    protected $databaseUtils;

    protected function setUp(): void
    {
        $this->databaseUtils = new DatabaseUtils();
    }

    /**
     * @throws UserException
     */
    public function testGetDatabaseName(): void
    {
        $this->databaseUtils = '';
    }

    public function testGetDsnAttribute(): void
    {

    }

    public function testValidateException(): void
    {
        $this->expectException(UserException::class);
        try{
            $this->databaseUtils->dbConnection = null;
            $this->databaseUtils->getDatabaseName();
        }catch (UserException $userException){
            throw $userException;
        }
    }
}