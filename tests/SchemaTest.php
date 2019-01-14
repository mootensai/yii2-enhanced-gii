<?php

namespace yiiunit\gii;

use yii\gii\generators\model\Generator as ModelGenerator;
use Yii;

/**
 * SchemaTest checks that Gii model generator supports multiple schemas
 * @group gii
 * @group pgsql
 */
class SchemaTest extends GiiTestCase
{
    protected $driverName = 'pgsql';

    public function testPrefixesGenerator()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = 'schema1.*';
        $generator->generateRelationsFromCurrentSchema = false;

        $files = $generator->generate();

        if (version_compare(str_replace('-dev', '', Yii::getVersion()), '2.0.4', '<')) {
            $this->markTestSkipped('This feature is only available since Yii 2.0.4.');
        }

        $this->assertEquals(5, count($files));
        $this->assertEquals("Schema1Table1", basename($files[3]->path, '.php'));
        $this->assertEquals("Schema1Table2", basename($files[4]->path, '.php'));
    }

    public function relationsProvider()
    {
        return [
            ['default', 'schema1.*', 5, [
                0 => [ // relations from junction1 table
                    "\$this->hasOne(Schema1Table1::className(), ['id' => 'table1_id']);",
                    "\$this->hasOne(Schema1MultiPk::className(), ['id1' => 'multi_pk_id1', 'id2' => 'multi_pk_id2']);",
                ],
                2 => [ // relations from multi_pk table
                    "\$this->hasMany(Schema1Table1::className(), ['id' => 'table1_id'])->viaTable('junction1', ['multi_pk_id1' => 'id1', 'multi_pk_id2' => 'id2']);",
                    "\$this->hasMany(Schema1Junction1::className(), ['multi_pk_id1' => 'id1', 'multi_pk_id2' => 'id2']);",
                ],
                3 => [ // relations from table1 table
                    "\$this->hasMany(Schema2Table1::className(), ['fk1' => 'fk2', 'fk2' => 'fk1']);",
                    "\$this->hasMany(Schema2Table1::className(), ['fk3' => 'fk4', 'fk4' => 'fk3']);",
                    "\$this->hasOne(Schema2Table2::className(), ['fk1' => 'fk1', 'fk2' => 'fk2']);",
                    "\$this->hasMany(Schema1MultiPk::className(), ['id1' => 'multi_pk_id1', 'id2' => 'multi_pk_id2'])->viaTable('junction1', ['table1_id' => 'id']);",
                    "\$this->hasMany(Schema1Junction1::className(), ['table1_id' => 'id']);",
                ],
            ]],
            ['default', 'schema2.*', 2, [
                0 => [
                    "\$this->hasOne(Schema1Table1::className(), ['fk2' => 'fk1', 'fk1' => 'fk2']);",
                    "\$this->hasOne(Schema1Table1::className(), ['fk4' => 'fk3', 'fk3' => 'fk4']);",
                    "\$this->hasOne(Schema2Table2::className(), ['fk5' => 'fk5', 'fk6' => 'fk6']);",
                ],
            ]],
        ];
    }

    /**
     * @dataProvider relationsProvider
     */
    public function testRelationsGenerator($template, $tableName, $filesCount, $relationSets)
    {
        $generator = new ModelGenerator();
        $generator->template = $template;
        $generator->tableName = $tableName;
        $generator->generateRelationsFromCurrentSchema = false;

        $files = $generator->generate();
        $this->assertEquals($filesCount, count($files));

        foreach ($relationSets as $index => $relations) {
            $modelCode = $files[$index]->content;
            $modelClass = basename($files[$index]->path, '.php');

            if (version_compare(str_replace('-dev', '', Yii::getVersion()), '2.0.4', '<')) {
                $this->markTestSkipped('This feature is only available since Yii 2.0.4.');
            }

            foreach ($relations as $relation) {
                $this->assertTrue(strpos($modelCode, $relation) !== false,
                    "Model $modelClass should contain this relation: $relation.\n$modelCode");
            }
        }
    }
}
