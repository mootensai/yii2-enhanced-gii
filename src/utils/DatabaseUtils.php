<?php declare(strict_types=1);
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
 * Class DatabaseUtils
 * @package inquid\enhancedgii\utils
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
            return $result ?? 'N/A';
        } catch (Exception $e) {
            throw new UserException("Database Error {$e->getMessage()}");
        }
    }
}
