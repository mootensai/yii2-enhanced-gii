<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
if ($generator->useTableComment) {$customName = $tableCommentName; } else { $customName = ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) : $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))); }
?>

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('Agregar ' . $customName) ?>;
$this->params['breadcrumbs'][] = ['label' => '<?= $customName  ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= "<?= " ?>Html::encode($this->title) ?></h3>
        </div>
            <div class="box-body">
                <?= "<?= " ?>$this->render('_form', [
                    'model' => $model,
                ]) ?>
        </div>
    </div>
</div>
