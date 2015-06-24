<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\Generator */
print_r($relations);
$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\GridView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= "<?= " ?><?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass)))?>.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            <?= "<?php
            echo Html::a('<i class=\"fa glyphicon glyphicon-hand-up\"></i> ' . ".$generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))).", ['pdf', 'id' => \$model['id']], [
                'class' => 'btn btn-danger',
                'target' => '_blank',
                'data-toggle' => 'tooltip',
                'title' => ".$generator->generateString('Will open the generated PDF file in a new window')."
            ]);
            ?>
            <?= Html::a(".$generator->generateString('Update').", ['update', 'id' => \$model['id']], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a(".$generator->generateString('Delete').", ['delete', 'id' => \$model['id']], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => ".$generator->generateString('Are you sure you want to delete this item?').",
                    'method' => 'post',
                ],
            ])
            ?>\n"?>
        </div>
    </div>
    
    <div class="row">
        <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if(!in_array($name, $generator->skippedColumns)){
            echo "            '" . $name . "',\n";
        }
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        if(!in_array($column->name, $generator->skippedColumns)){
            $format = $generator->generateColumnFormat($column);
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
        ],
    ]) ?>
    </div>
<?php foreach($relations as $name => $rel): ?>
<?php if($rel[2] && isset($rel[3])): ?>
        <div class="row">
            <?= "<?php\n"?>
            echo Gridview::widget([
                'dataProvider' => $provider<?= $rel[1] ?>,
                'pjax' => true,
                'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i>  ' . Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1]))?>.' '. $this->title) . ' </h3>',
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
<?php 
                            /* @var $tableSchema \yii\db\TableSchema */
$tableSchema = $generator->getDbConnection()->getTableSchema($rel[3]);
//print_r($tableSchema->getColumnNames());
//print_r($rel);
foreach($tableSchema->getColumnNames() as $attribute): 
if(!in_array($attribute, $generator->skippedColumns)) :
?>
                    <?= $generator->generateGridViewField($attribute,$generator->generateFK($tableSchema), $tableSchema)?>
<?php
endif;
endforeach; ?>
                ],
            ]);
            <?= "?>\n"?>
        </div>
<?php endif; ?>
<?php endforeach; ?>
    </div>
</div>