<?php

/**
 * This is the template for generating a CRUD controller class file.
 */
use yii\helpers\StringHelper;
use inquid\yiireports\ExcelHelper;

/* @var $this yii\web\View */
/* @var $generator \inquid\enhancedgii\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}
$pks = $generator->tableSchema->primaryKey;
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
$skippedRelations = array_map(function($value) {
    return "'$value'";
},$generator->skippedRelations);
echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use kartik\mpdf\Pdf;
use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else {
    : ?>
use yii\data\ActiveDataProvider;
<?php endif;
}
?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
<?php if (isset($relations)): ?>
use yii\data\ArrayDataProvider;
<?php endif; ?>
<?php if ($generator->hasFile($generator->tableSchema)):?>
use yii\web\UploadedFile;
<?php endif; ?>
use inquid\google_debugger\GoogleCloudLogger;
use dominus77\sweetalert2\Alert;
use PhpOffice\PhpSpreadsheet\IOFactory;
use inquid\yiireports\ExcelHelper;
use yii\helpers\Json;
use Exception;
use yii\base\InvalidConfigException;
<?php if ($generator->pdf): ?>
    use app\modules\<?=$generator->moduleName?>\components\<?=$modelClass?>PDF;
<?php endif; ?>

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
<?php if ($generator->loggedUserOnly):
    $actions = ["'index'", "'view'", "'create'", "'update'", "'delete'"];
    if ($generator->pdf) {
        array_push($actions, "'pdf'");
    }
    if ($generator->importExcel) {
        array_push($actions, "'import'");
        array_push($actions, "'import-validate'");
        array_push($actions, "'import-excel'");
        array_push($actions, "'get-format'");
    }
    if ($generator->saveAsNew) {
        array_push($actions, "'save-as-new'");
    }
    foreach ($relations as $name => $rel) {
        if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)) {
            array_push($actions, "'" . \yii\helpers\Inflector::camel2id('add' . $rel[1]) . "'");
        }
    }
?>
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                                    <?= implode(",\n\t\t\t\t\t\t\t\t\t", $actions)?><?= "\n" ?>
                        ],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
<?php endif; ?>
        ];
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else {
    : ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif;
}
?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView(<?= $actionParams ?>)
    {
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
        $model = $this->findModel(<?= $actionParams ?>);
        $provider<?= $rel[1]?> = new ArrayDataProvider([
            'allModels' => $model-><?= $name ?>,
        ]);
<?php endif; ?>
<?php endforeach; ?>
        return $this->render('view', [
            'model' => $this->findModel(<?= $actionParams ?>),
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
            'provider<?= $rel[1]?>' => $provider<?= $rel[1]?>,
<?php endif; ?>
<?php endforeach; ?>
        ]);
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\db\Exception
     */
     public function actionCreate()
     {
         $model = new <?= $modelClass ?>();
         <?php if ($generator->hasFile()) { ?>
            $model->setScenario('insert');
         <?php } ?>
         if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [" . implode(", ", $skippedRelations) . "]" : ""; ?>)){
             if($model->saveAll(<?= !empty($generator->skippedRelations) ? "[" . implode(", ", $skippedRelations) . "]" : ""; ?>)) {
                 return $this->redirect(['view', <?= $urlParams ?>]);
             }
                 return $this->render('create', [
                 'model' => $model,
                 ]);
             }
             else {
                 return $this->render('create', [
                 'model' => $model,
                 ]);
             }
     }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
<?php if ($generator->saveAsNew) : ?>
        if (Yii::$app->request->post('_asnew') == '1') {
            $model = new <?= $modelClass ?>();
        }else{
            $model = $this->findModel(<?= $actionParams ?>);
        }
<?php else: ?>
        $model = $this->findModel(<?= $actionParams ?>);
         <?php if ($generator->hasFile()) { ?>
            $model->setScenario('update');
         <?php } ?>
<?php endif; ?>
        if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [" . implode(", ", $skippedRelations) . "]" : ""; ?>) && $model->saveAll(<?= !empty($generator->skippedRelations) ? "[" . implode(", ", $skippedRelations) . "]" : ""; ?>)) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $this->findModel(<?= $actionParams ?>)->deleteWithRelated();

        return $this->redirect(['index']);
    }
<?php if ($generator->pdf):?>
    /**
     * Export <?= $modelClass ?> information into PDF format.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPdf(<?= $actionParams ?>) {
        $model = $this->findModel(<?= $actionParams ?>);
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
        $provider<?= $rel[1] ?> = new ArrayDataProvider([
            'allModels' => $model-><?= $name; ?>,
        ]);
<?php endif; ?>
<?php endforeach; ?>
        $voucher = new <?=$modelClass?>PDF('P', 'mm', 'Letter');
        $voucher-><?=$modelClass?> = $model;
        $voucher->Body();
        $voucher->Output('I', "<?=$modelClass?>-{$id}.pdf", true);
        exit;
    }
<?php endif; ?>

<?php if ($generator->saveAsNew):?>
    /**
    * Creates a new <?= $modelClass ?> model by another data,
    * so user don't need to input all field from scratch.
    * If creation is successful, the browser will be redirected to the 'view' page.
    *
    * @param mixed $id
    * @return mixed
    * @throws NotFoundHttpException
    */
    public function actionSaveAsNew(<?= $actionParams; ?>) {
        $model = new <?= $modelClass ?>();

        if (Yii::$app->request->post('_asnew') != '1') {
            $model = $this->findModel(<?= $actionParams; ?>);
        }

        if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [" . implode(", ", $skippedRelations) . "]" : ""; ?>) && $model->saveAll(<?= !empty($generator->skippedRelations) ? "[" . implode(", ", $skippedRelations) . "]" : ""; ?>)) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        } else {
            return $this->render('saveAsNew', [
                'model' => $model,
            ]);
        }
    }
<?php endif; ?>

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(<?= $generator->generateString('La página solicitada no existe.')?>);
        }
    }
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>

    /**
    * Action to load a tabular form grid
    * for <?= $rel[1] . "\n" ?>
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    * @throws NotFoundHttpException    */
    public function actionAdd<?= $rel[1] ?>()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('<?= $rel[1] ?>');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_form<?= $rel[1] ?>', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(<?= $generator->generateString('La página solicitada no existe.')?>);
        }
    }
<?php endif; ?>
<?php endforeach; ?>

        /* Excel Zone */
    /**
     * @param int $id
     * @return bool|\yii\web\Response
     */
    public function actionGetFormat($format = false)
    {
        $excel = new ExcelHelper();
        try {
            $data = <?= $modelClass ?>::find()
                ->select([
<?php
    foreach ($generator->getColumnNames() as $name) {
        if (!in_array($name, $generator->skippedColumns)) {
                    echo "            '" . $name . "',\n";
        }
    }
?>
                ]);
            if ($format) {
                $data->where(['id' => -1]);
            }
            $excel->createExportTable(
                $data->asArray()->all(),
                [
<?php
    foreach ($generator->getColumnNames() as $key => $name) {
        if (!in_array($name, $generator->skippedColumns))
            echo "            ['coordinate' => '" . (new ExcelHelper())->getNameFromNumber($key + 1) . "1', 'title' => '" . $name . "'],\n";
    }
?>
                ]);
        $excel->autoSizeColumns([
<?php
    foreach ($generator->getColumnNames() as $key => $name) {
        if (!in_array($name, $generator->skippedColumns))
            echo "            '" . (new ExcelHelper())->getNameFromNumber($key + 1) . "',\n";
    }
?>
            ]);
            return $this->redirect($excel->saveExcel('files/formats', 'FormatoImportar<?= $modelClass ?>'));
        } catch (Exception $e) {
            return false;
        } catch (InvalidConfigException $e) {
            return false;
        }
    }
    /**
     * @return string
     */
    public function actionImport()
    {
        return $this->render('import');
    }
    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Exception
     */
    public function actionImportValidate()
    {
        $personal = new <?= $modelClass ?>();
        $personal->fileExcelImport = UploadedFile::getInstanceByName('fileExcelTest');
        $personal->fileExcelImport->saveAs('files/<?= $modelClass ?>/tmp_' . $personal->fileExcelImport->baseName . '_' . $personal->id . $personal->fileExcelImport->extension);
        $path = './files/<?= $modelClass ?>/tmp_' . $personal->fileExcelImport->baseName . '_' . $personal->id . $personal->fileExcelImport->extension;
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($path);
        $highestRow = $spreadsheet->getActiveSheet()->getHighestRow();
        $data = $spreadsheet->getActiveSheet()->rangeToArray('A1:U' . $highestRow, null, true, false);
        if ($this->extractData($data, true)) {
            Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, 'La información es correcta y se puede subir al sistema');
        } else {
            Yii::$app->session->setFlash(Alert::TYPE_ERROR, 'La información contiene errores favor de revisar');
        }
    }
    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Exception
     */
    public function actionImportExcel()
    {
        $personal = new <?= $modelClass ?>();
        $personal->fileExcelImport = UploadedFile::getInstanceByName('fileExcel');
        $personal->fileExcelImport->saveAs('files/<?= $modelClass ?>/' . $personal->fileExcelImport->baseName . '_' . $personal->id . $personal->fileExcelImport->extension);
        $path = './files/<?= $modelClass ?>/' . $personal->fileExcelImport->baseName . '_' . $personal->id . $personal->fileExcelImport->extension;
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($path);
        $highestRow = $spreadsheet->getActiveSheet()->getHighestRow();
        $data = $spreadsheet->getActiveSheet()->rangeToArray('A1:U' . $highestRow, null, true, false);
        if ($this->extractData($data)) {
            Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, 'La información fue ingresada al sistema de forma correcta');
        } else {
            Yii::$app->session->setFlash(Alert::TYPE_ERROR, 'La información contiene errores favor de revisar');
        }
    }
    /**
     * @param $data
     * @param bool $test
     * @return array|bool
     */
    private function extractData($data, $test = false)
    {
        Yii::debug('Data to import to movements' . Json::encode($data), GoogleCloudLogger::INVENTARIOS_LOG);
        unset($data[0]);
        foreach ($data as $datum) {
            $personal = <?= $modelClass ?>::find()->where(['id' => (int)$datum[0]])->one();
            if ($personal === null) {
                $personal = new <?= $modelClass ?>();
            }
<?php
    foreach ($generator->getColumnNames() as $key => $name) {
        if (!in_array($name, $generator->skippedColumns)) {
                    echo "              \$personal->{$name} = (string)\$datum[{$key}];\n";
        }
    }
?>
            if ($test) {
                if (!$personal->validate()) {
                    Yii::debug('Errors' . Json::encode($personal->getErrors()));
                    return false;
                }
                return true;
            }
            if (!$personal->save()) {
                Yii::debug($personal->getErrors());
                return false;
            }
        }
        return true;
    }
        //END EXCEL Zone
}
