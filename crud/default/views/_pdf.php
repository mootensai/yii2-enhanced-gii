<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */
$urlParams = $generator->generateUrlParams();
$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) : $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= "<?= " ?><?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>.' '. Html::encode($this->title) ?></h2>
        </div>
    </div>

    <div class="row">
<?= "<?php \n" ?>
    $gridColumn = [
<?php 
if ($tableSchema === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
}else{
    foreach($tableSchema->getColumnNames() as $attribute){
        if(!in_array($attribute, $generator->skippedColumns)) {
            echo "        ".$generator->generateGridViewField($attribute,$fk, $tableSchema);
        }
    }
}?>
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
    
    <div class="row">
<?= "<?php\n" ?>
if($provider<?= $rel[1] ?>->totalCount){
    $gridColumn<?= $rel[1] ?> = [
        ['class' => 'yii\grid\SerialColumn'],
<?php
        $relTableSchema = $generator->getDbConnection()->getTableSchema($rel[3]);
        $fkRel = $generator->generateFK($relTableSchema);
        if ($relTableSchema === false) {
            foreach ($relTableSchema->getColumnNames() as $attribute) {
                if (!in_array($attribute, $generator->skippedColumns) && $attribute != $relations[5]){
                    echo "        '" . $attribute . "',\n";
                }
            }
        }else {
            foreach ($relTableSchema->getColumnNames() as $attribute){
                if (!in_array($attribute, $generator->skippedColumns)){
                    echo '        '.$generator->generateGridViewField($attribute, $fkRel, $relTableSchema);
                }
            }
        }
?>
    ];
    echo Gridview::widget([
        'dataProvider' => $provider<?= $rel[1] ?>,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
        ],
        'panelHeadingTemplate' => '<h4>{heading}</h4>{summary}',
        'toggleData' => false,
        'columns' => $gridColumn<?= $rel[1]."\n" ?>
    ]);
}
<?= "?>\n" ?>
    </div>
<?php endif; ?>
<?php endforeach; ?>
</div>
