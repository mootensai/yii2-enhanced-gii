<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */
echo $form->errorSummary($generator);
echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'nsModel');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'skippedRelations');
echo $form->field($generator, 'nameAttribute');
echo $form->field($generator, 'hiddenColumns');
echo $form->field($generator, 'skippedColumns');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');

echo $form->field($generator, 'nsController');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'pluralize')->checkbox(); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'loggedUserOnly')->checkbox(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'expandable')->checkbox(); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'pdf')->checkbox(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'cancelable')->checkbox(); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'useTablePrefix')->checkbox(); ?>
    </div>
</div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($generator, 'saveAsNew')->checkbox(); ?>
        </div>
        <div class="col-md-6">
            <?php // $form->field($generator, 'useTablePrefix')->checkbox(); ?>
        </div>
    </div>
<?php
echo $form->field($generator, 'generateSearchModel')->checkbox();
echo $form->field($generator, 'nsSearchModel');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'viewPath');
