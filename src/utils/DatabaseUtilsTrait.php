<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2019-08-01
 * Time: 23:25.
 */

namespace inquid\enhancedgii\utils;

use yii\base\UserException;

/**
 * Trait DatabaseUtilsTrait.
 */
trait DatabaseUtilsTrait
{
    /**
     * @throws UserException
     */
    protected function validateDbConnection(): void
    {
        if ($this->dbConnection === null) {
            throw new UserException('No database connection set');
        }
    }

    /**
     * @param string $database
     *
     * @return array|null
     */
    protected function getDatabase(string $database): ?string
    {
        if ($database === null) {
            return DatabaseUtils::getDsnAttribute($this->dbConnection->dsn);
        }

        return $database;
    }

    /**
     * @param string|null $databaseName
     *
     * @return string
     */
    protected function getTableName($databaseName = null): string
    {
        return $databaseName ?? self::getDsnAttribute($this->dbConnection->dsn);
    }

    /**
     * @param $dsn
     * @param string $name
     *
     * @return string|null
     */
    public static function getDsnAttribute($dsn, $name = 'dbname'): ?string
    {
        return preg_match('/'.$name.'=([^;]*)/', $dsn, $match) ?
            $match[1] : null;
    }
}
