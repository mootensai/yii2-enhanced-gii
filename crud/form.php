<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */
?>
<blockquote class="alert-warning" style="font-size: small">
    <strong>Note : </strong><br />
    To generate nested or tree, please use <a href="http://demos.krajee.com/tree-manager#prepare-database">kartik-v\yii2-tree-manager</a> for table structure<br />
    <strong>If table contains all the defined columns, the generator will automatically generate CRUD that uses </strong><a href="http://demos.krajee.com/tree-manager#tree-view">kartik\tree\TreeView</a>
</blockquote>
<?php
echo $form->errorSummary($generator);
echo $form->field($generator, 'db');
echo $form->field($generator, 'moduleName');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'nsModel');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'skippedRelations');
echo $form->field($generator, 'nameAttribute');
echo $form->field($generator, 'hiddenColumns');
echo $form->field($generator, 'skippedColumns');
echo $form->field($generator, 'skippedTables');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');

echo $form->field($generator, 'modelSort')->dropDownList(['SORT_DESC'=>'⭣ DESCENDING Last created at the top','SORT_ASC'=>'⭡ ASCENDING First created at the top ']);

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
    <div class="col-md-4">
        <?= $form->field($generator, 'expandable')->checkbox(); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($generator, 'pdf')->checkbox(); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($generator, 'importExcel')->checkbox(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($generator, 'cancelable')->checkbox(); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($generator, 'saveAsNew')->checkbox(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?=  $form->field($generator, 'useTablePrefix')->checkbox(); ?>
    </div>
    <div class="col-md-6">
        <?=  $form->field($generator, 'useTableComment')->checkbox(); ?>
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
echo $form->field($generator,'placeHolders')->checkbox();
echo $form->field($generator, 'generateDocumentation')->checkbox();
