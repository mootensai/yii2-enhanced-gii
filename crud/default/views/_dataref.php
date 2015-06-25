<?php

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

?>
<?= "<?php" ?>

use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

    $dataProvider = new ArrayDataProvider([
        'allModels' => $model-><?= $relName; ?>,
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
<?php 
if (($tableSchema = $generator->getTableSchema()) === false) :
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
else :
foreach($tableSchema->getColumnNames() as $attribute): 
if(!in_array($attribute, $generator->skippedColumns)) :
?>
        <?= $generator->generateGridViewField($attribute,$generator->generateFK($tableSchema), $tableSchema)?>
<?php
endif;
endforeach;
endif;?>
    ];
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'],
        'pjax' => true,
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => 'Pengeluaran Barang Detail', 'options' => ['colspan' => 5, 'class' => 'text-center warning']],
                ],
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
