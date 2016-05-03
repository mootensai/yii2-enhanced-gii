<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
<?php
// @TODO : use namespace of foreign keys & widgets
?>

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */

<?php 
$pk = empty($generator->tableSchema->primaryKey) ? $generator->tableSchema->getColumnNames()[0] : $generator->tableSchema->primaryKey[0];
$modelClass = StringHelper::basename($generator->modelClass);
foreach ($relations as $name => $rel) {
    $relID = Inflector::camel2id($rel[1]);
    if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)) {
        echo "\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, \n"
                . "    'viewParams' => [\n"
                . "        'class' => '$rel[1]', \n"
                . "        'relID' => '$relID', \n"
                . "        'value' => \yii\helpers\Json::encode(\$model->$name),\n"
                . "        'isNewRecord' => (\$model->isNewRecord) ? 1 : 0\n"
                . "    ]\n"
                . "]);\n";
    }
}
?>
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    
    <?= "<?= " ?>$form->errorSummary($model); ?>

<?php foreach ($generator->tableSchema->getColumnNames() as $attribute) {
    if (!in_array($attribute, $generator->skippedColumns)) {
        echo "    <?= " . $generator->generateActiveField($attribute, $generator->generateFK()) . " ?>\n\n";
    }
} ?>
<?php 
foreach ($relations as $name => $rel) {
    $relID = Inflector::camel2id($rel[1]);
    if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)) {
        echo "    <div class=\"form-group\" id=\"add-$relID\">\n"
            . "        <?= \$this->render('_form".$rel[1]."', ['row'=>\yii\helpers\ArrayHelper::toArray(\$model->$name)]); ?>\n"
            . "    </div>\n\n";
    }
}
?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
<?php if ($generator->cancelable): ?>
        <?= "<?= " ?>Html::a(Yii::t('app', 'Cancel'),['index'],['class'=> 'btn btn-danger']) ?>
<?php endif; ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
