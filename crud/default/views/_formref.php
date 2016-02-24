<?php
/* @var $generator \mootensai\enhancedgii\crud\Generator */
$tableSchema = $generator->getDbConnection()->getTableSchema($relations[3]);
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>
use kartik\grid\GridView;
use kartik\builder\TabularForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

Pjax::begin();
$dataProvider = new ArrayDataProvider([
    'allModels' => $row,
]);
echo TabularForm::widget([
    'dataProvider' => $dataProvider,
    'formName' => '<?= $relations[1]; ?>',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
<?php foreach ($tableSchema->getColumnNames() as $attribute) : 
    $column = $tableSchema->getColumn($attribute);
    if (!in_array($attribute, $generator->skippedColumns) && $attribute != $relations[4]) {
//        echo $attribute." ".$relations[4];
        echo "        " . $generator->generateTabularFormField($attribute, $fk, $tableSchema) . ",\n";
    }
endforeach; ?>
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  <?= $generator->generateString('Delete') ?>, 'onClick' => 'delRow<?= $relations[1]; ?>(' . $key . '); return false;', 'id' => '<?= yii\helpers\Inflector::camel2id($relations[1]) ?>-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . <?= $generator->generateString(yii\helpers\Inflector::camel2words($relations[1])) ?>,
            'type' => GridView::TYPE_INFO,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . <?= $generator->generateString('Add Row') ?>, ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRow<?= $relations[1]; ?>()']),
        ]
    ]
]);
Pjax::end();
?>
