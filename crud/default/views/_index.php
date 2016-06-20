<?php
/* @var $generator \mootensai\enhancedgii\crud\Generator */
$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
?>
<?= "<?php \n" ?>
use \yii\helpers\Html;
use yii\widgets\DetailView;
?>

<div class="row">
    <div class="<?= ($generator->saveAsNew) ? "col-sm-7" : "col-sm-9";?>">
        <h2><?= "<?= " ?>Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]) ?></h2>
    </div>
    <div class="<?= ($generator->saveAsNew) ? "col-sm-5" : "col-sm-3";?>" style="margin-top: 15px">
<?php if($generator->pdf): ?>
        <?= "<?= " ?>Html::a('<i class="fa glyphicon glyphicon-hand-up"></i> ' . <?= $generator->generateString('Print PDF')?>,
            ['pdf', <?= $urlParams ?>],
            [
                'class' => 'btn btn-danger',
                'target' => '_blank',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('app', 'Will open the generated PDF file in a new window')
            ]
        )?>
<?php endif; ?>
<?php if($generator->saveAsNew): ?>
        <?= "<?= " ?>Html::a('<i class="fa glyphicon glyphicon-hand-up"></i> ' . <?= $generator->generateString('Save As New')?>,
            ['save-as-new', <?= $urlParams ?>], ['class' => 'btn btn-info'])?>
<?php endif; ?>
        <?= "<?=" ?> Html::a(Yii::t('app', 'Update'), ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary']) ?>
        <?= "<?=" ?> Html::a(Yii::t('app', 'Delete'), ['delete', <?= $urlParams ?>], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </div>
    <?= "<?php \n" ?>
    $gridColumn = [
<?php
    $count = 0;
    if ($tableSchema === false) {
        foreach ($generator->getColumnNames() as $name) {
            if (++$count < 6) {
                echo "            '" . $name . "',\n";
            } else {
                echo "            // '" . $name . "',\n";
            }
        }
    } else{
        foreach($tableSchema->getColumnNames() as $attribute){
            if (++$count < 6) {
                if (!in_array($attribute, $generator->skippedColumns)) {
                    echo "        " . $generator->generateDetailViewField($attribute, $fk, $tableSchema);

                }
            }else{
                if (!in_array($attribute, $generator->skippedColumns)) {
                    echo "        //" . $generator->generateDetailViewField($attribute, $fk, $tableSchema);

                }
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


