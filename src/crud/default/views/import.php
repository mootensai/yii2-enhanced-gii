<?php
/**
 * Created by Inquid INC.
 */
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

echo "
<?php
use dominus77\sweetalert2\Alert;
use \yii\helpers\Url;
use kartik\\file\FileInput;
";

echo "/* @var \$this \yii\web\View */
\$this->title = 'Import'; ?>"
?>
<?= "<?= Alert::widget(['useSessionFlash' => true]); ?>" ?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= "<?= \yii\helpers\Html::encode(\$this->title) ?>"?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <h1>Formatos</h1>
                    <ul>
                        <li><?= "<?=  \yii\helpers\Html::a('Formato para Importar', ['get-format?format=true']) ?> "?></li>
                        <li><?= "<?= \yii\helpers\Html::a('Formato para Actualizar', ['get-format']) ?> "?></li>
                    </ul>
                </div>
                <div class="col-sm-4">
                    <h1>Validate Format</h1>
                    <?= "<?= FileInput::widget([
                        'name' => 'fileExcelTest',
                        'id' => 'fileExcelTest',
                        'pluginEvents' => [
                            //'filebatchuploadcomplete' => 'function() {location.reload();}',
                        ],
                        'pluginOptions' => [
                            'showPreview' => false,
                            'showCaption' => false,
                            'browseIcon' => '<i class=\"glyphicon glyphicon-file\"></i> ',
                            'browseLabel' => 'Import From Excel',
                            'elCaptionText' => '#customCaption',
                            'uploadUrl' => Url::to(['import-validate']),
                            'allowedFileTypes' => 'object',
                            'allowedFileExtensions' => ['xls', 'xlsx']
                        ],
                    ]); ?>"?>
                </div>
                <div class="col-sm-4">
                    <h1>Import Format</h1>
                    <?= "<?= FileInput::widget([
                        'name' => 'fileExcel',
                        'id' => 'fileExcel',
                        'pluginEvents' => [
                            //'filebatchuploadcomplete' => 'function() {location.reload();}',
                        ],
                        'pluginOptions' => [
                            'showPreview' => false,
                            'showCaption' => false,
                            'browseIcon' => '<i class=\"glyphicon glyphicon-file\"></i> ',
                            'browseLabel' => 'Import from Excel',
                            'elCaptionText' => '#customCaption',
                            'uploadUrl' => Url::to(['import-excel']),
                            'allowedFileTypes' => 'object',
                            'allowedFileExtensions' => ['xls', 'xlsx']
                        ],
                    ]); ?>" ?>
                </div>
            </div>
        </div>
    </div>
</div>
