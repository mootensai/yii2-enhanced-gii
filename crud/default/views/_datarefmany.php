<?php

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */
$tableSchema = $generator->getDbConnection()->getTableSchema($relations[3]);
$fk = $generator->generateFK($tableSchema);
?>
<?= "<?php" ?>

use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

    $dataProvider = new ArrayDataProvider([
        'allModels' => $model-><?= $relName; ?>,
<?php if (count($tableSchema->primaryKey) > 1):
    $key = [];
    foreach ($tableSchema->primaryKey as $pk) {
        $key[] = "'$pk' => \$model->$pk";
    }
?>
        'key' => function($model){
            return [<?= implode(', ', $key); ?>];
        }
<?php else:?>
        'key' => '<?= $tableSchema->primaryKey[0] ?>'
<?php endif; ?>
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
<?php 
if ($tableSchema === false) :
    foreach ($generator->getColumnNames() as $name) {
        if ($name == $relations[4]) continue;
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
else :
foreach ($tableSchema->getColumnNames() as $attribute): 
if (!in_array($attribute, $generator->skippedColumns) && $attribute != $relations[5]) :
?>
        <?= $generator->generateGridViewField($attribute, $fk, $tableSchema)?>
<?php
endif;
endforeach;
endif; ?>
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => '<?= \yii\helpers\Inflector::camel2id($relations[1])?>'
        ],
    ];
    
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'],
        'pjax' => true,
        'beforeHeader' => [
            [
                'options' => ['class' => 'skip-export']
            ]
        ],
        'export' => [
            'fontAwesome' => true
        ],
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'hover' => true,
        'showPageSummary' => false,
        'persistResize' => false,
    ]);
