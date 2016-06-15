<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */
/* @var $relations array */
/* @var $relName string */

//print_r($relations);
$tableSchema = $generator->getDbConnection()->getTableSchema($relations[$generator::REL_TABLE]);
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
<?php
// @TODO : use namespace of foreign keys & widgets
?>

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->nsModel, '\\').'\\'.$relations[$generator::REL_CLASS] ?> */
/* @var $form yii\widgets\ActiveForm */

<?php
$pk = empty($generator->tableSchema->primaryKey) ? $generator->tableSchema->getColumnNames()[0] : $generator->tableSchema->primaryKey[0];
$modelClass = StringHelper::basename($generator->modelClass);
//foreach ($relations as $name => $rel) {
//    $relID = Inflector::camel2id($rel[1]);
//    if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)) {
//        echo "\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, \n"
//                . "    'viewParams' => [\n"
//                . "        'class' => '$rel[1]', \n"
//                . "        'relID' => '$relID', \n"
//                . "        'value' => \yii\helpers\Json::encode(\$model->$name),\n"
//                . "        'isNewRecord' => (\$model->isNewRecord) ? 1 : 0\n"
//                . "    ]\n"
//                . "]);\n";
//    }
//}
?>
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

<?php
foreach ($tableSchema->getColumnNames() as $attribute) {
    if (!in_array($attribute, $generator->skippedColumns) && !in_array($attribute, array_keys($fk))) {
        echo "    <?= " . $generator->generateActiveField($attribute, $fk, $tableSchema, $relations) . " ?>\n\n";
    }
}
?>
</div>
