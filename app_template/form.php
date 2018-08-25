<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo $form->field($generator, 'appName');
echo $form->field($generator, 'path');
echo $form->field($generator, 'repo')->dropDownList(['yii-basic', 'yii-advance', 'inquid']);
