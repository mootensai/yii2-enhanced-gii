<?php
/**
 * Created by PhpStorm.
 * User: Yohanes
 * Date: 14-Jun-16
 * Time: 3:56 PM.
 */

namespace inquid\enhancedgii;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException as InvalidConfigExceptionAlias;
use yii\base\NotSupportedException;
use yii\db\Connection;
use yii\db\TableSchema;
use yii\helpers\Inflector;

/**
 * @property array $columnNames
 * @property string[] $schemaNames
 * @property \yii\base\Component|object $dbConnection
 */
abstract class BaseGenerator extends \yii\gii\Generator
{
    public $skippedTables = 'auth_assignment, auth_item, auth_item_child, auth_rule, token,social_account, user, profile, migration';
    const RELATIONS_NONE = 'none';
    const RELATIONS_ALL = 'all';
    const RELATIONS_ALL_INVERSE = 'all-inverse';

    // thanks to github.com/iurijacob for simplify the relation array
    const REL_TYPE = 0;
    const REL_CLASS = 1;
    const REL_IS_MULTIPLE = 2;
    const REL_TABLE = 3;
    const REL_PRIMARY_KEY = 4;
    const REL_FOREIGN_KEY = 5;
    const REL_IS_MASTER = 6;

    const FK_TABLE_NAME = 0;
    const FK_FIELD_NAME = 1;

    public $db = 'db';
    public $dbNoSql = 'mongodb';
    /* @var $tableSchema TableSchema */
    public $tableSchema;
    public $tableName;
    public $modelClass;
    public $moduleName;
    public $baseModelClass = 'ActiveRecord';
    public $baseModelClassNoSql = 'ActiveRecordNoSql';
    public $nsModel = 'app\models';
    public $nsSearchModel = 'app\models';
    public $skippedRelations;
    public $useSchemaName = true;

    public static function getTreeColumns()
    {
        return ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly',
            'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', ];
    }

    /**
     * {@inheritdoc}
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
     * Validates the [[db]] attribute.
     */
    public function validateDb(): void
    {
        if (!Yii::$app->has($this->db)) {
            $this->addError('db', 'There is no application component named "db".');
        } elseif (!Yii::$app->get($this->db) instanceof Connection) {
            $this->addError('db', 'The "db" application component must be a DB connection instance.');
        }
    }

    /**
     * Validate if the module exist in the app.
     *
     * @return bool
     */
    public function validateModuleExist(): bool
    {
        if (isset(Yii::$app->modules[$this->moduleName])) {
            return true;
        }

        return false;
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

    /**
     * Checks if model class is valid.
     */
    public function validateModelClass()
    {
        $pk = $this->tableSchema->primaryKey;
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $this->modelClass must have primary key(s).");
        }
    }

//    public function isModelExist()
//    {
//        $class = $this->modelClass;
//        try {
//            if (!class_exists($class)) {
//                return false;
//            }
//        } catch (\Exception $e) {
//            return false;
//        }
//    }

    /**
     * Checks if the given table is a junction table.
     * For simplicity, this method only deals with the case where the pivot contains two PK columns,
     * each referencing a column in a different table.
     *
     * @param TableSchema the table being checked
     * @param TableSchema $table
     *
     * @return array|bool the relevant foreign key constraint information if the table is a junction table,
     *                    or false if the table is not a junction table.
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
     * @throws InvalidConfigExceptionAlias
     *
     * @return string[] all db schema names or an array with a single empty string
     *
     * @since 2.0.5
     */
    protected function getSchemaNames()
    {
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

        return $schemaNames;
    }

    /**
     * @throws InvalidConfigExceptionAlias
     * @throws NotSupportedException
     *
     * @return array the generated relation declarations
     */
    protected function generateRelations(): array
    {
        if (!$this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();

        $relations = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relFK = key($refs);
                    $relations[$table->fullName][lcfirst($relationName)] = [
                        self::REL_TYPE        => "return \$this->hasOne({$refClassName}::class, $link);", // relation type
                        self::REL_CLASS       => $refClassName, //relclass
                        self::REL_IS_MULTIPLE => 0, //is multiple
                        self::REL_TABLE       => $refTable, //related table
                        self::REL_PRIMARY_KEY => $refs[$relFK], // related primary key
                        self::REL_FOREIGN_KEY => $relFK, // this foreign key
                        self::REL_IS_MASTER   => in_array($relFK, $table->getColumnNames()) ? 1 : 0,
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][lcfirst($relationName)] = [
                        self::REL_TYPE        => 'return $this->'.($hasMany ? 'hasMany' : 'hasOne')."({$className}::class, $link);", // rel type
                        self::REL_CLASS       => $className, //rel class
                        self::REL_IS_MULTIPLE => $hasMany, //is multiple
                        self::REL_TABLE       => $table->fullName, // rel table
                        self::REL_PRIMARY_KEY => $refs[key($refs)], // rel primary key
                        self::REL_FOREIGN_KEY => key($refs), // this foreign key
                        self::REL_IS_MASTER   => in_array($relFK, $refTableSchema->getColumnNames()) ? 1 : 0,
                    ];
                }

                if (($junctionFks = $this->checkPivotTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        return $relations;
    }

    /**
     * Determines if relation is of has many type.
     *
     * @param TableSchema $table
     * @param array       $fks
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return bool
     *
     * @since 2.0.5
     */
    protected function isHasManyRelation($table, $fks)
    {
        $uniqueKeys = [$table->primaryKey];

        try {
            $uniqueKeys = array_merge($uniqueKeys, $this->getDbConnection()->getSchema()->findUniqueIndexes($table));
        } catch (NotSupportedException $e) {
            // ignore
        }
        foreach ($uniqueKeys as $uniqueKey) {
            if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Adds inverse relations.
     *
     * @param array $relations relation declarations
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return array relation declarations extended with inverse relation names
     *
     * @since 2.0.5
     */
    protected function addInverseRelations($relations): array
    {
        $relationNames = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($this->getDbConnection()->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $this->getDbConnection()->getTableSchema($refTable);
                    unset($refs[0]);
                    $fks = array_keys($refs);

                    $leftRelationName = $this->generateRelationName($relationNames, $table, $fks[0], false);
                    $relationNames[$table->fullName][$leftRelationName] = true;
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $rightRelationName = $this->generateRelationName(
                        $relationNames,
                        $refTableSchema,
                        $className,
                        $hasMany
                    );
                    $relationNames[$refTableSchema->fullName][$rightRelationName] = true;

                    $relations[$table->fullName][$leftRelationName][0] =
                        rtrim($relations[$table->fullName][$leftRelationName][0], ';')
                        ."->inverseOf('".lcfirst($rightRelationName)."');";
                    $relations[$refTableSchema->fullName][$rightRelationName][0] =
                        rtrim($relations[$refTableSchema->fullName][$rightRelationName][0], ';')
                        ."->inverseOf('".lcfirst($leftRelationName)."');";
                }
            }
        }

        return $relations;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     *
     * @param TableSchema the table being checked
     * @param array       $fks       obtained from the checkPivotTable() method
     * @param array       $relations
     * @param TableSchema $table
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations): array
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
            "return \$this->hasMany(\\{$this->nsModel}\\$className1::class, $link)->viaTable('"
            .$this->generateTableName($table->name)."', $viaLink);",
            $className1,
            true,
        ];

        $link = $this->generateRelationLink([$fks[$table->primaryKey[0]][1] => $table->primaryKey[0]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[1] => $fks[$table->primaryKey[1]][1]]);
        $relationName = $this->generateRelationName($relations, $table1Schema, $table->primaryKey[0], true);
        $relations[$table1Schema->fullName][$relationName] = [
            "return \$this->hasMany(\\{$this->nsModel}\\$className0::class, $link)->viaTable('"
            .$this->generateTableName($table->name)."', $viaLink);",
            $className0,
            true,
        ];

        return $relations;
    }

    /**
     * Generates the link parameter to be used in generating the relation declaration.
     *
     * @param array $refs reference constraint
     *
     * @return string the generated link parameter.
     */
    protected function generateRelationLink($refs)
    {
        $pairs = [];
        foreach ($refs as $a => $b) {
            $pairs[] = "'$a' => '$b'";
        }

        return '['.implode(', ', $pairs).']';
    }

    /**
     * Generates a class name from the specified table name.
     *
     * @param string $tableName     the table name (which may contain schema prefix)
     * @param bool   $useSchemaName should schema name be included in the class name, if present
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = null): string
    {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }

        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos).'_';
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
            $patterns[] = '/^'.str_replace('*', '(\w+)', $pattern).'$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        return $this->classNames[$fullTableName] = Inflector::id2camel($schemaName.$className, '_');
    }

    /**
     * Generate a relation name for the specified table and a base name.
     *
     * @param array       $relations the relations being generated currently.
     * @param TableSchema $table     the table schema
     * @param string      $key       a base name that the relation name may be generated from
     * @param bool        $multiple  whether this is a has-many relation
     *
     * @return string the relation name
     */
    protected function generateRelationName($relations, $table, $key, $multiple)
    {
//        print_r($key);
        static $baseModel;
        if ($baseModel === null && isset($this->baseClass)) {
            $baseClass = $this->baseClass;
            $baseModel = new $baseClass();
        }

        if (!empty($key) && substr_compare($key, 'id', -2, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = rtrim(substr($key, 0, -2), '_');
        } elseif (!empty($key) && substr_compare($key, 'id', 0, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = ltrim(substr($key, 2, strlen($key)), '_');
        }

        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($baseModel) && $baseModel->hasProperty(lcfirst($name))) {
            $name = $rawName.($i++);
        }
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName.($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName.($i++);
        }

        return lcfirst($name);
    }

    /**
     * Connection the DB connection as specified by [[db]].
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return object|Component
     */
    public function getDbConnection()
    {
        return Yii::$app->get($this->db, false);
    }

    /**
     * Connection the DB connection as specified by [[db]].
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return object|Component
     */
    public function getDbNoSQLConnection()
    {
        return Yii::$app->mongodb->getDatabase()->listCollections();
    }

    /**
     * @return TableSchema
     */
    public function getTableSchema(): TableSchema
    {
        return $this->tableSchema;
    }

    protected $tableNames;
    protected $classNames;

    /**
     * Returns an array of the table names that match the pattern specified by [[tableName]].
     *
     * @throws InvalidConfigExceptionAlias
     *
     * @return array
     */
    protected function getTableNames(): array
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
                $pattern = '/^'.str_replace('*', '\w+', substr($this->tableName, $pos + 1)).'$/';
            } else {
                $schema = '';
                $pattern = '/^'.str_replace('*', '\w+', $this->tableName).'$/';
            }

            foreach ($db->schema->getTableNames($schema) as $table) {
                if (preg_match($pattern, $table)) {
                    $tableNames[] = $schema === '' ? $table : ($schema.'.'.$table);
                }
            }
        } elseif (($table = $db->getTableSchema($this->tableName, true)) !== null) {
            $tableNames[] = $this->tableName;
            $this->classNames[$this->tableName] = $this->modelClass;
        }

        return $this->tableNames = $tableNames;
    }

    /**
     * The column names of the schema.
     *
     * @return array
     */
    public function getColumnNames(): array
    {
        return $this->getTableSchema()->getColumnNames();
    }

    /**
     * Generates the table name by considering table prefix.
     * If [[useTablePrefix]] is false, the table name will be returned without change.
     *
     * @param string $tableName the table name (which may contain schema prefix)
     *
     *@throws InvalidConfigExceptionAlias
     *
     * @return string the generated table name
     */
    public function generateTableName($tableName): string
    {
        if (!$this->useTablePrefix) {
            return $tableName;
        }

        $db = $this->getDbConnection();
        if (preg_match("/^{$db->tablePrefix}(.*?)$/", $tableName, $matches)) {
            $tableName = '{{%'.$matches[1].'}}';
        } elseif (preg_match("/^(.*?){$db->tablePrefix}$/", $tableName, $matches)) {
            $tableName = '{{'.$matches[1].'%}}';
        }

        return $tableName;
    }
}
