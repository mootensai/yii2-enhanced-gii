<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */
use mootensai\components\JsBlock;

JsBlock::widget(['viewFile' => 'script', 'pos'=> \yii\web\View::POS_END]);
?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'db'); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'tableName'); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'enableI18N')->checkbox(); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'messageCategory'); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'nsModel'); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'modelClass'); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'nsController');?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'controllerClass');?>
    </div>
</div>
<?= $form->field($generator, 'skippedColumns'); ?>
<?= $form->field($generator, 'optimisticLock'); ?>
<?= $form->field($generator, 'generateRelations')->checkbox(); ?>
<?php
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'viewPath');
?>