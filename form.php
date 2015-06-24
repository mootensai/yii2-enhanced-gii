<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<h3>General Settings</h3>";
echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'nameAttribute');
echo $form->field($generator, 'hiddenColumns');
echo $form->field($generator, 'skippedColumns');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'nsTraits');

echo "<h3>Model</h3>";
echo $form->field($generator, 'nsModel');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'baseModelClass');
echo $form->field($generator, 'nsSearchModel');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateMigrations')->checkbox();
echo "<h4>Optimistic Lock</h4>";
echo $form->field($generator, 'optimisticLock');
echo "<h4>Timestamp Behaviors</h4>";
echo $form->field($generator, 'createdAt');
echo $form->field($generator, 'updatedAt');
echo $form->field($generator, 'timestampValue');
echo "<h4>Blameable Behaviors</h4>";
echo $form->field($generator, 'createdBy');
echo $form->field($generator, 'updatedBy');
echo $form->field($generator, 'blameableValue');
echo "<h4>UUID Behaviors</h4>";
echo $form->field($generator, 'UUIDColumn');
echo "<h4>Soft Delete Trait</h4>";
echo $form->field($generator, 'deletedBy');
echo $form->field($generator, 'deletedAt');
echo "<hr />";
echo "<h3>Views & Controllers</h3>";
echo $form->field($generator, 'nsController');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'skippedRelations');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'viewPath');