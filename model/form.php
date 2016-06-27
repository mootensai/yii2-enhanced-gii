<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
//echo $form->field($generator, 'nsTraits');

echo $form->field($generator, 'nsModel');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'baseModelClass');
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryBaseClass');
?>
<div class="row">
    <div class="col-md-6">
<?= $form->field($generator, 'generateLabelsFromComments')->checkbox(); ?>
    </div>
    <div class="col-md-6">
<?= $form->field($generator, 'useTablePrefix')->checkbox(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php
            echo $form->field($generator, 'generateRelations')->dropDownList([
                $generator::RELATIONS_NONE => 'No relations',
                $generator::RELATIONS_ALL => 'All relations',
                $generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
            ]);
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= $form->field($generator, 'skippedRelations');?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'generateAttributeHints')->checkbox(); ?>
    </div>
    <div class="col-md-6">
<?= $form->field($generator, 'generateBaseOnly')->checkbox(); ?>
    </div>
</div>
<?= $form->field($generator, 'optimisticLock'); ?>
<?= "<h4>Timestamp Behaviors</h4>"; ?>
<div class="row">
    <div class="col-md-3">
<?= $form->field($generator, 'createdAt'); ?>
    </div>
    <div class="col-md-3">
<?= $form->field($generator, 'updatedAt'); ?>
    </div>
    <div class="col-md-6">
<?= $form->field($generator, 'timestampValue'); ?>
    </div>
</div>
<?php
echo "<h4>Blameable Behaviors</h4>";
?>
<div class="row">
    <div class="col-md-3">
<?= $form->field($generator, 'createdBy'); ?>
    </div>
    <div class="col-md-3">
<?= $form->field($generator, 'updatedBy'); ?>
    </div>
    <div class="col-md-6">
<?= $form->field($generator, 'blameableValue'); ?>
    </div>
</div>
<?php
echo $form->field($generator, 'UUIDColumn');
//echo "<h4>Soft Delete Trait</h4>";
//echo $form->field($generator, 'deletedBy');
//echo $form->field($generator, 'deletedAt');