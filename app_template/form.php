<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo $form->field($generator, 'appName');
echo $form->field($generator, 'path');
echo $form->field($generator, 'repo')->dropDownList(['https://github.com/yiisoft/yii2-app-basic'=>'yii-basic', 'https://github.com/yiisoft/yii2-app-advanced'=>'yii-advance', 'https://github.com/gogl92/legal'=>'inquid']);
