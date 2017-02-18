<?php

/**
 * This is the template for generating a CRUD controller class file.
 */
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

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
$skippedRelations = array_map(function($value){
    return "'$value'";
},$generator->skippedRelations);
echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else : ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
<?php if ($generator->loggedUserOnly):
    $actions = ["'index'", "'view'", "'create'", "'update'","'delete'"];
    if($generator->pdf){
        array_push($actions,"'pdf'");
    }
    if($generator->saveAsNew){
        array_push($actions,"'save-as-new'");
    }
    foreach ($relations as $name => $rel){
        if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)){
            array_push($actions,"'".\yii\helpers\Inflector::camel2id('add'.$rel[1])."'");
        }
    }
?>
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [<?= implode(', ',$actions)?>],
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
<?php else : ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
        $provider<?= $rel[1]?> = new \yii\data\ArrayDataProvider([
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
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();

        if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [".implode(", ", $skippedRelations)."]" : ""; ?>) && $model->saveAll(<?= !empty($generator->skippedRelations) ? "[".implode(", ", $skippedRelations)."]" : ""; ?>)) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        } else {
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
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
<?php if($generator->saveAsNew) : ?>
        if (Yii::$app->request->post('_asnew') == '1') {
            $model = new <?= $modelClass ?>();
        }else{
            $model = $this->findModel(<?= $actionParams ?>);
        }

<?php else: ?>
        $model = $this->findModel(<?= $actionParams ?>);

<?php endif; ?>
        if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [".implode(", ", $skippedRelations)."]" : ""; ?>) && $model->saveAll(<?= !empty($generator->skippedRelations) ? "[".implode(", ", $skippedRelations)."]" : ""; ?>)) {
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
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $this->findModel(<?= $actionParams ?>)->deleteWithRelated();

        return $this->redirect(['index']);
    }
<?php if ($generator->pdf):?>    
    /**
     * 
     * Export <?= $modelClass ?> information into PDF format.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionPdf(<?= $actionParams ?>) {
        $model = $this->findModel(<?= $actionParams ?>);
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
        $provider<?= $rel[1] ?> = new \yii\data\ArrayDataProvider([
            'allModels' => $model-><?= $name; ?>,
        ]);
<?php endif; ?>
<?php endforeach; ?>

        $content = $this->renderAjax('_pdf', [
            'model' => $model,
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
            'provider<?= $rel[1]?>' => $provider<?= $rel[1] ?>,
<?php endif; ?>
<?php endforeach; ?>
        ]);

        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_CORE,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => \Yii::$app->name],
            'methods' => [
                'SetHeader' => [\Yii::$app->name],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }
<?php endif; ?>

<?php if($generator->saveAsNew):?>
    /**
    * Creates a new <?= $modelClass ?> model by another data,
    * so user don't need to input all field from scratch.
    * If creation is successful, the browser will be redirected to the 'view' page.
    *
    * @param mixed $id
    * @return mixed
    */
    public function actionSaveAsNew(<?= $actionParams; ?>) {
        $model = new <?= $modelClass ?>();

        if (Yii::$app->request->post('_asnew') != '1') {
            $model = $this->findModel(<?= $actionParams; ?>);
        }
    
        if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [".implode(", ", $skippedRelations)."]" : ""; ?>) && $model->saveAll(<?= !empty($generator->skippedRelations) ? "[".implode(", ", $skippedRelations)."]" : ""; ?>)) {
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
            throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.')?>);
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
    */
    public function actionAdd<?= $rel[1] ?>()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('<?= $rel[1] ?>');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_form<?= $rel[1] ?>', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.')?>);
        }
    }
<?php endif; ?>
<?php endforeach; ?>
}
