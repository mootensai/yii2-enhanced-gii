<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2/1/19
 * Time: 11:30 AM.
 */

namespace inquid\enhancedgii\utils;

use Yii;
use yii\base\UserException;
use yii\db\Exception;

/**
 * Class DatabaseUtils.
 */
class DatabaseUtils
{
    use DatabaseUtilsTrait;

    public $dbConnection;

    /**
     * Get the database comment or database name.
     *
     * @param string|null $databaseName
     *
     * @throws UserException
     *
     * @return false|string|null
     */
    public function getDatabaseName($databaseName = null)
    {
        $this->validateDbConnection();

        $databaseName = $this->getTableName($databaseName);

        try {
            $result = Yii::$app->db->createCommand(
                "SELECT comment
                     FROM phpmyadmin.pma__column_info
                     WHERE db_name='{$databaseName}';")
                ->queryScalar();

            $result = $result ?? 'N/A';
        } catch (Exception $e) {
            $result = '';
            //throw new UserException("Database Error {$e->getMessage()}");
            // Do not throw an exception if the phpMyAdmin database is not present
            // refer to https://github.com/inquid/yii2-enhanced-gii/issues/25
        }
        return $result;
    }
}
