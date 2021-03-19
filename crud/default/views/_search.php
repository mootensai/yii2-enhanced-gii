<?php

use \yii\helpers\Inflector;
use \yii\helpers\StringHelper;

/**
 * @var \yii\web\View $this
 * @var \mootensai\enhancedgii\crud\Generator $generator
 * @var int $count
 */

$fk = $generator->generateFK();

echo "<?php\n";
?>

use \kartik\helpers\Html;
use \kartik\widgets\ActiveForm;

/**
* @var \yii\web\View $this
* @var <?= ltrim($generator->searchModelClass, '\\') ?> $model
* @var \kartik\widgets\ActiveForm $form
*/
?>

<div class="form-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (!in_array($attribute, $generator->skippedColumns)) {
        if (++$count < 6) {
            echo "    <?= " . $generator->generateActiveField($attribute, $fk) . " ?>\n\n";
        } else {
            echo "    <?php /* echo " . $generator->generateActiveField($attribute, $fk) . " */ ?>\n\n";
        }
    }
}
?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Search') ?>, ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::resetButton(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-default']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
