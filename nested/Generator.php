<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace mootensai\enhancedgii\nested;

use \Yii;
use \yii\gii\CodeFile;
use \yii\helpers\Inflector;
/**
 * Description of Generator
 *
 * @author Yohanes
 */
class Generator extends \yii\gii\generators\model\Generator{
    public $tableSchema;
    
    public $nsModel = 'app\models';
    public $nsController = 'app\controllers';
    public $baseControllerClass = 'yii\web\Controller';
    public $controllerClass;
    public $searchModelClass;
    public $generateRelations = 0;
    public $optimisticLock = 'lock';
    public $skippedColumns = 'created_at, updated_at, created_by, updated_by, deleted_at, deleted_by, created, modified, deleted';


    public function getName() {
        return 'IO Generator (Nested)';
    }
    
    public function getDescription() {
        return 'This generator generates Model & CRUD for nested table by <a href="https://github.com/creocoder/yii2-nested-sets">creocoder</a>'
        . ' & <a href="http://demos.krajee.com/tree-manager">kartik-v Tree Manager</a> STILL ON DEVELOPMENT! NOT WORKING YET!<br />'
        . 'If you use this generator, then you must add these code to your <code>modules</code> configuration : <br />'
        . "<div class='col-md-8'><pre>'modules' => [
    ... //your other module
    'treemanager' =>  [
        'class' => '\kartik\\tree\\Module'
    ]
    ... //your other module
]</pre></div>";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template'], 'required', 'message' => 'A code template must be selected.'],
            [['template'], 'validateTemplate'],
            [['db', 'nsModel', 'tableName', 'modelClass', 'controllerClass', 'queryNs', 'queryClass', 'queryBaseClass'], 'filter', 'filter' => 'trim'],
            [['ns', 'queryNs'], 'filter', 'filter' => function($value) { return trim($value, '\\'); }],
            [['db', 'nsModel', 'nsController', 'tableName', 'queryNs', 'queryBaseClass'], 'required'],
            [['db', 'modelClass', 'controllerClass', 'queryClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['nsModel', 'queryNs', 'queryBaseClass'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['db'], 'validateDb'],
            [['ns', 'queryNs'], 'validateNamespace'],
            [['tableName'], 'validateTableName'],
            [['queryBaseClass'], 'validateClass', 'params' => ['extends' => \yii\db\ActiveQuery::className()]],
            [['generateRelations', 'generateLabelsFromComments', 'useTablePrefix', 'useSchemaName', 'generateQuery'], 'boolean'],
            [['enableI18N'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['optimisticLock'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'nsModel' => 'Model Namespace',
            'nsController' => 'Controller Namespace',
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function hints() {
        return array_merge(parent::hints(), [
            'nsModel' => 'This is the namespace of the Model class to be generated, e.g., <code>app\models</code>',
            'modelClass' => 'This is the name of the Model class to be generated. The class name should not contain
                the namespace part as it is specified in "Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple Model classes will be generated.',
            'nsController' => 'This is the namespace of the Controller class to be generated, e.g., <code>app\controllers</code>',
            'controllerClass' => 'This is the name of the Controller class to be generated. The class name should not contain
                the namespace part as it is specified in "Controller Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple Controller classes will be generated.',
            
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function stickyAttributes() {
        return array_merge(
                parent::stickyAttributes(), [
                    'nsModel',
                    'nsController',
                    'skippedColumns',
                    'optimisticLock'
                ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        // @todo make 'query.php' to be required before 2.1 release
        return ['model.php'/*, 'query.php'*/];
    }


    public function generate() {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        $this->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [];
        
        foreach($this->getTableNames() as $tableName){
            // preparation :
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
                $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            }else{
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
                $queryClassName = (!empty($this->queryClass)) ? $this->generateQueryClassName($this->queryClass) : $this->generateQueryClassName($modelClassName);
            }
            Yii::trace("echoing query classname ".$queryClassName, __METHOD__);
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
            }else{
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
            }
            
            $this->tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $this->tableSchema,
                'labels' => $this->generateLabels($this->tableSchema),
                'rules' => $this->generateRules($this->tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            // model :
            $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->nsModel)) . '/base/' . $modelClassName . '.php', $this->render('model.php', $params)
            );
            $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->nsModel)) . '/' . $modelClassName . '.php', $this->render('model-extended.php', $params)
            );
            //query : 
            if ($queryClassName) {
                $params = [
                    'className' => $queryClassName,
                    'modelClassName' => $modelClassName,
                ];
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }
            // controller :
            $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->nsController)) . '/' . $modelClassName . 'Controller.php', $this->render('controller.php', [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ])
            );
            //views :
            $viewPath = $this->getViewPath();
            $templatePath = $this->getTemplatePath() . '/views';
            $files[] = new CodeFile("$viewPath/index.php", $this->render("index.php", $params)
            );
            $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
        }
        return $files;
    }

    // schmunk42's code from https://github.com/schmunk42/yii2-giiant/blob/master/model/Generator.php
    
    /**
     * @var null string for the table prefix, which is ignored in generated class name
     */
    public $tablePrefix = null;

    /**
     * @var array key-value pairs for mapping a table-name to class-name, eg. 'prefix_FOObar' => 'FooBar'
     */
    public $tableNameMap = [];
    protected $classNames2;
    /**
     * Generates a class name from the specified table name.
     *
     * @param string $tableName the table name (which may contain schema prefix)
     *
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {

        #Yii::trace("Generating class name for '{$tableName}'...", __METHOD__);
        if (isset($this->classNames2[$tableName])) {
            #Yii::trace("Using '{$this->classNames2[$tableName]}' for '{$tableName}' from classNames2.", __METHOD__);
            return $this->classNames2[$tableName];
        }

        if (isset($this->tableNameMap[$tableName])) {
            Yii::trace("Converted '{$tableName}' from tableNameMap.", __METHOD__);
            return $this->classNames2[$tableName] = $this->tableNameMap[$tableName];
        }

        if (($pos = strrpos($tableName, '.')) !== false) {
            $tableName = substr($tableName, $pos + 1);
        }

        $db         = $this->getDbConnection();
        $patterns   = [];
        $patterns[] = "/^{$this->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$this->tablePrefix}$/";
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";

        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }

        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                Yii::trace("Mapping '{$tableName}' to '{$className}' from pattern '{$pattern}'.", __METHOD__);
                break;
            }
        }

        $returnName = Inflector::id2camel($className, '_');
        Yii::trace("Converted '{$tableName}' to '{$returnName}'.", __METHOD__);
        return $this->classNames2[$tableName] = $returnName;
    }

    protected function generateRelations()
    {
        $relations = parent::generateRelations();

        // inject namespace
        $ns = "\\{$this->nsModel}\\";
        foreach ($relations AS $model => $relInfo) {
            foreach ($relInfo AS $relName => $relData) {

                $relations[$model][$relName][0] = preg_replace(
                    '/(has[A-Za-z0-9]+\()([a-zA-Z0-9]+::)/',
                    '$1__NS__$2',
                    $relations[$model][$relName][0]
                );
                $relations[$model][$relName][0] = str_replace('__NS__', $ns, $relations[$model][$relName][0]);
            }
        }
        return $relations;
    }
    
    //from CRUD Generator : 

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams() {
        $pks = $this->tableSchema->primaryKey;
        if (count($pks) === 1) {
            if (is_subclass_of($this->modelClass, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($this->modelClass, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams() {
        $pks = $this->tableSchema->primaryKey;
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments() {
        /* @var $class ActiveRecord */
        $pks = $this->tableSchema->primaryKey;
        if (($table = $this->tableSchema) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class);
    }

    /**
     * @return string the controller view path
     */
    public function getViewPath() {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerID());
        } else {
            return Yii::getAlias($this->viewPath . '/' . $this->getControllerID());
        }
    }
}
