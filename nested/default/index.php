<?php

use frontend\modules\inventory\models\Satuan;
use frontend\modules\inventory\models\SatuanSearch;
use kartik\tree\TreeView;
use yii\data\ActiveDataProvider;
use kartik\tree\Module;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel SatuanSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('frontend', 'Satuans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="satuan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <!--<? Html::a(Yii::t('frontend', 'Create Satuan'), ['create'], ['class' => 'btn btn-success']) ?>-->
    </p>

    <?php
        echo TreeView::widget([
        'query' => Satuan::find()->addOrderBy('root, lft'),
        'headingOptions' => ['label' => 'Categories'],
        'rootOptions' => ['label' => '<span class="text-primary">Root</span>'],
        'fontAwesome' => false,
        'isAdmin' => true,
        'displayValue' => 1,
//        'iconEditSettings' => [
//            'show' => 'list',
//            'listData' => [
//                'folder' => 'Folder',
//                'file' => 'File',
//                'mobile' => 'Phone',
//                'bell' => 'Bell',
//            ]
//        ],
        'softDelete' => true,
        'cacheSettings' => ['enableCache' => true],
        'nodeAddlViews' => [
            Module::VIEW_PART_2 => '@frontend/modules/inventory/views/satuan/_pengali' // set a path accessible
        ]
    ]);
    ?>

</div>
<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>

</div>
