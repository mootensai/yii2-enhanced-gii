<?php

class m180728_020101_service_columns extends \yii\db\Migration
{
    public $skippedTables = ['auth_assignment', 'auth_item', 'auth_item_child', 'auth_rule', 'migration', 'social_account', 'token', 'user'];

    public function up()
    {
        $tableOptions = null;

        foreach ($this->db->schema->tableNames as $tableName) {
            try {
                if (!in_array($tableName, $this->skippedTables)) {
                    $this->addColumn($tableName, 'created_at', 'datetime');
                    $this->addColumn($tableName, 'updated_at', 'datetime');
                    $this->addColumn($tableName, 'deleted_at', 'datetime');

                    $this->addColumn($tableName, 'created_by', 'integer');
                    $this->addColumn($tableName, 'updated_by', 'integer');
                    $this->addColumn($tableName, 'deleted_by', $this->integer()->defaultValue(0));
                }
            } catch (Exception $exception) {
                Yii::debug('Exception ->'.\yii\helpers\Json::encode($exception));
            }
        }
    }

    public function down()
    {
        $tableOptions = null;

        foreach ($this->db->schema->tableNames as $tableName) {
            try {
                if (!in_array($tableName, $this->skippedTables)) {
                    $this->dropColumn($tableName, 'created_at');
                    $this->dropColumn($tableName, 'updated_at');
                    $this->dropColumn($tableName, 'deleted_at');

                    $this->dropColumn($tableName, 'created_by');
                    $this->dropColumn($tableName, 'updated_by');
                    $this->dropColumn($tableName, 'deleted_by');
                }
            } catch (Exception $exception) {
                Yii::debug('Exception ->'.\yii\helpers\Json::encode($exception));
            }
        }
    }
}
