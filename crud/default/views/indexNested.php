<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

//$urlParams = $generator->generateUrlParams();
//$nameAttribute = $generator->getNameAttribute();
$tableSchema = $generator->getTableSchema();
$baseModelClass = StringHelper::basename($generator->modelClass);
$id = Inflector::camel2id($baseModelClass);
$fk = $generator->generateFK($tableSchema);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}
?>
<?= "<?php" ?>

/* @var $this yii\web\View */

use yii\helpers\Html;
use <?= ltrim($generator->modelClass, '\\') ?>;
use kartik\tree\TreeView;
use kartik\tree\Module;
use yii\web\View;


$this->title = <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words($baseModelClass))) : $generator->generateString(Inflector::camel2words($baseModelClass)) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= $id ?>-index">

    <h1><?= "<?=" ?>Html::encode($this->title) ?></h1>

    <?= "<?php\n" ?>
    echo TreeView::widget([
        'query' => <?= $baseModelClass?>::find()->addOrderBy('root, lft'),
        'headingOptions' => ['label' => '<?= Inflector::humanize($baseModelClass) ?>'],
        'rootOptions' => ['label' => '<span class="text-primary">Root</span>'],
        'fontAwesome' => false,
        'isAdmin' => true, // @TODO : put your isAdmin getter here
        'displayValue' => 0,
        'cacheSettings' => ['enableCache' => true],
        'nodeAddlViews' => [
            Module::VIEW_PART_2 => '<?= $generator->viewPath ?>/<?= $id ?>/_form'
        ]
    ]);
    ?>

</div>
