<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2/1/19
 * Time: 11:30 AM
 */

namespace inquid\enhancedgii\utils;
use yii\base\UserException;
use yii\helpers\Json;

class DatabaseUtils
{
    public $dbConnection = null;

    public function getDatabaseComment(string $database = null){
        if ($this->dbConnection === null) {
            throw new UserException("No databse connection set");
        }
        if ($database === null) {
            $database = DatabaseUtils::getDsnAttribute($this->dbConnection->dsn);
        }
        try {
            $result = Yii::$app->db->createCommand("SELECT comment FROM phpmyadmin.pma__column_info WHERE db_name='{$database}';")
                ->queryScalar();
            return ($result != null) ? $result : 'N/A';
        } catch (\yii\db\Exception $e) {
            return "ERROR " . Json::encode($e);
        }
    }

    public static function getDsnAttribute($dsn, $name = 'dbname')
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}