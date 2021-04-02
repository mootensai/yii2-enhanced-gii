<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var mootensai\enhancedgii\crud\Generator $generator
 * @var array $relations
 * @var boolean $isTree
 */

$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
$model = ($isTree) ? '$node' : '$model';
echo "<?php\n";
?>

use kartik\helpers\Html;
use kartik\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> <?= $model?>
*/

?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

<?php
foreach ($tableSchema->getColumnNames() as $attribute) {
    if (!in_array($attribute, $generator->skippedColumns) && !in_array($attribute, $generator::getTreeColumns())) {
        echo "    <?= " . $generator->generateActiveField($attribute, $fk, null, null, $isTree) . " ?>\n\n";
    }
}
?>

</div>
