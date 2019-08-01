<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2019-07-31
 * Time: 22:30
 */

namespace inquid\tests;


use inquid\enhancedgii\utils\DatabaseUtils;

class DatabaseUtilsTest extends TestCase
{
    /** @var DatabaseUtils $databaseUtils */
    protected $databaseUtils;

    protected function setUp(): void
    {
        $this->databaseUtils = new DatabaseUtils();
    }

    public function testGetDatabaseName()
    {
        $this->databaseUtils = '';
    }

    public function testGetDsnAttribute()
    {

    }
}