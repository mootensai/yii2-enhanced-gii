<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

$js = 
<<<JS
    $('#optimistic-lock-cb').click(function(){
        $('#optimistic-lock-tb').toggle();
    });
JS;
$this->registerJs($js);

echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'nsModel');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'nsTrait');
echo $form->field($generator, 'useUUID')->checkbox();
echo $form->field($generator, 'isOptimisticLock', ['options' =>['onClick'=>"$('#optimistic-lock-tb').toggle();"]])->checkbox();
echo $form->field($generator, 'optimisticLockColumn', ['options' => ['id'=>'optimistic-lock-tb']]);
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
