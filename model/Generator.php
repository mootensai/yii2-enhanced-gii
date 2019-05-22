<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace inquid\enhancedgii\model;

use inquid\enhancedgii\BaseGenerator;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;

/**
 * Generates CRUD
 *
 * @property array $columnNames Model column names. This property is read-only.
 * @property string $controllerID The controller ID (without the module ID prefix). This property is
 * read-only.
 * @property array $searchAttributes Searchable attributes. This property is read-only.
 * @property string $viewPath The controller view path. This property is read-only.
 * @property TableSchema $tableSchema The TableSchema of this model.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends BaseGenerator
{

    /* @var $tableSchema TableSchema */

    public $nsModel = 'app\models';
    public $nameAttribute = 'name, title, username, nombre';
    public $hiddenColumns = 'id, lock';
    public $skippedTables = 'auth_assignment, auth_item, auth_item_child, auth_rule, token,social_account, user, profile, migration';
    public $skippedColumns = 'created_at, updated_at, created_by, updated_by, deleted_at, deleted_by, created, modified, deleted';
    public $generateQuery = true;
    public $queryNs = 'app\models';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $generateRelations = self::RELATIONS_ALL;
    public $generateAttributeHints = false;
    public $generateMigrations = false;
    public $optimisticLock = 'lock';
    public $createdAt = 'created_at';
    public $updatedAt = 'updated_at';
    public $timestampValue = "new \\yii\\db\\Expression('CONVERT_TZ(NOW(),\"+00:00\",\"-05:00\")')";
    public $createdBy = 'created_by';
    public $updatedBy = 'updated_by';
    public $blameableValue = '\Yii::$app->user->id';
    public $deletedBy = 'deleted_by';
    public $deletedByValue = '\Yii::$app->user->id';
    public $deletedByValueRestored = '0';
    public $deletedAt = 'deleted_at';
    public $deletedAtValue = 'date(\'Y-m-d H:i:s\')';
    public $deletedAtValueRestored = 'date(\'Y-m-d H:i:s\')';
    public $generateBaseOnly = false;
    public $UUIDColumn = 'id';
    public $skipAllExistingTables = true;
    public $nsComponent = 'app\components';
    // Uploaders
    public $excelImport = true;
    public $imageExtensions = 'png, jpg';
    public $videoExtensions = 'mp4, mpg, mpeg';
    public $pdfExtensions = 'pdf';
    public $wordExtensions = 'doc, docx';
    public $excelExtensions = 'xls, csv, xlsx';
    public $powerPointExtensions = 'ppt, pptx';
    public $genericFileExtensions = 'txt, sh';
    public $componentClass;
    public $fileFields = [];


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'INQUID Generator (Model)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates model operations for the database.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'nsModel', 'tableName', 'modelClass', 'queryNs', 'nsComponent'], 'filter', 'filter' => 'trim'],
            [['tableName', 'db'], 'required'],
            [['tableName', 'moduleName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['tableName'], 'validateTableName'],
            [['nsModel', 'baseModelClass', 'queryNs', 'queryBaseClass'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['modelClass', 'baseModelClass', 'db'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['queryBaseClass', 'queryClass'], 'validateClass', 'params' => ['extends' => ActiveQuery::className()]],
            [['db'], 'validateDb'],
            [['enableI18N', 'generateQuery', 'generateLabelsFromComments',
                'useTablePrefix', 'generateMigrations', 'generateAttributeHints', 'generateBaseOnly', 'excelImport'], 'boolean'],
            [['generateRelations'], 'in', 'range' => [self::RELATIONS_NONE, self::RELATIONS_ALL, self::RELATIONS_ALL_INVERSE]],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],

            [['skippedTables', 'skippedColumns', 'skippedRelations',
                'blameableValue', 'nameAttribute', 'hiddenColumns', 'timestampValue',
                'optimisticLock', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy',
                'blameableValue', 'UUIDColumn', 'deletedBy', 'deletedByValue', 'deletedAt', 'deletedAtValue'], 'safe'],
            [['imageExtensions', 'videoExtensions', 'excelImport', 'wordExtensions', 'powerPointExtensions', 'pdfExtensions', 'genericFileExtensions'], 'string']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'db' => 'Database Connection ID',
            'moduleName' => 'Module Name',
            'modelClass' => 'Model Class',
            'timestampValue' => 'Value',
            'blameableValue' => 'Value',
            'generateQuery' => 'Generate ActiveQuery',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'nsModel' => 'Model Namespace',
            'nsSearchModel' => 'Search Model Namespace',
            'UUIDColumn' => 'UUID Column',
            'viewPath' => 'View Path',
//            'baseControllerClass' => 'Base Controller Class',
//            'indexWidgetType' => 'Widget Used in Index Page',
//            'searchModelClass' => 'Search Model Class',
            'generateBaseOnly' => 'Generate Base Model Only',
            'deletedBy' => 'Column',
            'deletedByValue' => 'Value',
            'deletedByValueRestored' => 'Column Restored Value',
            'deletedAt' => 'Info Column',
            'deletedAtValue' => 'Info Value',
            'deletedAtValueRestored' => 'Info Restored Value',
            'excelImport' => 'Import From Excel',
            'imageExtensions' => 'Accepted Image Extensions',
            'videoExtensions' => 'Accepted Video Extensions',
            'wordExtensions' => 'Accepted Word Extensions',
            'excelExtensions' => 'Accepted Excel Extensions',
            'powerpointExtensions' => 'Accepted PowerPoint Extensions',
            'genericFileExtensions' => 'Generic files accepted',
            'nsComponent' => 'Component Namespace',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'db' => 'This is the ID of the DB application component.',
            'moduleName' => 'The module where the models will be placed',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>. In this case, multiple ActiveRecord classes
                will be generated, one for each matching table name; and the class names will be generated from
                the matching characters. For example, table <code>tbl_post</code> will generate <code>Post</code>
                class.',
            'nameAttribute' => 'This is the (set) of name column that you use to show as label, '
                . 'separated by comma (,) for multiple table(asterisk on Table Name).',
            'skippedColumns' => 'Fill this field with the column name that you dont want to generate form & labels for the table.
                You can fill multiple columns, separated by comma (,). You may specify the column name
                although "Table Name" ends with asterisk, in which case all columns will not be generated at all models & CRUD.',
            'skippedTables' => 'Fill this field with the table name that you dont want to generate files.
                You can fill multiple tables, separated by comma (,).',
            'skippedRelations' => 'Fill this field with the relation name that you dont want to generate CRUD for the table.
                You can fill multiple relations, separated by comma (,). You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case all relations will be generated.',
            'hiddenColumns' => 'Fill this field with the column name that you want to generate form with the hidden field of the table.
                You can fill multiple columns, separated by comma (,). You may specify the column name
                although "Table Name" ends with asterisk, in which case all columns will be generated with hidden field at the forms',
            'nsModel' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'modelClass' => 'This is the name of the Model class to be generated. The class name should not contain
                the namespace part as it is specified in "Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'baseModelClass' => 'This is the base class of the new ActiveRecord class. It should be a fully qualified namespaced class name.',
            'nsSearchModel' => 'This is the namespace of the search model class to be generated, e.g., <code>app\models</code>',
//            'searchModelClass' => 'This is the name of the search class to be generated. The class name should not contain
//                the namespace part as it is specified in "Search Model Namespace". You do not need to specify the class name
//                if "Table Name" ends with asterisk, in which case multiple search model classes will be generated.',
            'generateQuery' => 'This indicates whether to generate ActiveQuery for the ActiveRecord class.',
            'generateLabelsFromComments' => 'This indicates whether the generator should generate attribute labels
                by using the comments of the corresponding DB columns.',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated ActiveRecord class
                should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the
                table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the ActiveRecord class
                will return the table name as <code>{{%post}}</code>.',
            'generateRelations' => 'This indicates whether the generator should generate relations based on
                foreign key constraints it detects in the database. Note that if your database contains too many tables,
                you may want to uncheck this option to accelerate the code generation process.',
            'generateAttributeHints' => 'This indicates whether the generator generate attribute hints on the extended models',
            'generateMigrations' => 'This indicates whether the generator should generate migrations based on
                table structure.',
            'optimisticLock' => 'This indicates whether the generator should generate optimistic lock feature for Model. '
                . 'Enter this field with optimistic lock column name. '
                . 'Empty this field if you want to disable this feature.',
            'createdAt' => 'This indicates whether the generator should generate Timestamp Behaviors feature for Model. '
                . 'Enter this field with Created At column name. '
                . 'Empty <code>Created At</code> & <code>Updated At</code> field if you want to disable this feature.',
            'updatedAt' => 'This indicates whether the generator should generate Timestamp Behaviors feature for Model. '
                . 'Enter this field with Updated At column name. '
                . 'Empty <code>Created At</code> & <code>Updated At</code> field if you want to disable this feature.',
            'timestampValue' => 'This will generate the <code>value</code> configuration entry for Timestamp Behaviors.  e.g., <code>new Expression(\'NOW()\')</code>',
            'createdBy' => 'This indicates whether the generator should generate Blameable Behaviors feature for Model. '
                . 'Enter this field with Created By column name. '
                . 'Empty <code>Created By</code> & <code>Updated By</code> field if you want to disable this feature.',
            'updatedBy' => 'This indicates whether the generator should generate Blameable Behaviors feature for Model. '
                . 'Enter this field with Updated By column name. '
                . 'Empty <code>Created By</code> & <code>Updated By</code> field if you want to disable this feature.',
            'blameableValue' => 'This will generate the </code>value</code> configuration entry for Blameable Behaviors.  e.g., <code>new Expression(\'NOW()\')</code>',
            'UUIDColumn' => 'This indicates whether the generator should generate UUID Behaviors feature for Model. '
                . 'Enter this field with UUID column name. '
                . 'Empty <code>UUID Column</code> field if you want to disable this feature.',
            'deletedBy' => 'This indicates whether the generator should generate Soft Delete feature for Model or not. ' .
                'Enter this field with column name to tell whether row is deleted or not. ' .
                'For example, You could use <code>is_deleted</code> for boolean value, or you could use my default value example. ' .
                'If <code>Column</code> field is empty, then <code>Soft Deletion will not run!</code> ',
            'deletedByValue' => 'This will generate the <code>value</code> marker entry for Soft Delete feature for model ' .
                'Enter this field with value to give info like <code>1</code> or even <code>date(\'Y-m-d H:i:s\')</code>. ' .
                'This entry will not be quoted by <code>\' \'</code>. Default <code>value</code> is <code>1</code>' .
                'Empty <code>Column</code> field if you want to disable this feature. ',
            'deletedByValueRestored' => 'This will generate the <code>value</code> marker for entry that is not deleted ' .
                'Enter this field with simple value like <code>0</code>. ' .
                'Because this field will be called everytime you run query from <code>find()</code>.' .
                'Empty <code>Column</code> field if you want to disable this feature. Default <code>value</code> is <code>0</code>' .
                'If <code>Column</code> field is empty, then this field will not work!',
            'deletedAt' => 'This give additional info for deleted row of Model. ' .
                'You could add more info by manually adding <code>$_rt_softdelete</code> array value at base model.' .
                'Enter this field with column name to store additional info. ' .
                'Empty this field if you don\'t want to add additional info and disable this feature. ' .
                'This field only work when <code>Column</code> field is not empty ',
            'deletedAtValue' => 'Enter this field with additional <code>value</code> to be saved. You could enter PHP function here. ' .
                'This entry will not be quoted by <code>\' \'</code>. ' .
                'Empty <code>Info Column</code> field if you want to disable this feature. ' .
                'If <code>Column</code> field is empty, then this field will not work!',
            'deletedAtValueRestored' => 'This will generate the <code>value</code> information when restored. ' .
                'Enter this field with value to give info like <code>\Yii::$app->user->id</code> ' .
                'or even <code>date(\'Y-m-d H:i:s\')</code> to know when it is restored or by who. ' .
                'This entry will not be quoted by <code>\' \'</code>. ' .
                'Empty <code>Info Column</code> field if you want to disable this feature. ' .
                'If <code>Column</code> field is empty, then this field will not work!',

//            'controllerClass' => 'This is the name of the Controller class to be generated. The class name should not contain
//                the namespace part as it is specified in "Controller Namespace". You do not need to specify the class name
//                if "Table Name" ends with asterisk, in which case multiple Controller classes will be generated.',
            'viewPath' => 'Specify the directory for storing the view scripts for the controller. You may use path alias here, e.g.,
                <code>/var/www/basic/controllers/views/post</code>, <code>@app/views/post</code>. If not set, it will default
                to <code>@app/views/ControllerID</code>',
//            'baseControllerClass' => 'This is the class that the new CRUD controller class will extend from.
//                You should provide a fully qualified class name, e.g., <code>yii\web\Controller</code>.',
//            'skippedRelations' => 'Fill this field with the relation name that you dont want to generate CRUD for the table.
//                You can fill multiple relations, separated by comma (,). You do not need to specify the class name
//                if "Table Name" ends with asterisk, in which case all relations will be generated.',
//            'indexWidgetType' => 'This is the widget type to be used in the index page to display list of the models.
//                You may choose either <code>GridView</code> or <code>ListView</code>',
            'queryNs' => 'This is the namespace of the ActiveQuery class to be generated, e.g., <code>app\models</code>',
            'queryClass' => 'This is the name of the ActiveQuery class to be generated. The class name should not contain
                the namespace part as it is specified in "ActiveQuery Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveQuery classes will be generated.',
            'queryBaseClass' => 'This is the base class of the new ActiveQuery class. It should be a fully qualified namespaced class name.',
            'generateBaseOnly' => 'This indicates whether the generator should generate extended model(where you write your code) or not. '
                . 'You usually re-generate models when you make changes on your database.',
            'excelImport' => 'Prepare the model to be filled from a Excel Sheet',
            'imageExtensions' => 'Accepted image extensions, should be spearated by a comma',
            'videoExtensions' => 'Accepted video extensions, should be spearated by a comma',
            'pdfExtensions' => 'Accepted PDF extensions, should be spearated by a comma',
            'excelExtensions' => 'Accepted Excel extensions, should be spearated by a comma',
            'wordExtensions' => 'Accepted Word extensions, should be spearated by a comma',
            'powerpointExtensions' => 'Accepted PowerPoint extensions, should be spearated by a comma',
            'genericFileExtensions' => 'Files that should be accepted using the @file annotation at the column comments',
            'nsComponent' => 'This is the namespace of the Components class to be generated, e.g., <code>app\components</code>'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), [
            'db',
//            'skippedColumns',
//            'hiddenColumns',
            'nameAttribute',
            'nsModel',
            'nsSearchModel',
            'baseModelClass',
            'queryNs',
            'queryBaseClass',
            'optimisticLock',
            'createdBy',
            'updatedBy',
            'createdAt',
            'timestampValue',
            'updatedAt',
            'blameableValue',
            'deletedAt',
            'deletedAtValue',
            'deletedAtValueRestored',
            'deletedBy',
            'deletedByValue',
            'deletedByValueRestored',
            'UUIDColumn',
            'generateRelations'
//            'baseControllerClass',
//            'indexWidgetType',
//            'viewPath'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'component.php'];
    }

    public $isTree;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();

        if (isset($this->moduleName)) {
            $moduleGenerator = new \inquid\enhancedgii\module\Generator();
            $moduleGenerator->moduleClass = "app\modules\\$this->moduleName\Module";
            $moduleGenerator->moduleID = $this->moduleName;
            $moduleGenerator->generate();
            $this->nsModel = "app\modules\\$this->moduleName\models";
            $this->nsSearchModel = "app\modules\\$this->moduleName\models\search";
            $this->queryNs = "app\modules\\$this->moduleName\models\ActiveQuery";
            $this->nsComponent = "app\modules\\$this->moduleName\components";
        }

        $this->nameAttribute = ($this->nameAttribute) ? explode(',', str_replace(' ', '', $this->nameAttribute)) : [];
        $this->skippedTables = ($this->skippedTables) ? explode(',', str_replace(' ', '', $this->skippedTables)) : [];
        $this->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [];
        $this->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [$this->skippedRelations];
        $this->skippedTables = array_filter($this->skippedTables);
        $this->skippedColumns = array_filter($this->skippedColumns);
        $this->skippedRelations = array_filter($this->skippedRelations);
//        $this->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [];

        /* TODO get all the generated tables on the database
         * if ($this->skipAllExistingTables) {
            foreach ($this->getTableNames() as $tableName) {
                Yii::debug("Table skipped " . $tableName);
                array_push($this->skippedTables, $tableName);
            }
        }*/

        foreach ($this->getTableNames() as $tableName) {

            if (in_array($tableName, $this->skippedTables)):
                continue;
            endif;

            // preparation :
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
            } else {
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
            }
            $componentClassName = $modelClassName . 'Component';
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $this->modelClass = "{$this->nsModel}\\{$modelClassName}";
            $this->componentClass = "{$this->nsComponent}\\{$componentClassName}";

            $this->tableSchema = $tableSchema;
            $this->isTree = !array_diff(self::getTreeColumns(), $tableSchema->columnNames);
//            $this->controllerClass = $this->nsController . '\\' . $modelClassName . 'Controller';
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'componentClass' =>$this->componentClass,
                'componentClassName' => $componentClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'isTree' => $this->isTree
            ];
            // model :
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->nsModel)) . '/base/' . $modelClassName . '.php', $this->render('model.php', $params)
            );
            if (!$this->generateBaseOnly) {
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->nsModel)) . '/' . $modelClassName . '.php', $this->render('model-extended.php', $params)
                );
            }
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->nsComponent)) . '/' . $componentClassName . '.php', $this->render('component.php', $params)
            );
            // query :
            if ($queryClassName) {
                $params = [
                    'className' => $queryClassName,
                    'modelClassName' => $modelClassName,
                ];
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php', $this->render('query.php', $params)
                );
            }

            if (strpos($this->tableName, '*') !== false) {
                $this->modelClass = '';
//                $this->controllerClass = '';
            } else {
                $this->modelClass = $modelClassName;
//                $this->controllerClass = $modelClassName . 'Controller';
            }
        }
        $this->nameAttribute = (is_array($this->nameAttribute)) ? implode(', ', $this->nameAttribute) : '';
        $this->skippedTables = (is_array($this->skippedTables)) ? implode(', ', $this->skippedTables) : '';
        $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
        $this->skippedRelations = (is_array($this->skippedRelations)) ? implode(', ', $this->skippedRelations) : '';

        return $files;
    }

    /**
     * Checks if any of the specified columns is auto incremental.
     * @param \yii\db\TableSchema $table the table schema
     * @param array $columns columns to check for autoIncrement property
     * @return boolean whether any of the specified columns is auto incremental.
     */
    protected function isColumnAutoIncremental($table, $columns)
    {
        foreach ($columns as $column) {
            if (isset($table->columns[$column]) && $table->columns[$column]->autoIncrement) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $this->removeAnnotation($column->comment);
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                /*if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }*/
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates a query class name from the specified model class name.
     * @param string $modelClassName model class name
     * @return string generated class name
     */
    protected function generateQueryClassName($modelClassName)
    {
        $queryClassName = $this->queryClass;
        if (empty($queryClassName) || strpos($this->tableName, '*') !== false) {
            $queryClassName = $modelClassName . 'Query';
        }
        return $queryClassName;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        $this->fileFields = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                if ($this->isTree && in_array($column->name, ['lft', 'rgt', 'lvl'])) {

                } else {
                    $types['required'][] = $column->name;
                }
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case 'double': // Schema::TYPE_DOUBLE, which is available since Yii 2.0.3
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        if ($this->containsAnnotation($column, "@file")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "File";
                        } elseif ($this->containsAnnotation($column, "@image")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "Image";
                        } elseif ($this->containsAnnotation($column, "@video")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "Video";
                        } elseif ($this->containsAnnotation($column, "@pdf")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "Pdf";
                        } elseif ($this->containsAnnotation($column, "@excel")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "Excel";
                        } elseif ($this->containsAnnotation($column, "@word")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "Word";
                        } elseif ($this->containsAnnotation($column, "@powerpoint")) {
                            $this->fileFields[] = $column->name;
                            $types['file'][] = $column->name . "Powerpoint";
                        }
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        $rules = [];
        /* TODO column is not avilable at this point */
        foreach ($types as $type => $columns) {
            if ($this->containsAnnotation($column, "@file")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->genericFileExtensions}', 'on' => 'insert', 'update']";
            } elseif ($this->containsAnnotation($column, "@image")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->imageExtensions}', 'on' => 'insert', 'update']";
            } elseif ($this->containsAnnotation($column, "@video")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->videoExtensions}', 'on' => 'insert', 'update']";
            } elseif ($this->containsAnnotation($column, "@pdf")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->pdfExtensions}', 'on' => 'insert', 'update']";
            } elseif ($this->containsAnnotation($column, "@excel")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->excelExtensions}', 'on' => 'insert', 'update']";
            } elseif ($this->containsAnnotation($column, "@word")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->wordExtensions}', 'on' => 'insert', 'update']";
            } elseif ($this->containsAnnotation($column, "@powerpoint")) {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','extensions'=>'{$this->powerPointExtensions}', 'on' => 'insert', 'update']";
            } else {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
            }
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        // Unique indexes rules
        try {
            $db = $this->getDbConnection();
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount == 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['" . $columnsList . "'], 'unique', 'targetAttribute' => ['" . $columnsList . "'], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']";
                    }
                }
            }
            if (!empty($this->optimisticLock)) {
                $rules[] = "[['" . $this->optimisticLock . "'], 'default', 'value' => '0']";
                $rules[] = "[['" . $this->optimisticLock . "'], 'mootensai\\components\\OptimisticLockValidator']";
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        return $rules;
    }

    /**
     * @param $column
     * @param $annotation
     * @return bool
     */
    public function containsAnnotation($column, $annotation)
    {
        if (substr($column->comment, 0, 1) !== "@")
            return false;
        return substr($column->comment, 0, strlen($annotation)) === $annotation;
    }

    /**
     * @param $comment
     * @return mixed
     */
    public function removeAnnotation($comment)
    {
        if (substr($comment, 0, 1) !== "@")
            return $comment;
        if (substr($comment, 0, 5) === "@file") {
            return str_replace("@file", "", $comment);
        } elseif (substr($comment, 0, 6) === "@image") {
            return str_replace("@image", "", $comment);
        }
        return $comment;
    }
}
