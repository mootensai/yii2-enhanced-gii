<?php

namespace mootensai\enhancedgii\migration;

use Yii;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\db\Expression;

/**
 * This generator will generate migration file for the specified database table.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.1
 */
class Generator extends \yii\gii\Generator
{
    public $db = 'db';
    public $migrationPath = '@app/migrations';
    public $migrationName;
    public $migrationTime;
    public $tableName;
    public $generateRelations = true;
    public $useTablePrefix = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->migrationTime = gmdate('ymd_H0101');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Migration Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a migration for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'migrationPath', 'tableName', 'migrationName', 'migrationTime'], 'filter', 'filter' => 'trim'],
            [['db', 'migrationPath', 'tableName', 'migrationName', 'migrationTime'], 'required'],
            [['db', 'migrationName'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['migrationTime'], 'match', 'pattern' => '/^(\d{6}_\d{6})/', 'message' => 'Only format xxxxxx_xxxxxx are allowed.'],
            [['db'], 'validateDb'],
            [['tableName'], 'validateTableName'],
            [['generateRelations'], 'boolean'],
            [['useTablePrefix'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'migrationPath' => 'Migration Path',
            'db' => 'Database Connection ID',
            'tableName' => 'Table Name',
            'migrationName' => 'Migration Name',
            'migrationTime' => 'Migration Time',
            'generateRelations' => 'Generate Relations',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'migrationPath' => 'Path to store generated file, e.g., <code>@app/migrations</code>',
            'db' => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>.',
            'migrationName' => 'The name of the new migration. This should only contain letters, digits and/or underscores.',
            'migrationTime' => 'Time of the new migration. This should only has format <code>yymmdd_hhiiss</code>.',
            'generateRelations' => 'This indicates whether the generator should generate relations based on
                foreign key constraints it detects in the database. Note that if your database contains too many tables,
                you may want to uncheck this option to accelerate the code generation process.',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated migration
                should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the
                table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the migration
                will use the table name as <code>{{%post}}</code>.',
        ]);
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
        return ['migration.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['migrationPath', 'db', 'generateRelations', 'useTablePrefix']);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        $tables = [];
        foreach ($this->getTableNames() as $tableName) {
            $tableSchema = $db->getTableSchema($tableName);
            $columns = $this->generateColumns($tableSchema);
            if (isset($columns[0])) {
                $primary = $columns[0];
                unset($columns[0]);
            } else {
                $primary = null;
            }
            $tables[$tableSchema->name] = [
                'name' => $this->generateTableName($tableSchema->name),
                'columns' => $columns,
                'primary' => $primary,
                'relations' => isset($relations[$tableSchema->name]) ? $relations[$tableSchema->name] : [],
            ];
        }

        $migrationName = 'm' . $this->migrationTime . '_' . $this->migrationName;
        $file = rtrim(Yii::getAlias($this->migrationPath), '/') . "/{$migrationName}.php";
        $files = new CodeFile($file, $this->render('migration.php', [
                'tables' => $this->reorderTables($tables, $relations),
                'migrationName' => $migrationName,
        ]));
        return [$files];
    }

    /**
     * Reorder tables acourding with dependencies.
     * @param array $tables
     * @param array $relations
     * @return array
     */
    protected function reorderTables($tables, $relations)
    {
        $depencies = $orders = $result = [];
        foreach ($relations as $table => $relation) {
            if (isset($relation[$table])) {
                unset($relation[$table]);
            }
            $depencies[$table] = array_keys($relation);
        }
        $tableNames = array_keys($tables);
        sort($tableNames);
        $this->reorderRecrusive($tableNames, $depencies, $orders);
        foreach (array_keys($orders) as $value) {
            if (isset($tables[$value])) {
                $result[] = $tables[$value];
            }
        }
        return $result;
    }

    /**
     *
     * @param array $tableNames
     * @param array $depencies
     * @param array $orders
     */
    protected function reorderRecrusive($tableNames, &$depencies, &$orders)
    {
        foreach ($tableNames as $table) {
            if (!isset($orders[$table])) {
                if (isset($depencies[$table])) {
                    $this->reorderRecrusive($depencies[$table], $depencies, $orders);
                }
                $orders[$table] = true;
            }
        }
    }
    protected $constans;

    /**
     *
     * @param \yii\db\ColumnSchema $column
     * @return array
     */
    public function getSchemaType($column)
    {
        if ($this->constans === null) {
            $this->constans = [];
            $ref = new \ReflectionClass(Schema::className());
            foreach ($ref->getConstants() as $constName => $constValue) {
                if (strpos($constName, 'TYPE_') === 0) {
                    $this->constans[$constValue] = '$this->' . $constValue;
                }
            }
            $this->constans['smallint'] = '$this->smallInteger';
            $this->constans['bigint'] = '$this->bigInteger';
        }
        if ($column->type !== Schema::TYPE_BOOLEAN && $column->size !== null) {
            $size = [$column->size];
            if ($column->scale !== null) {
                $size[] = $column->scale;
            }
        } else {
            $size = [];
        }
        $result = '';
        if (isset($this->constans[$column->type])) {
            $result = $this->constans[$column->type] . '(' . implode(',', $size) . ')';
            if (!$column->allowNull) {
                $result .= '->notNull()';
            }
            if ($column->defaultValue !== null) {
                $default = is_string($column->defaultValue) ? "'" . addslashes($column->defaultValue) . "'" : $column->defaultValue;
                $result .= "->defaultValue({$default})";
            }
        } else {
            $result = $column->dbType;
            if (!empty($size)) {
                $result.= '(' . implode(',', $size) . ')';
            }
            if (!$column->allowNull) {
                $result .= ' NOT NULL';
            }
            if ($column->defaultValue !== null) {
                $default = is_string($column->defaultValue) ? "'" . addslashes($column->defaultValue) . "'" : $column->defaultValue;
                $result .= " DEFAULT {$default}";
            }
            $result = '"' . $result . '"';
        }
        return $result;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    protected function generateColumns($table)
    {
        $columns = [];
        $needPK = true;
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                $columns[$column->name] = $column->type == Schema::TYPE_BIGINT ? '$this->bigPrimaryKey()' : '$this->primaryKey()';
                $needPK = false;
                continue;
            }
            $columns[$column->name] = $this->getSchemaType($column);
        }
        if ($needPK && !empty($table->primaryKey)) {
            $pks = implode(']], [[', $table->primaryKey);
            $columns[0] = "PRIMARY KEY ([[{$pks}]])";
        }

        return $columns;
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

        if (($pos = strpos($this->tableName, '.')) !== false) {
            $schemaName = substr($this->tableName, 0, $pos);
        } else {
            $schemaName = '';
        }

        $relations = [];
        foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
            $tableName = $table->name;
            foreach ($table->foreignKeys as $refs) {
                $refTable = $refs[0];
                $refTableName = $this->generateTableName($refTable);
                unset($refs[0]);

                $fks = implode(']], [[', array_keys($refs));
                $pks = implode(']], [[', array_values($refs));

                $relation = "FOREIGN KEY ([[$fks]]) REFERENCES $refTableName ([[$pks]]) ON DELETE CASCADE ON UPDATE CASCADE";
                $relations[$tableName][$refTable] = $relation;
            }
        }
        return $relations;
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
        }
    }
    protected $tableNames;

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
     * @return Connection the DB connection as specified by [[db]].
     */
    protected function getDbConnection()
    {
        return Yii::$app->get($this->db, false);
    }
}