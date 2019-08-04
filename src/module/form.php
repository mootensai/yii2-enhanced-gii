<?php declare(strict_types=1);
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator inquid\enhancedgii\module\Generator */

?>
<div class="module-form">
    <?php
    echo $form->field($generator, 'db');
    echo $form->field($generator, 'moduleClass');
    echo $form->field($generator, 'moduleID');
    ?>
</div>
