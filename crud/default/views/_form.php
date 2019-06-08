<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \inquid\enhancedgii\crud\Generator */
/* @var $relations array */
$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use inquid\vue\Vue;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */

<?php
$pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];
$modelClass = StringHelper::basename($generator->modelClass);
foreach ($relations as $name => $rel) {
    $relID = Inflector::camel2id($rel[$generator::REL_CLASS]);
    if ($rel[$generator::REL_IS_MULTIPLE] && isset($rel[$generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)) {
        echo "\\mootensai\\components\\JsBlock::widget(['viewFile' => '_script', 'pos'=> \\yii\\web\\View::POS_END, \n"
            . "    'viewParams' => [\n"
            . "        'class' => '{$rel[$generator::REL_CLASS]}', \n"
            . "        'relID' => '$relID', \n"
            . "        'value' => \\yii\\helpers\\Json::encode(\$model->$name),\n"
            . "        'isNewRecord' => (\$model->isNewRecord) ? 1 : 0\n"
            . "    ]\n"
            . "]);\n";
    }
}
?>
Vue::begin([
    'jsName' => '<?= Inflector::underscore(StringHelper::basename($generator->modelClass)) ?>',
    'id' => "<?= Inflector::underscore(StringHelper::basename($generator->modelClass)) ?>-form",
    'data' => [
<?php
$numberInputs = count($tableSchema->getColumnNames());
foreach ($tableSchema->getColumnNames() as $key => $attribute) {
    if (!in_array($attribute, $generator->skippedColumns)) {
        //TODO Get the number of inputs divide in rows and add the col-sm-size to each attribute
        echo "'$attribute' => isset(\$model->$attribute) ? \$model->$attribute : '',\n";
    }
}
?>
],
    'methods' => [
    'reverseMessage' => new yii\web\JsExpression("function(){"
    . "this.message =1; "
    . "}"),
]
]);
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?php
    if ($generator->hasFile($tableSchema)):
        echo "<?php " ?>$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','class' => 'disable-submit-buttons']]); ?>
    <?php else: ?>
        <?= "<?php " ?>$form = ActiveForm::begin(['options' => ['class' => 'disable-submit-buttons']]); ?>
    <?php endif; ?>

    <?= "<?= " ?>$form->errorSummary($model); ?>

    <?php
    $rowCount = 0;
    $bootstrapColWidth = 12 / $generator->formColumns;
    echo "<div class='row'>";
    foreach ($tableSchema->getColumnNames() as $key => $attribute) {
        if ($attribute == $tableSchema->primaryKey[0]) {
            echo "</div>";
            echo "    \n<?= " . $generator->generateActiveField($attribute, $fk) . " ?>\n\n";
            echo "<div class='row'>";
            continue;
        }
        if (!in_array($attribute, $generator->skippedColumns)) {
            echo "<div class='col-md-$bootstrapColWidth'>";
            echo "    \n<?= " . $generator->generateActiveField($attribute, $fk) . " ?>\n\n";
            echo "</div>";
            $rowCount++;
            if ($rowCount % $generator->formColumns == 0) echo '</div><div class="row">';
        }
    }
    echo "</div>";

    $forms = "";
    foreach ($relations as $name => $rel) {
        $relID = Inflector::camel2id($rel[$generator::FK_FIELD_NAME]);
        if ($rel[$generator::REL_IS_MULTIPLE] && isset($rel[$generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)) {
            $forms .= "        [\n" .
                "            'label' => '<i class=\"glyphicon glyphicon-book\"></i> ' . Html::encode(" . $generator->generateString($rel[$generator::REL_CLASS]) . "),\n" .
                "            'content' => \$this->render('_form" . $rel[$generator::FK_FIELD_NAME] . "', [\n" .
                "                'row' => \\yii\\helpers\\ArrayHelper::toArray(\$model->$name),\n" .
                "            ]),\n" .
                "        ],\n";
        } else if (isset($rel[$generator::REL_IS_MASTER]) && !$rel[$generator::REL_IS_MASTER]) {
            $forms .= "        [\n" .
                "            'label' => '<i class=\"glyphicon glyphicon-book\"></i> ' . Html::encode(" . $generator->generateString($rel[$generator::REL_CLASS]) . "),\n" .
                "            'content' => \$this->render('_form" . $rel[$generator::FK_FIELD_NAME] . "', [\n" .
                "                'form' => \$form,\n" .
                "                '" . $rel[$generator::REL_CLASS] . "' => is_null(\$model->$name) ? new " . $generator->nsModel . "\\" . $rel[$generator::REL_CLASS] . "() : \$model->$name,\n" .
                "            ]),\n" .
                "        ],\n";
        }
    }
    if (!empty($forms)) {
        echo "    <?php\n";
        echo "    \$forms = [\n";
        echo $forms;
        echo "    ];\n";
        echo "    echo kartik\\tabs\\TabsX::widget([\n" .
            "        'items' => \$forms,\n" .
            "        'position' => kartik\\tabs\\TabsX::POS_ABOVE,\n" .
            "        'encodeLabels' => false,\n" .
            "        'pluginOptions' => [\n" .
            "            'bordered' => true,\n" .
            "            'sideways' => true,\n" .
            "            'enableCache' => false,\n" .
            "        ],\n" .
            "    ]);\n" .
            "    ?>\n";
    }
    ?>
    <div class="form-group">
        <?php if ($generator->saveAsNew): ?>
            <?= "    <?php if(Yii::\$app->controller->action->id != 'save-as-new'): ?>\n" ?>
            <?= "        <?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Agregar') ?> : <?= $generator->generateString('Actualizar') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','data' => ['disabled-text' => 'Please Wait']]) ?>
            <?= "    <?php endif; ?>\n" ?>
            <?= "    <?php if(Yii::\$app->controller->action->id != 'create'): ?>\n" ?>
            <?= "        <?= " ?>Html::submitButton(<?= $generator->generateString('Duplicar Registro') ?>, ['class' => 'btn btn-info', 'value' => '1', 'name' => '_asnew']) ?>
            <?= "    <?php endif; ?>\n" ?>
        <?php else: ?>
            <?= "        <?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Agregar') ?> : <?= $generator->generateString('Actualizar') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','data' => ['disabled-text' => 'Please Wait']]) ?>
        <?php endif; ?>
        <?php if ($generator->cancelable): ?>
            <?= "<?= " ?>Html::a(Yii::t('app', 'Cancelar'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger']) ?>
        <?php endif; ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>
    <?= "<?php " ?>Vue::end(); ?>
</div>