<?php
/* @var $this yii\web\View */
/* @var $generator inquid\enhancedgii\module\Generator */
/** @var string $databaseName */
?>
<?php
echo "<?php 
\$this->title = '{$generator->moduleID}';
?>"
?>
<div class="<?= $generator->moduleID . '-default-index' ?>">
    <h1><?=
        $databaseName ?></h1>
    <p>
        Módulo de administración
    </p>
</div>
