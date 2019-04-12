<?php

namespace inquid\enhancedgii\testsgenerator;

use Yii;
use inquid\enhancedgii\utils\TableUtils;
use yii\gii\CodeFile;

/**
 * This generator will generate all the test necessary for test the CRUD.
 *
 * @author Luis Gonzalez <contact@inquid.co>
 */
class Generator extends \inquid\enhancedgii\BaseGenerator
{
    public $db;
    public $testPath = '@app/tests';
    public $tableName;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'INQUID Generator (TEST)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates all test needed for the CRUD';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'testPath', 'tableName'], 'filter', 'filter' => 'trim'],
            [['db', 'testPath', 'tableName'], 'required'],
            [['db'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['db'], 'validateDb'],
            [['tableName'], 'validateTableName'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'testPath' => 'Migration Path',
            'db' => 'Database Connection ID',
            'tableName' => 'Table Name'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'testPath' => 'Path to store generated file, e.g., <code>@app/migrations</code>',
            'db' => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>.'
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
        return ['unit_test.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['testPath', 'db']);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $db = $this->getDbConnection();
        $tableUtils = new TableUtils();
        $tableUtils->dbConnection = $db;
        $tables = [];
        $files = [];
        foreach ($this->getTableNames() as $tableName) {
            $testName = $tableName;
            $file = rtrim(Yii::getAlias($this->testPath), '/') . "/{$testName}UnitTest.php";
            $files[] = new CodeFile($file, $this->render('unit_test.php', [
                'testName' => $testName,
            ]));
        }

        return $files;
    }

    /**
     * Validates the [[db]] attribute.
     */
    public function validateDb()
    {
        if (!Yii::$app->has($this->db)) {
            $this->addError('db', 'There is no application component named "db".');
        } elseif (!Yii::$app->get($this->db) instanceof \yii\db\Connection) {
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
     * @throws \yii\base\InvalidConfigException
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
     * @throws \yii\base\InvalidConfigException
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
    public function getDbConnection()
    {
        return Yii::$app->get($this->db, false);
    }
}
