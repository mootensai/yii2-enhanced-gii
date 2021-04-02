<?php
/**
 * Created by PhpStorm.
 * User: Yohanes
 * Date: 17-Jun-16
 * Time: 1:10 PM
 */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var mootensai\enhancedgii\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use kartik\helpers\Html;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = <?= $generator->generateString('Save As New {modelClass}: ', [
        'modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))
    ]) ?>. ' ' . $model-><?= $generator->getNameAttribute(); ?>;
$this->params['breadcrumbs'][] = ['label' => <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) : $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Save As New');?>;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

    <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>

    <?= "<?= " ?>$this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
