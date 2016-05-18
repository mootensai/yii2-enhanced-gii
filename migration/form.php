<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'migrationPath');
echo $form->field($generator, 'migrationTime')->widget('yii\widgets\MaskedInput', [
    'mask' => '999999_999999'
]);
echo $form->field($generator, 'migrationName');
echo $form->field($generator, 'db');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
