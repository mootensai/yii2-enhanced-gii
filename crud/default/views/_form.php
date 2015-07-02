<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\Generator */

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
foreach($relations as $name => $rel){
    $relID = Inflector::camel2id($rel[1]);
    if($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)){
        echo "\mootensai\components\JsBlock::widget(['viewFile' => '_script', "
                . "'pos'=> \yii\web\View::POS_END, \n"
                . "    'viewParams' => [\n"
                . "        'pk' => '$pk', \n"
                . "        'relID' => '$relID', \n"
                . "    ]\n"
                . "]);\n";
    }
}
?>
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

<?php foreach ($generator->tableSchema->getColumnNames() as $attribute) {
    if(!in_array($attribute, $generator->skippedColumns)) {
        echo "    <?= " . $generator->generateActiveField($attribute, $generator->generateFK()) . " ?>\n\n";
    }
} ?>
<?php 
foreach($relations as $name => $rel){
    $relID = Inflector::camel2id($rel[1]);
    if($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)){
        echo "    <div class=\"form-group\" id=\"add-$relID\"></div>\n\n";
    }
}
?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
