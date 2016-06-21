<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */
/* @var $relName array */
/* @var $relations array */

//print_r($relations);

$urlParams = $generator->generateUrlParams();
$tableSchema = $generator->getDbConnection()->getTableSchema($relations[$generator::REL_CLASS]);
$pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($relations[$generator::REL_CLASS], '\\') ?> */

?>
<?= "<?php if(!is_null(\$model)): ?>\n" ?>
<div>

    <div class="row">
        <div class="col-sm-9">
            <h2><?= "<?= " ?>Html::encode($model-><?= $generator->getNameAttributeFK($relations[$generator::REL_TABLE]) ?>) ?></h2>
        </div>
    </div>

    <div class="row">
    <?= "<?php \n" ?>
        $gridColumn = [
<?php
        if ($tableSchema === false) {
            foreach ($tableSchema->getColumnNames() as $name) {
                if (++$count < 6) {
                    echo "            '" . $name . "',\n";
                } else {
                    echo "            // '" . $name . "',\n";
                }
            }
        } else{
            foreach($tableSchema->getColumnNames() as $attribute){
                if(!in_array($attribute, $generator->skippedColumns)) {
                    echo "            ".$generator->generateDetailViewField($attribute,$fk, $tableSchema);
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
<?= "<?php else: ?>\n" ?>
<div class="<?= Inflector::camel2id($relations[$generator::REL_CLASS]) ?>-view">
    <div class="row">
        <div class="col-sm-9">
            <h2><?= $relations[$generator::REL_CLASS] ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12"><?= $relations[$generator::REL_CLASS] ?> not set.</div>
    </div>
</div>
<?= "<?php endif; ?>\n" ?>