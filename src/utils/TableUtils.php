<?php

/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace inquid\enhancedgii\utils;

use Yii;
use yii\base\UserException;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * Utils for Table databases.
 *
 * @author Inquid Inc <contact@inquid.co>
 *
 * @since 2.0
 */
class TableUtils
{
    use DatabaseUtilsTrait;

    /* @var $dbConnection */
    public $dbConnection;

    /**
     * @param string $table
     * @param string $database
     *
     * @throws UserException
     *
     * @return string
     */
    public function getTableComment(string $table, string $database = null): ?string
    {
        $this->validateDbConnection();

        $this->getDatabase($database);

        try {
            $result = Yii::$app->db->createCommand(
                "SELECT table_comment
                     FROM INFORMATION_SCHEMA.TABLES
                     WHERE table_schema='{$database}'
                     AND table_name='{$table}';"
            )
                ->queryScalar();

            return $result ?? 'N/A';
        } catch (Exception $e) {
            return 'ERROR '.Json::encode($e);
        }
    }
}
