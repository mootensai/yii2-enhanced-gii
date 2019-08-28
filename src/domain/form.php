<?php

declare(strict_types=1);

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo '<h2>General</h2>';
echo $form->field($generator, 'godaddy_key');
echo $form->field($generator, 'godaddy_secret');
echo $form->field($generator, 'domain');
echo $form->field($generator, 'ip');
echo $form->field($generator, 'name');
echo $form->field($generator, 'ttl');
echo $form->field($generator, 'type')->dropDownList(['A'=>'A']);
