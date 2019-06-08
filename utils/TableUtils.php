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
    /* @var $dbConnection */
    public $dbConnection = null;

    /**
     * @param string $database
     * @param string $table
     *
     * @throws \yii\db\Exception
     *
     * @return string
     */
    public function getTableComment(string $table, string $database = null)
    {
        if ($this->dbConnection === null) {
            throw new UserException('No databse connection set');
        }
        if ($database === null) {
            $database = DatabaseUtils::getDsnAttribute($this->dbConnection->dsn);
        }

        try {
            $result = Yii::$app->db->createCommand("SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='{$database}' AND table_name='{$table}';")
                ->queryScalar();

            return ($result != null) ? $result : 'N/A';
        } catch (\yii\db\Exception $e) {
            return 'ERROR '.Json::encode($e);
        }
    }
}
