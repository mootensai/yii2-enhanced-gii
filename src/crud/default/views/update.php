<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \inquid\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?php if ($generator->useTableComment) {
    /** @var string $tableCommentName */
    echo "'" . $tableCommentName . "'";
} else {
    echo "'" . $generator->generateString('Actualizar {modelClass}: ', [
        'modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))
    ]) . "'";
} ?> $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= "'" . $tableCommentName . "'" ?: '\'Index\'' ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Actualizar') ?>;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">
    <div class="box box-primary" style="height: auto !important;">
        <div class="box-header with-border">
            <h3 class="box-title"><?= '<?= ' ?> Html::encode($this->title) ?></h3>
        </div>
        <div class="box-body">
            <?= '<?= ' ?>$this->render('_form', [
            'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
