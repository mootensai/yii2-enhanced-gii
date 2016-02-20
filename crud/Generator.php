<?php
namespace mootensai\enhancedgii\crud;

use \Yii;
use \yii\base\Model;
use \yii\base\NotSupportedException;
use \yii\db\ActiveRecord;
use \yii\db\ColumnSchema;
use \yii\db\Connection;
use \yii\db\Schema;
use \yii\db\TableSchema;
use \yii\gii\CodeFile;
use \yii\helpers\Inflector;
use yii\helpers\StringHelper;
use \yii\helpers\VarDumper;
use \yii\web\Controller;

/**
 * Generates Relational CRUD
 *
 *
 * @author Yohanes Candrajaya <moo.tensai@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\Generator
{
    /* @var $tableSchema TableSchema */

    public $db = 'db';
    public $tableSchema;
    public $tableName;
    public $nsTraits = 'app\traits';
    public $nameAttribute = 'name, title';
    public $hiddenColumns = 'id, lock';
    public $skippedColumns = 'created_at, updated_at, created_by, updated_by, deleted_at, deleted_by, created, modified, deleted';
    public $nsModel = 'app\models';
    public $modelClass;
    public $baseModelClass = 'yii\db\ActiveRecord';
    public $nsSearchModel = 'app\models';
    public $generateSearchModel;
    public $searchModelClass;
    public $generateQuery = true;
    public $queryNs = 'app\models';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $generateRelations = true;
    public $generateMigrations = true;
    public $optimisticLock = 'lock';
    public $createdAt = 'created_at';
    public $updatedAt = 'updated_at';
    public $timestampValue = "new Expression('NOW()')";
    public $createdBy = 'created_by';
    public $updatedBy = 'updated_by';
    public $blameableValue = 'Yii::\$app->user->id';
    public $UUIDColumn = 'id';
    public $deletedBy = 'deleted_by';
    public $deletedAt = 'deleted_at';
    public $nsController = 'app\controllers';
    public $controllerClass;
    public $pluralize;
    public $loggedUserOnly;
    public $expandable;
    public $cancelable;
    public $pdf;
    public $viewPath = '@app/views';
    public $baseControllerClass = 'yii\web\Controller';
    public $indexWidgetType = 'grid';
    public $skippedRelations;
    public $relations;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'IO Generator (CRUD)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the database.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'nsModel', 'viewPath', 'queryNs', 'nsController', 'nsSearchModel', 'tableName', 'modelClass', 'searchModelClass', 'baseControllerClass'], 'filter', 'filter' => 'trim'],
            [['tableName', 'baseControllerClass', 'indexWidgetType', 'db'], 'required'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['tableName'], 'validateTableName'],
//            [['searchModelClass'], 'compare', 'compareAttribute' => 'modelClass', 'operator' => '!==', 'message' => 'Search Model Class must not be equal to Model Class.'],
            [['modelClass', 'baseControllerClass', 'searchModelClass', 'db', 'queryClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
//            [['modelClass'], 'validateClass', 'params' => ['extends' => BaseActiveRecord::className()]],
            [['baseControllerClass'], 'validateClass', 'params' => ['extends' => Controller::className()]],
            [['db'], 'validateDb'],
            [['controllerClass'], 'match', 'pattern' => '/Controller$/', 'message' => 'Controller class name must be suffixed with "Controller".'],
            [['controllerClass'], 'match', 'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/', 'message' => 'Controller class name must start with an uppercase letter.'],
//            [['searchModelClass'], 'validateNewClass'],
            [['indexWidgetType'], 'in', 'range' => ['grid', 'list']],
//            [['modelClass'], 'validateModelClass'],
            [['enableI18N', 'generateRelations', 'generateSearchModel', 'pluralize', 'expandable', 'cancelable', 'pdf', 'loggedUserOnly'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['viewPath', 'skippedRelations', 'skippedColumns',
                'controllerClass', 'blameableValue', 'nameAttribute',
                'hiddenColumns', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy',
                'UUIDColumn'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'db' => 'Database Connection ID',
            'nsTraits' => 'Trait Namespace',
            'modelClass' => 'Model Class',
            'generateQuery' => 'Generate ActiveQuery',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'nsModel' => 'Model Namespace',
            'nsSearchModel' => 'Search Model Namespace',
            'UUIDColumn' => 'UUID Column',
            'nsController' => 'Controller Namespace',
            'viewPath' => 'View Path',
            'baseControllerClass' => 'Base Controller Class',
            'indexWidgetType' => 'Widget Used in Index Page',
            'searchModelClass' => 'Search Model Class',
            'expandable' => 'Expandable Index Grid View',
            'cancelable' => 'Add Cancel Button On Form',
            'pdf' => 'PDF Printable View'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'db' => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>. In this case, multiple ActiveRecord classes
                will be generated, one for each matching table name; and the class names will be generated from
                the matching characters. For example, table <code>tbl_post</code> will generate <code>Post</code>
                class.',
            'nsTraits' => 'This is the namespace of the traits to be generated, e.g., <code>app\traits</code>',
            'nameAttribute' => 'This is the (set) of name column that you use to show as label, '
                . 'separated by comma (,) for multiple table(asterisk on Table Name).',
            'skippedColumns' => 'Fill this field with the column name that you dont want to generate form & labels for the table. 
                You can fill multiple columns, separated by comma (,). You may specify the column name
                although "Table Name" ends with asterisk, in which case all columns will not be generated at all models & CRUD.',
            'hiddenColumns' => 'Fill this field with the column name that you want to generate form with the hidden field of the table. 
                You can fill multiple columns, separated by comma (,). You may specify the column name
                although "Table Name" ends with asterisk, in which case all columns will be generated with hidden field at the forms',
            'nsModel' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'modelClass' => 'This is the name of the Model class to be generated. The class name should not contain
                the namespace part as it is specified in "Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'baseModelClass' => 'This is the base class of the new ActiveRecord class. It should be a fully qualified namespaced class name.',
            'nsSearchModel' => 'This is the namespace of the search model class to be generated, e.g., <code>app\models</code>',
            'searchModelClass' => 'This is the name of the search class to be generated. The class name should not contain
                the namespace part as it is specified in "Search Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple search model classes will be generated.',
            'generateQuery' => 'This indicates whether to generate ActiveQuery for the ActiveRecord class.',
            'generateLabelsFromComments' => 'This indicates whether the generator should generate attribute labels
                by using the comments of the corresponding DB columns.',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated ActiveRecord class
                should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the
                table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the ActiveRecord class
                will return the table name as <code>{{%post}}</code>.',
            'generateSearchModel' => 'This indicates whether the generator should generate search model based on
                columns it detects in the database.',
            'generateRelations' => 'This indicates whether the generator should generate relations based on
                foreign key constraints it detects in the database. Note that if your database contains too many tables,
                you may want to uncheck this option to accelerate the code generation process.',
            'generateMigrations' => 'This indicates whether the generator should generate migrations based on
                table structure.',
            'optimisticLock' => 'This indicates whether the generator should generate optimistic lock feature for Model. '
                . 'Enter this field with optimistic lock column name. '
                . 'Empty this field if you want to disable this feature.',
            'createdAt' => 'This indicates whether the generator should generate Timestamp Behaviors feature for Model. '
                . 'Enter this field with Created At column name. '
                . 'Empty "Created At" & "Updated At" field if you want to disable this feature.',
            'updatedAt' => 'This indicates whether the generator should generate Timestamp Behaviors feature for Model. '
                . 'Enter this field with Updated At column name. '
                . 'Empty "Created At" & "Updated At" field if you want to disable this feature.',
            'timestampValue' => 'This will generate the </code>value</code> configuration entry for Timestamp Behaviors.  e.g., <code>new Expression(\'NOW()\')</code>',
            'createdBy' => 'This indicates whether the generator should generate Blameable Behaviors feature for Model. '
                . 'Enter this field with Created By column name. '
                . 'Empty "Created By" & "Updated By" field if you want to disable this feature.',
            'updatedBy' => 'This indicates whether the generator should generate Blameable Behaviors feature for Model. '
                . 'Enter this field with Updated By column name. '
                . 'Empty "Created By" & "Updated By" field if you want to disable this feature.',
            'blameableValue' => 'This will generate the </code>value</code> configuration entry for Blameable Behaviors.  e.g., <code>new Expression(\'NOW()\')</code>',
            'UUIDColumn' => 'This indicates whether the generator should generate UUID Behaviors feature for Model. '
                . 'Enter this field with UUID column name. '
                . 'Empty "UUID Column" field if you want to disable this feature.',
            'deletedBy' => 'This indicates whether the generator should generate Soft Delete feature for Model. '
                . 'Enter this field with Deleted By column name. '
                . 'Empty "Deleted By" & "Deleted At" field if you want to disable this feature.',
            'deletedAt' => 'This indicates whether the generator should generate Soft Delete feature for Model. '
                . 'Enter this field with Updated By column name. '
                . 'Empty "Deleted By" & "Deleted At" field if you want to disable this feature.',
            'nsController' => 'This is the namespace of the Controller class to be generated, e.g., <code>app\controllers</code>',
            'controllerClass' => 'This is the name of the Controller class to be generated. The class name should not contain
                the namespace part as it is specified in "Controller Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple Controller classes will be generated.',
            'nsModel' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'pluralize' => 'Set the generator to generate pluralize for label',
            'expandable' => 'Set the generator to generate expandable/collapsible row for related at index',
            'cancelable' => 'Set the generator to generate cancel button to return to grid view at form',
            'pdf' => 'Set the generator to generate printable PDF generator at view',
            'viewPath' => 'Specify the directory for storing the view scripts for the controller. You may use path alias here, e.g.,
                <code>/var/www/basic/controllers/views/post</code>, <code>@app/views/post</code>. If not set, it will default
                to <code>@app/views/ControllerID</code>',
            'baseControllerClass' => 'This is the class that the new CRUD controller class will extend from.
                You should provide a fully qualified class name, e.g., <code>yii\web\Controller</code>.',
            'skippedRelations' => 'Fill this field with the relation name that you dont want to generate CRUD for the table. 
                You can fill multiple relations, separated by comma (,). You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case all relations will be generated.',
            'indexWidgetType' => 'This is the widget type to be used in the index page to display list of the models.
                You may choose either <code>GridView</code> or <code>ListView</code>',
            'modelClass' => 'This is the name of the Model class to be generated. The class name should not contain
                the namespace part as it is specified in "Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'queryNs' => 'This is the namespace of the ActiveQuery class to be generated, e.g., <code>app\models</code>',
            'queryClass' => 'This is the name of the ActiveQuery class to be generated. The class name should not contain
                the namespace part as it is specified in "ActiveQuery Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveQuery classes will be generated.',
            'queryBaseClass' => 'This is the base class of the new ActiveQuery class. It should be a fully qualified namespaced class name.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), [
            'db',
            'skippedColumns',
            'hiddenColumns',
            'nameAttribute',
            'nsModel',
            'nsSearchModel',
            'nsController',
            'nsTraits',
            'baseModelClass',
            'queryNs',
            'queryBaseClass',
            'optimisticLock',
            'createdBy',
            'updatedBy',
            'deletedBy',
            'createdAt',
            'timestampValue',
            'updatedAt',
            'deletedAt',
            'blameableValue',
            'UUIDColumn',
            'baseControllerClass',
            'indexWidgetType',
            'viewPath']);
    }

    /**
     * @inheritdoc
     */
    public function autoCompleteData()
    {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return [
                'tableName' => function () use ($db) {
                    return $db->getSchema()->getTableNames();
                },
            ];
        } else {
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    /**
     * Checks if model class is valid
     */
    public function validateModelClass()
    {
        $pk = $this->tableSchema->primaryKey;
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $this->modelClass must have primary key(s).");
        }
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $this->relations = $relations;
//        print_r($relations);
        $db = $this->getDbConnection();
        $this->nameAttribute = ($this->nameAttribute) ? explode(',', str_replace(' ', '', $this->nameAttribute)) : [$this->nameAttribute];
        $this->hiddenColumns = ($this->hiddenColumns) ? explode(',', str_replace(' ', '', $this->hiddenColumns)) : [$this->hiddenColumns];
        $this->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [$this->skippedColumns];
        $this->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [$this->skippedRelations];
        foreach ($this->getTableNames() as $tableName) {
            // model :
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
                $controllerClassName = $modelClassName . 'Controller';
            } else {
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
                $controllerClassName = (!empty($this->controllerClass)) ? $this->controllerClass : $modelClassName . 'Controller';
            }
//            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $this->modelClass = "{$this->nsModel}\\{$modelClassName}";
            $this->tableSchema = $tableSchema;
//            $this->relations = isset($relations[$tableName]) ? $relations[$tableName] : [];
            $this->controllerClass = $this->nsController . '\\' . $controllerClassName;

            // search model :
            if ($this->generateSearchModel) {
                if (empty($this->searchModelClass) || strpos($this->tableName, '*') !== false) {
                    $searchModelClassName = $modelClassName . 'Search';
                } else {
                    if ($this->nsSearchModel === $this->nsModel && $this->searchModelClass === $modelClassName)
                        $searchModelClassName = $this->searchModelClass . 'Search';
                    else
                        $searchModelClassName = $this->searchModelClass;
                }
                $this->searchModelClass = $this->nsSearchModel . '\\' . $searchModelClassName;
                $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
                $files[] = new CodeFile($searchModel, $this->render('search.php',
                    ['relations' => isset($relations[$tableName]) ? $relations[$tableName] : []]));
            }

            //controller
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->nsController)) . '/' . $controllerClassName . '.php', $this->render('controller.php', [
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ])
            );

            // views :
            $viewPath = $this->getViewPath();
            $templatePath = $this->getTemplatePath() . '/views';
            foreach (scandir($templatePath) as $file) {
                if (empty($this->searchModelClass) && $file === '_search.php') {
                    continue;
                }
                if ($file === '_formref.php' || $file === '_dataref.php'|| $file === '_data.php') {
                    continue;
                }
                if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ]));
                }
            }
            if (isset($relations[$tableName])) {
                if ($this->expandable) {
                    $files[] = new CodeFile("$viewPath/_data.php", $this->render("views/_data.php", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ]));
                }
                foreach ($relations[$tableName] as $name => $rel) {
                    if ($rel[2] && isset($rel[3]) && !in_array($name, $this->skippedRelations)) {
                        $files[] = new CodeFile("$viewPath/_form$rel[1].php", $this->render("views/_formref.php", [
                            'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                        ]));
                        if ($this->expandable) {
                            $files[] = new CodeFile("$viewPath/_data$rel[1].php", $this->render("views/_dataref.php", [
                                'relName' => $name,
                                'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                            ]));
                        }
                    }
                }
            }

            if (strpos($this->tableName, '*') !== false) {
                $this->modelClass = '';
                $this->controllerClass = '';
                $this->searchModelClass = '';
            } else {
                $this->modelClass = $modelClassName;
                $this->controllerClass = $controllerClassName;
                if ($this->generateSearchModel) {
                    $this->searchModelClass = $searchModelClassName;
                }
            }
        }
        $this->nameAttribute = (is_array($this->nameAttribute)) ? implode(', ', $this->nameAttribute) : '';
        $this->hiddenColumns = (is_array($this->hiddenColumns)) ? implode(', ', $this->hiddenColumns) : '';
        $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
        $this->skippedRelations = (is_array($this->skippedRelations)) ? implode(', ', $this->skippedRelations) : '';

        return $files;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param TableSchema the table being checked
     * @param array $fks obtained from the checkPivotTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations)
    {
        $db = $this->getDbConnection();
        $table0 = $fks[$table->primaryKey[0]][0];
        $table1 = $fks[$table->primaryKey[1]][0];
        $className0 = $this->generateClassName($table0);
        $className1 = $this->generateClassName($table1);
        $table0Schema = $db->getTableSchema($table0);
        $table1Schema = $db->getTableSchema($table1);

        $link = $this->generateRelationLink([$fks[$table->primaryKey[1]][1] => $table->primaryKey[1]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[0] => $fks[$table->primaryKey[0]][1]]);
        $relationName = $this->generateRelationName($relations, $table0Schema, $table->primaryKey[1], true);
        $relations[$table0Schema->fullName][$relationName] = [
            "return \$this->hasMany(\\{$this->nsModel}\\$className1::className(), $link)->viaTable('"
            . $this->generateTableName($table->name) . "', $viaLink);",
            $className1,
            true,
        ];

        $link = $this->generateRelationLink([$fks[$table->primaryKey[0]][1] => $table->primaryKey[0]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[1] => $fks[$table->primaryKey[1]][1]]);
        $relationName = $this->generateRelationName($relations, $table1Schema, $table->primaryKey[0], true);
        $relations[$table1Schema->fullName][$relationName] = [
            "return \$this->hasMany(\\{$this->nsModel}\\$className0::className(), $link)->viaTable('"
            . $this->generateTableName($table->name) . "', $viaLink);",
            $className0,
            true,
        ];

        return $relations;
    }

    /**
     * @return array the generated relation declarations
     */
    protected function generateRelations()
    {
        if (!$this->generateRelations) {
            return [];
        }

        $db = $this->getDbConnection();

        $schema = $db->getSchema();
        if ($schema->hasMethod('getSchemaNames')) { // keep BC to Yii versions < 2.0.4
            try {
                $schemaNames = $schema->getSchemaNames();
            } catch (NotSupportedException $e) {
                // schema names are not supported by schema
            }
        }
        if (!isset($schemaNames)) {
            if (($pos = strpos($this->tableName, '.')) !== false) {
                $schemaNames = [substr($this->tableName, 0, $pos)];
            } else {
                $schemaNames = [''];
            }
        }

        $relations = [];
        foreach ($schemaNames as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
//                $className = $this->generateClassName($table->fullName);
                if (strpos($this->tableName, '*') !== false) {
                    $className = $this->generateClassName($table->fullName);
                } else {
                    $className = Inflector::id2camel($table->fullName, '_');
                }
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    unset($refs[0]);
                    $fks = array_keys($refs);

                    if (strpos($this->tableName, '*') !== false) {
                        $refClassName = $this->generateClassName($refTableSchema->fullName);
                    } else {
                        $refClassName = Inflector::id2camel($refTableSchema->fullName, '_');
                    }
//                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName][lcfirst($relationName)] = [
                        "return \$this->hasOne(\\{$this->nsModel}\\$refClassName::className(), $link);", // relation type
                        $refClassName, //relclass
                        0, //is multiple
                        $refTable, //related table
                        $refs[key($refs)], // related primary key
                        key($refs) // this foreign key
                    ];

                    // Add relation for the referenced table
                    $uniqueKeys = [$table->primaryKey];
                    try {
                        $uniqueKeys = array_merge($uniqueKeys, $db->getSchema()->findUniqueIndexes($table));
                    } catch (NotSupportedException $e) {
                        // ignore
                    }
                    $hasMany = 1;
                    foreach ($uniqueKeys as $uniqueKey) {
                        if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                            $hasMany = 0;
                            break;
                        }
                    }
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][lcfirst($relationName)] = [
                        "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "(\\{$this->nsModel}\\$className::className(), $link);", // rel type
                        $className, //rel class
                        $hasMany, //is multiple
                        $table->fullName, // rel table
                        $refs[key($refs)], // rel primary key
                        key($refs) // this foreign key
                    ];
                }

                if (($fks = $this->checkPivotTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $fks, $relations);
            }
        }

        return $relations;
    }

    /**
     * Generates the link parameter to be used in generating the relation declaration.
     * @param array $refs reference constraint
     * @return string the generated link parameter.
     */
    protected function generateRelationLink($refs)
    {
        $pairs = [];
        foreach ($refs as $a => $b) {
            $pairs[] = "'$a' => '$b'";
        }

        return '[' . implode(', ', $pairs) . ']';
    }

    /**
     * Checks if the given table is a junction table.
     * For simplicity, this method only deals with the case where the pivot contains two PK columns,
     * each referencing a column in a different table.
     * @param TableSchema the table being checked
     * @return array|boolean the relevant foreign key constraint information if the table is a junction table,
     * or false if the table is not a junction table.
     */
    protected function checkPivotTable($table)
    {
        $pk = $table->primaryKey;
        if (count($pk) !== 2) {
            return false;
        }
        $fks = [];
        foreach ($table->foreignKeys as $refs) {
            if (count($refs) === 2) {
                if (isset($refs[$pk[0]])) {
                    $fks[$pk[0]] = [$refs[0], $refs[$pk[0]]];
                } elseif (isset($refs[$pk[1]])) {
                    $fks[$pk[1]] = [$refs[0], $refs[$pk[1]]];
                }
            }
        }
        if (count($fks) === 2 && $fks[$pk[0]][0] !== $fks[$pk[1]][0]) {
            return $fks;
        } else {
            return false;
        }
    }

    /**
     * Generate a relation name for the specified table and a base name.
     * @param array $relations the relations being generated currently.
     * @param TableSchema $table the table schema
     * @param string $key a base name that the relation name may be generated from
     * @param boolean $multiple whether this is a has-many relation
     * @return string the relation name
     */
    protected function generateRelationName($relations, $table, $key, $multiple)
    {
        if (!empty($key) && substr_compare($key, 'id', -2, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = rtrim(substr($key, 0, -2), '_');
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }
        return $name;
    }

    /**
     * @return Connection the DB connection as specified by [[db]].
     */
    public function getDbConnection()
    {
        return Yii::$app->get($this->db, false);
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
    public function getViewPath()
    {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerID());
        } else {
            return Yii::getAlias($this->viewPath . '/' . $this->getControllerID());
        }
    }

    public function getNameAttribute()
    {
        foreach ($this->tableSchema->getColumnNames() as $name) {
            foreach ($this->nameAttribute as $nameAttr) {
                if (!strcasecmp($name, $nameAttr) || !strcasecmp($name, $this->tableSchema->fullName)) {
                    return $name;
                }
            }
        }
        /* @var $class ActiveRecord */
//        $class = $this->modelClass;
        $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];

        return $pk;
    }

    public function getNameAttributeFK($tableName)
    {
        $tableSchema = $this->getDbConnection()->getTableSchema($tableName);
        foreach ($tableSchema->getColumnNames() as $name) {
            if (in_array($name, $this->nameAttribute) || $name === $tableName) {
                return $name;
            }
        }
        $pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];

        return $pk;
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
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
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
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
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
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
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
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        return $rules;
    }

    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateGridViewField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "['attribute' => '$attribute', 'hidden' => true],\n";
        }
        $humanize = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            } else {
                return "'$attribute',\n";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $format = $this->generateColumnFormat($column);
//        if($column->autoIncrement){
//            return "";
//        } else
        if (array_key_exists($attribute, $fk)) {
            $rel = $fk[$attribute];
            $labelCol = $this->getNameAttributeFK($rel[3]);
            $humanize = Inflector::humanize($rel[3]);
            $id = 'grid-'.Inflector::camel2id(StringHelper::basename($this->searchModelClass)).'-'.$attribute;
            $output = "[
            'attribute' => '$attribute',
            'label' => " . $this->generateString(ucwords(Inflector::humanize($rel[5]))) . ",
            'value' => function(\$model){
                return \$model->$rel[3]->$labelCol;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \\yii\\helpers\\ArrayHelper::map(\\$this->nsModel\\$rel[1]::find()->asArray()->all(), '$rel[4]', '$labelCol'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => '$humanize', 'id' => '$id']
        ],\n";
            return $output;
        } else {
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    public function generateFK($tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        $fk = [];
        if (isset($this->relations[$tableSchema->fullName])) {
            foreach ($this->relations[$tableSchema->fullName] as $name => $relations) {
                foreach ($tableSchema->foreignKeys as $value) {
                    if (isset($relations[5]) && $relations[3] == $value[0]) {
                        $fk[$relations[5]] = $relations;
                        $fk[$relations[5]][] = $name;
                    }
                }
            }
        }
        return $fk;
    }

    /**
     * Generates code for Kartik Tabular Form field
     * @param string $attribute
     * @return string
     */
    public function generateTabularFormField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\"$attribute\" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions'=>['hidden'=>true]]";
        }
//        print_r($tableSchema->foreignKeys);
//        print_r($fk);
        $humanize = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\"$attribute\" => ['type' => TabularForm::INPUT_PASSWORD]";
            } else {
                return "\"$attribute\" => ['type' => TabularForm::INPUT_TEXT]";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->autoIncrement) {
            return "'$attribute' => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden' => true]]";
        } elseif ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return "'$attribute' => ['type' => TabularForm::INPUT_CHECKBOX]";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "'$attribute' => ['type' => TabularForm::INPUT_TEXTAREA]";
        } elseif ($column->dbType === 'date') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\DatePicker::classname(),
            'options' => [
                'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
                'type' => \kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ]
            ]
        ]";
        } elseif ($column->dbType === 'time') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\TimePicker::classname()
        ]";
        } elseif ($column->dbType === 'datetime') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
        'widgetClass' => \kartik\widgets\DateTimePicker::classname(),
            'options' => [
                'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'hh:ii:ss dd-M-yyyy'
                ]
            ]
        ]";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];
            $labelCol = $this->getNameAttributeFK($rel[3]);
            $humanize = Inflector::humanize($rel[3]);
//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[1];
            $output = "'$attribute' => [
            'label' => '$humanize',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map($fkClassFQ::find()->orderBy('$labelCol')->asArray()->all(), '$rel[4]', '$labelCol'),
                'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
            ],
            'columnOptions' => ['width' => '200px']
        ]";
            return $output;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'INPUT_PASSWORD';
            } else {
                $input = 'INPUT_TEXT';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "'$attribute' => ['type' => TabularForm::INPUT_DROPDOWN_LIST,
                    'options' => [
                        'items' => " . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ",
                        'columnOptions => ['width' => '185px'],
                        'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
                    ]
        ]";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "'$attribute' => ['type' => TabularForm::$input]";
            } else {
                return "'$attribute' => ['type' => TabularForm::$input]";//max length??
            }
        }
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\$form->field(\$model, '$attribute', ['template' => '{input}'])->textInput(['style' => 'display:none']);";
        }
        $placeholder = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else if (in_array($attribute, $this->hiddenColumns)) {
                return "\$form->field(\$model, '$attribute')->hiddenInput()";
            } else {
                return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "\$form->field(\$model, '$attribute')->textarea(['rows' => 6])";
        } elseif ($column->dbType === 'date') {
            return "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\DatePicker::classname(), [
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $placeholder) . "],
        'type' => \kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd-M-yyyy'
        ]
    ]);";
        } elseif ($column->dbType === 'time') {
            return "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\TimePicker::className());";
        } elseif ($column->dbType === 'datetime') {
            return "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\DateTimePicker::classname(), [
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $placeholder) . "],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'mm/dd/yyyy hh:ii:ss'
        ]
    ])";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];
            $labelCol = $this->getNameAttributeFK($rel[3]);
            $humanize = Inflector::humanize($rel[3]);
//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[1];
            $output = "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map($fkClassFQ::find()->orderBy('$rel[4]')->asArray()->all(), '$rel[4]', '$labelCol'),
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])";
            return $output;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field(\$model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field(\$model, '$attribute')->$input(['placeholder' => '$placeholder'])";
            } else {
                return "\$form->field(\$model, '$attribute')->$input(['maxlength' => true, 'placeholder' => '$placeholder'])";
            }
        }
    }

    /**
     * Generates code for active search field
     * @param string $attribute
     * @return string
     */
    public function generateActiveSearchField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\$form->field(\$model, '$attribute', ['template' => '{input}'])->textInput(['style' => 'display:none']);";
        }
        $placeholder = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else if (in_array($attribute, $this->hiddenColumns)) {
                return "\$form->field(\$model, '$attribute')->hiddenInput()";
            } else {
                return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "\$form->field(\$model, '$attribute')->textarea(['rows' => 6])";
        } elseif ($column->dbType === 'date') {
            return "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\DatePicker::classname(), [
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $placeholder) . "],
        'type' => \kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd-M-yyyy'
        ]
    ]);";
        } elseif ($column->dbType === 'time') {
            return "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\TimePicker::className());";
        } elseif ($column->dbType === 'datetime') {
            return "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\DateTimePicker::classname(), [
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $placeholder) . "],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'mm/dd/yyyy hh:ii:ss'
        ]
    ])";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];
            $labelCol = $this->getNameAttributeFK($rel[3]);
            $humanize = Inflector::humanize($rel[3]);
//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[1];
            $output = "\$form->field(\$model, '$attribute')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map($fkClassFQ::find()->orderBy('$rel[4]')->asArray()->all(), '$rel[4]', '$labelCol'),
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])";
            return $output;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field(\$model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field(\$model, '$attribute')->$input(['placeholder' => '$placeholder'])";
            } else {
                return "\$form->field(\$model, '$attribute')->$input(['maxlength' => true, 'placeholder' => '$placeholder'])";
            }
        }
    }

    /**
     * Generates column format
     * @param ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column)
    {
        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
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
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes()
    {
        return $this->getColumnNames();
    }

    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels()
    {
        /* @var $model Model */
//        $model = new $this->modelClass();
//        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
//            if (isset($attributeLabels[$name])) {
//                $labels[$name] = $attributeLabels[$name];
//            } else {
            if (!strcasecmp($name, 'id')) {
                $labels[$name] = 'ID';
            } else {
                $label = Inflector::camel2words($name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$name] = $label;
            }
//            }
        }

        return $labels;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
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

    public function getTableSchema()
    {
        return $this->tableSchema;
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams()
    {
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
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $pks = $this->tableSchema->primaryKey;
        if (($table = $this->getTableSchema()) === false) {
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
     * Validates the [[db]] attribute.
     */
    public function validateDb()
    {
        if (!Yii::$app->has($this->db)) {
            $this->addError('db', 'There is no application component named "db".');
        } elseif (!Yii::$app->get($this->db) instanceof Connection) {
            $this->addError('db', 'The "db" application component must be a DB connection instance.');
        }
    }

    /**
     * Validates the [[tableName]] attribute.
     */
    public function validateTableName()
    {
        if (strpos($this->tableName, '*') !== false && substr_compare($this->tableName, '*', -1, 1)) {
            $this->addError('tableName', 'Asterisk is only allowed as the last character.');

            return;
        }
        $tables = $this->getTableNames();
        if (empty($tables)) {
            $this->addError('tableName', "Table '{$this->tableName}' does not exist.");
        } else {
            foreach ($tables as $table) {
                $class = $this->generateClassName($table);
                if ($this->isReservedKeyword($class)) {
                    $this->addError('tableName', "Table '$table' will generate a class which is a reserved PHP keyword.");
                    break;
                }
            }
        }
    }

    protected $tableNames;
    protected $classNames;

    /**
     * @return array the table names that match the pattern specified by [[tableName]].
     */
    protected function getTableNames()
    {
        if ($this->tableNames !== null) {
            return $this->tableNames;
        }
        $db = $this->getDbConnection();
        if ($db === null) {
            return [];
        }
        $tableNames = [];
        if (strpos($this->tableName, '*') !== false) {
            if (($pos = strrpos($this->tableName, '.')) !== false) {
                $schema = substr($this->tableName, 0, $pos);
                $pattern = '/^' . str_replace('*', '\w+', substr($this->tableName, $pos + 1)) . '$/';
            } else {
                $schema = '';
                $pattern = '/^' . str_replace('*', '\w+', $this->tableName) . '$/';
            }

            foreach ($db->schema->getTableNames($schema) as $table) {
                if (preg_match($pattern, $table)) {
                    $tableNames[] = $schema === '' ? $table : ($schema . '.' . $table);
                }
            }
        } elseif (($table = $db->getTableSchema($this->tableName, true)) !== null) {
            $tableNames[] = $this->tableName;
            $this->classNames[$this->tableName] = $this->modelClass;
        }

        return $this->tableNames = $tableNames;
    }

    /**
     * Generates the table name by considering table prefix.
     * If [[useTablePrefix]] is false, the table name will be returned without change.
     * @param string $tableName the table name (which may contain schema prefix)
     * @return string the generated table name
     */
    public function generateTableName($tableName)
    {
        if (!$this->useTablePrefix) {
            return $tableName;
        }

        $db = $this->getDbConnection();
        if (preg_match("/^{$db->tablePrefix}(.*?)$/", $tableName, $matches)) {
            $tableName = '{{%' . $matches[1] . '}}';
        } elseif (preg_match("/^(.*?){$db->tablePrefix}$/", $tableName, $matches)) {
            $tableName = '{{' . $matches[1] . '%}}';
        }
        return $tableName;
    }

    /**
     * Generates a class name from the specified table name.
     * @param string $tableName the table name (which may contain schema prefix)
     * @param boolean $useSchemaName should schema name be included in the class name, if present
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }

        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }

        $db = $this->getDbConnection();
        $patterns = [];
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
                break;
            }
        }
        return $this->classNames[$fullTableName] = Inflector::id2camel($schemaName . $className, '_');
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

    public function isModelExist()
    {
        $class = $this->modelClass;
        try {
            if (!class_exists($class)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array model column names
     */
    public function getColumnNames()
    {
        return $this->getTableSchema()->getColumnNames();
    }

}
