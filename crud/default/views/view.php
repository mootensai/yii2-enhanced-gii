<?php

use almirb\inflectorbr\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */
$urlParams = $generator->generateUrlParams();
$pk = empty($generator->tableSchema->primaryKey) ? $generator->tableSchema->getColumnNames()[0] : $generator->tableSchema->primaryKey[0];

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
    <h2><?= '<?= Html::encode($this->title)' ?> ?></h2>
    <br/>
<?php if ($generator->generateFlashMessages) : ?>
    <?='<?php ' ?>\mootensai\enhancedgii\components\FlashHelper::showFlashMessages(); <?='?>' ?>
<?php endif; ?>

    <div class="clearfix crud-navigation">
        <!-- menu buttons -->
        <div class='pull-left'>
            <?= "
            <?= Html::a('<span class=\"glyphicon glyphicon-plus\"></span> '." . $generator->generateString('Create') . ", ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<span class=\"glyphicon glyphicon-pencil\"></span> '." . $generator->generateString('Edit') . ", ['update', " . $generator->generateUrlParams() . "], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class=\"glyphicon glyphicon-trash\"></span> '." . $generator->generateString('Delete') . ", ['delete', " . $generator->generateUrlParams() . "], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => " . $generator->generateString('Are you sure you want to delete this item?') . ",
                    'method' => 'post',
                ],
            ])
            ?>\n" ?>
            <?php if ($generator->pdf): ?>
<?= "<?= " ?>
            <?= "
             Html::a('<i class=\"fa glyphicon glyphicon-hand-up\"></i> ' . " . $generator->generateString('PDF') . ", 
                ['pdf', 'id' => \$model['$pk']],
                [
                    'class' => 'btn btn-danger',
                    'target' => '_blank',
                    'data-toggle' => 'tooltip',
                    'title' => " . $generator->generateString('Will open the generated PDF file in a new window') . "
                ]
            )?>"
            ?>
            <?php endif; ?>
</div>
        <div class="pull-right">
            <?= "            
            <?= Html::a('<span class=\"glyphicon glyphicon-list\"></span> '.". $generator->generateString('List') . ", ['index'], ['class' => 'btn btn-default']) ?>
            \n" ?>
        </div>
    </div>
    <br/>
    <?php if ($generator->generateRelationsOnView): ?>
    <div class="row">
    <?php endif ?>
<?= "<?php \n" ?>
    $gridColumn = [
<?php 
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else{
    foreach($tableSchema->getColumnNames() as $attribute){
        if(!in_array($attribute, $generator->skippedColumns)) {
            echo "        ".$generator->generateDetailViewField($attribute,$generator->generateFK($tableSchema), $tableSchema);

        }
    }
}?>
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>

<?php if ($generator->generateRelationsOnView) {  ?>
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
    </div>
    <div class="row">
<?= "<?php\n" ?>
if($provider<?= $rel[1] ?>->totalCount){
    $gridColumn<?= $rel[1] ?> = [
        ['class' => 'yii\grid\SerialColumn'],
<?php
        $tableSchema = $generator->getDbConnection()->getTableSchema($rel[3]);
            if ($tableSchema === false) {
                foreach ($tableSchema->getColumnNames() as $attribute) {
                    if (!in_array($attribute, $generator->skippedColumns)){
                        echo "            '" . $attribute . "',\n";
                    }
                }
            } else {
                foreach ($tableSchema->getColumnNames() as $attribute){
                    if (!in_array($attribute, $generator->skippedColumns)){
                        echo '            '.$generator->generateGridViewField($attribute, $generator->generateFK($tableSchema), $tableSchema);
                    }
                }
            }
?>
    ];
    echo Gridview::widget([
        'dataProvider' => $provider<?= $rel[1] ?>,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id($rel[3])?>']],
        'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>.' '. $this->title),
        ],
        'columns' => $gridColumn<?= $rel[1] . "\n" ?>
    ]);
}
<?= "?>\n" ?>
    </div>
<?php endif; ?>
<?php endforeach; ?>
<?php } ?>
</div>