<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */
?>
<blockquote class="alert-warning" style="font-size: small">
    <strong>Note : </strong><br />
    To generate nested or tree, please use <a href="http://demos.krajee.com/tree-manager#prepare-database">kartik-v\yii2-tree-manager</a> for table structure<br />
    <strong>If table contains all the defined columns, the generator will automatically generate model that extends </strong><code>\kartik\tree\models\Tree</code>
</blockquote>
<?php
echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');

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