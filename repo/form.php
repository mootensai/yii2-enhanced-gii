<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo "<h2>General</h2>";
echo $form->field($generator, 'name');
echo $form->field($generator, 'local_path');
echo $form->field($generator, 'github_token');
echo $form->field($generator, 'suffix_page');
echo $form->field($generator, 'description')->textarea();
echo $form->field($generator, 'public_repo')->checkbox();
