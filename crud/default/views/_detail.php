<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var mootensai\enhancedgii\crud\Generator $generator
 * @var int $count
 */
$urlParams = $generator->generateUrlParams();
$tableSchema = $generator->getTableSchema();
$pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

use kartik\helpers\Html;
use kartik\detail\DetailView;
use kartik\grid\GridView;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= "<?= " ?>Html::encode($model-><?= $generator->getNameAttribute() ?>) ?></h2>
        </div>
    </div>

    <div class="row">
<?= "<?php \n" ?>
    $gridColumn = [
<?php 
if ($tableSchema === false) {
    $count = 0;
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
            echo "        ".$generator->generateDetailViewField($attribute,$fk, $tableSchema);

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
</div>