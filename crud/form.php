<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<h3>General Settings</h3>";
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'skippedRelations');
echo "<h3>Model</h3>";
echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'nsModel');
echo "<h3>Views & Controllers</h3>";
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'nsController');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'viewPath');
