<?php

namespace inquid\enhancedgii\testsgenerator;

use inquid\enhancedgii\utils\TableUtils;
use Yii;
use yii\db\mysql\ColumnSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;

/**
 * This generator will generate all the test necessary for test the CRUD.
 *
 * @author Luis Gonzalez <contact@inquid.co>
 */
class Generator extends \inquid\enhancedgii\BaseGenerator
{
    public $db;
    public $testPath = '@app/tests/unit/model';
    public $tableName;
    public $moduleName;
    public $skippedTables = 'auth_assignment, auth_item, auth_item_child, auth_rule, token,social_account, user, profile, migration';
    public $skippedColumns = 'created_at, updated_at, created_by, updated_by, deleted_at, deleted_by, created, modified, deleted';
    public $skipAllExistingTables = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'INQUID Generator (TEST)';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generates all test needed for the CRUD';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'testPath', 'tableName'], 'filter', 'filter' => 'trim'],
            [['db', 'testPath', 'tableName'], 'required'],
            [['db'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['tableName', 'moduleName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['db'], 'validateDb'],
            [['tableName'], 'validateTableName'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'testPath'   => 'Migration Path',
            'moduleName' => 'Module Name',
            'db'         => 'Database Connection ID',
            'tableName'  => 'Table Name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'testPath'  => 'Path to store generated file, e.g., <code>@app/migrations</code>',
            'db'        => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>.',
            'moduleName' => 'The module where the models will be placed',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function autoCompleteData()
    {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return [
                'tableName' => function() use ($db) {
                    return $db->getSchema()->getTableNames();
                },
            ];
        } else {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['unit_test.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['testPath', 'db']);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $db = $this->getDbConnection();
        $tableUtils = new TableUtils();
        $tableUtils->dbConnection = $db;
        $tables = [];
        $files = [];

        if (isset($this->moduleName)) {
            $moduleGenerator = new \inquid\enhancedgii\module\Generator();
            $moduleGenerator->moduleClass = "app\modules\\$this->moduleName\Module";
            $moduleGenerator->moduleID = $this->moduleName;
            $moduleGenerator->generate();
            $this->nsModel = "app\modules\\$this->moduleName\models";
        }

        $skippedTables = ($this->skippedTables) ? explode(',', str_replace(' ', '', $this->skippedTables)) : [];

        foreach ($this->getTableNames() as $tableName) {
            if (in_array($tableName, $skippedTables)):
                continue;
            endif;
            $columns = [];
            $tableSchema = $db->getTableSchema($tableName);
            $className = $this->generateClassName($tableName);
            $testName = $className . 'UnitTest';
            $file = rtrim(Yii::getAlias($this->testPath), '/') . "/{$testName}.php";
            $files[] = new CodeFile($file, $this->render('unit_test.php', [
                'testName'       => $testName,
                'className'      => $className,
                'columns'        => $columns,
                'tableSchema'    => $tableSchema,
                'skippedColumns' => $this->skippedColumns,
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
     * @throws \yii\base\InvalidConfigException
     *
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
     *
     * @param string $tableName the table name (which may contain schema prefix)
     *
     * @throws \yii\base\InvalidConfigException
     *
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
     * @throws \yii\base\InvalidConfigException
     *
     * @return Connection the DB connection as specified by [[db]].
     */
    public function getDbConnection()
    {
        return Yii::$app->get($this->db, false);
    }

    /**
     * Generates a class name from the specified table name.
     *
     * @param string $tableName     the table name (which may contain schema prefix)
     * @param bool   $useSchemaName should schema name be included in the class name, if present
     *
     * @throws \yii\base\InvalidConfigException
     *
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
     * @param ColumnSchema $column
     *
     * @return string
     */
    public function generateFakerType($column)
    {
        /* IDs */
        if ($column->name == 'id' && $column->type == 'integer') {
            return '$id';
        }
        if ($column->name == 'uuid' && $column->type == 'varchar') {
            return '$faker->uuid()';
        }
        if ($column->name == 'id' && $column->type == 'varchar') {
            return "\$faker->generateRandomString($column->size)";
        }
        /* Usernames */
        if ($column->name == 'name' || $column->name == 'title') {
            return "\$faker->name($column->size)";
        }
        if ($column->name == 'email') {
            return "\$faker->email($column->size)";
        }
        if ($column->name == 'username' || $column->name == 'user_name' || $column->name == 'userName') {
            return "\$faker->username($column->size)";
        }

        if ($column->name == 'cellPhone' || $column->name == 'cell_phone' || $column->name == 'cell' || $column->name == 'mobile') {
            return "\$faker->cellNumber($column->size)";
        }
        if ($column->name == 'firstName' || $column->name == 'first_name') {
            return "\$faker->firstName($column->size)";
        }
        if ($column->name == 'lastName' || $column->name == 'last_name') {
            return "\$faker->lastName($column->size)";
        }
        /* Contact info */
        if ($column->name == 'phone' || $column->name == 'phoneNumber') {
            return "\$faker->phoneNumber($column->size)";
        }
        if ($column->name == 'street' || $column->name == 'avenue' || $column->name == 'road' || $column->name == 'drive') {
            return "\$faker->streetName($column->size)";
        }
        if ($column->name == 'address') {
            return "\$faker->streetAddress($column->size)";
        }
        if ($column->name == 'number') {
            return "\$faker->buildingNumber($column->size)";
        }
        if ($column->name == 'city') {
            return "\$faker->city($column->size)";
        }
        if ($column->name == 'state') {
            return "\$faker->state($column->size)";
        }
        if ($column->name == 'country') {
            return "\$faker->country($column->size)";
        }
        if ($column->name == 'company' || $column->name == 'link') {
            return "\$faker->url($column->size)";
        }
        if ($column->name == 'postcode' || $column->name == 'postalcode') {
            return "\$faker->postcode($column->size)";
        }
        if ($column->name == 'latitude' || $column->name == 'lat') {
            return "\$faker->latitude($column->size)";
        }
        if ($column->name == 'longitude' || $column->name == 'long') {
            return "\$faker->latitude($column->size)";
        }
        /* Internet */
        if ($this->contains('url', $column->name) || $this->contains('link', $column->name)) {
            return "\$faker->url($column->size)";
        }
        if ($column->name == 'website') {
            return "\$faker->domainName($column->size)";
        }
        /* Date */
        if ($column->name == 'dateTime' || $column->name == 'date_time') {
            return "\$faker->dateTime($column->size)";
        }
        if ($column->name == 'date' || $this->contains('date', $column->name)) {
            return "\$faker->date($column->size)";
        }
        if ($column->name == 'year') {
            return '$faker->year()';
        }
        if ($column->name == 'month' && $column->type == 'integer') {
            return '$faker->month()';
        }
        if ($column->name == 'monthName' && $column->type == 'varchar') {
            return '$faker->monthName()';
        }
        if ($column->name == 'timezone' || $column->type == 'timeZone' || $column->type == 'time_zone') {
            return '$faker->timezone()';
        }
        /* Text */
        if ($column->type == 'text') {
            return '$faker->paragraph()';
        }
        /* Payment Info */
        if ($column->name == 'creditCardType' || $column->type == 'cardType' || $column->type == 'card') {
            return '$faker->creditCardType()';
        }
        if ($column->name == 'creditCardNumber' || $column->name == 'card_number' || $column->name == 'credit_card_number') {
            return '$faker->creditCardNumber()';
        }
        if ($column->name == 'creditCardExpirationDate' || $column->name == 'creditCardExp') {
            return '$faker->creditCardExpirationDate()';
        }

        if ($column->name == 'iban') {
            return '$faker->iban()';
        }
        if ($column->name == 'swiftBicNumber' || $column->name == 'swiftNumber' || $column->name == 'swift') {
            return '$faker->swiftBicNumber()';
        }

        // TODO improve this
        if ($column->name == 'clabe' || $column->size == 18) {
            return '$faker->number(18)';
        }

        /* Color */
        if ($this->contains('color', $column->name)) {
            return "\$faker->colorName($column->size)";
        }

        /* File */
        if ($column->name == 'file') {
            return "\$faker->file($column->size)";
        }
        if ($column->name == 'fileExtension' || $column->name == 'extension') {
            return '$faker->fileExtension()';
        }
        if ($column->name == 'image' || $column->name == 'photo') {
            return '$faker->image()';
        }

        return '$faker->text(10)';
    }

    public function contains($needle, $haystack)
    {
        if (strpos($haystack, $needle) !== false) {
            return true;
        }

        return false;
    }
}
