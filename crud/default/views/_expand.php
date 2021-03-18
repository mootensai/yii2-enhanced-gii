<?php

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$pk = empty($generator->tableSchema->primaryKey) ? $generator->tableSchema->getColumnNames()[0] : $generator->tableSchema->primaryKey[0];
?>
<?= "<?php" ?>

use yii\helpers\Html;
use kartik\tabs\TabsX;
use yii\helpers\Url;
$items = [
    [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode(<?= $generator->generateString(StringHelper::basename($generator->modelClass)) ?>),
        'options' => ['id' => "tab_<?= StringHelper::basename($generator->modelClass) ?>_{$model-><?= $pk ?>}"],
        'content' => $this->render('_detail', [
            'model' => $model,
        ]),
    ],
<?php foreach ($relations as $name => $rel): ?>
    <?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
    [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
        'options' => ['id' => "tab_<?= $rel[1] ?>_{$model-><?= $pk ?>}"],
        'content' => $this->render('_data<?= $rel[1] ?>', [
            'model' => $model,
            'row' => $model-><?= $name ?>,
        ]),
    ],
    <?php elseif(isset($rel[$generator::REL_IS_MASTER]) && !$rel[$generator::REL_IS_MASTER]): ?>
    [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
        'options' => ['id' => "tab_<?= $rel[1] ?>_{$model-><?= $pk ?>}"],
        'content' => $this->render('_data<?= $rel[1] ?>', [
        'model' => $model-><?= $name ?>
        ]),
    ],
    <?php endif; ?>
<?php endforeach; ?>
];
echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false,
    'class' => 'tes',
    'pluginOptions' => [
        'bordered' => true,
        'sideways' => true,
        'enableCache' => false
    ],
]);
?>
