<?php

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
?>
<?= "<?php" ?>

use yii\helpers\Html;
use kartik\tabs\TabsX;
use yii\helpers\Url;
$items = [
    [
        'label' => '<i class="glyphicon glyphicon-user"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
        'content' => $this->render('_view', [
            'all' => false,
        ]),
    ],
<?php foreach ($relations as $name => $rel): ?>
    <?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
    [
        'label' => '<i class="glyphicon glyphicon-user"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
        'content' => $this->render('_data<?= $rel[1] ?>', [
            'model' => $model,
            'row' => $model-><?= $name ?>,
        ]),
    ],
    <?php endif; ?>
<?php endforeach; ?>
];
echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false,
    'class' => 'tes',
    'pluginOptions' => [
        'bordered' => true,
        'sideways' => true,
        'enableCache' => false
        //        'height' => TabsX::SIZE_TINY
    ],
    'pluginEvents' => [
        "tabsX.click" => "function(e) {setTimeout(function(e){
                if ($('.nav-tabs > .active').next('li').length == 0) {
                    $('#prev').show();
                    $('#next').hide();
                } else if($('.nav-tabs > .active').prev('li').length == 0){
                    $('#next').show();
                    $('#prev').hide();
                }else{
                    $('#next').show();
                    $('#prev').show();
                };
                console.log(JSON.stringify($('.active', '.nav-tabs').html()));
            },10)}",
    ],
]);
?>
