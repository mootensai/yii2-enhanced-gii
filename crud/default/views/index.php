<?php

use almirb\inflectorbr\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$tableSchema = $generator->getTableSchema();
$baseModelClass = StringHelper::basename($generator->modelClass);
echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\export\ExportMenu;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView;" : "yii\\widgets\\ListView;" ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words($baseModelClass))) : $generator->generateString(Inflector::camel2words($baseModelClass)) ?>;
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
//$this->registerJs($search);
?>
<div class="<?= Inflector::camel2id($baseModelClass) ?>-index">

<?php if (!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
<?php if (!empty($generator->searchModelClass)): ?>
        <!--Remove hide class to display-->
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Advanced Search')?>, '#', ['class' => 'btn btn-info search-button hide']) ?>
<?php endif; ?>
    </p>
    <?php if (!empty($generator->searchModelClass)): ?>
    <div class="search-form" style="display:none">
        <?= "<?php //echo " ?> $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php endif; ?>
<?php 
if ($generator->indexWidgetType === 'grid'): 
?>
    <?= "<?php \n" ?>

    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
<?php
    if ($generator->expandable):
?>
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand', ['model' => $model]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'expandOneOnly' => true
        ],
<?php
    endif;
?>
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
        foreach ($tableSchema->getColumnNames() as $attribute): 
            if (!in_array($attribute, $generator->skippedColumns)) :
?>
        <?= $generator->generateGridViewFieldIndex($attribute, $generator->generateFK($tableSchema), $tableSchema)?>
<?php
            endif;
        endforeach; ?>
        [
            'class' => 'almirb\btactioncolumn\ActionColumn',
        ],
    ]; 
<?php 
    endif; 
?>
    ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => \$gridColumn,\n" : "'columns' => \$gridColumn,\n"; ?>
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass))?>']],
        'hover' => true,
        'responsiveWrap' => false,
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        // set a label for default menu
        /*'export' => [
            'label' => <?= $generator->generateString('Page')?>,
            'fontAwesome' => false,
        ],*/
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
            'before'  => Html::a('<i class="glyphicon glyphicon-plus"></i> '.<?= $generator->generateString('Create') ?>, ['create'], ['class' => 'btn btn-success', 'data-pjax'=>0]),
            'after'   => Html::a('<i class="glyphicon glyphicon-repeat"></i> '.<?= $generator->generateString('Reset Filters')?>, ['index'], ['class' => 'btn btn-info']),
        ],
        'toolbar' => [
            [
                'content'=>
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], [
                'class' => 'btn btn-default',
                'title' => <?= $generator->generateString('Reset Filters')?>
                ]),
            ],
            '{export}',
            // your toolbar can include the additional full export menu
            /*ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => false,
                'dropdownOptions' => [
                    'label' => <?= $generator->generateString('Full')?>,
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">'.<?= $generator->generateString('Export All Data')?>.'</li>',
                    ],
                ],
            ]) ,*/
            '{toggleData}',
        ],
    ]); ?>
<?php 
else: 
?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php 
endif; 
?>

</div>
