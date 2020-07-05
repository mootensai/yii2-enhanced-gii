<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator inquid\enhancedgii\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $isTree boolean */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>\base;

<?= (!$isTree) ? "use \\mootensai\\relation\\NoSqlRelationTrait;\n" : '' ?>
use mongosoft\mongodb\MongoDateBehavior;
use Yii;
use yii\mongodb\ActiveRecord as ActiveRecordNoSql;
<?php if ($generator->createdBy || $generator->updatedBy) { ?>
use yii\behaviors\BlameableBehavior;
<?php } ?>
<?php if ($generator->UUIDColumn) { ?>
use inquid\behaviors\UUIDBehaviorUUID4;
use yii\mongodb\ActiveQuery;
<?php } ?>

/**
 * This is the base model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column) { ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php } ?>
<?php if (!empty($relations)) { ?>
 *
<?php foreach ($relations as $name => $relation) { ?>
<?php if (!in_array($name, $generator->skippedRelations)) { ?>
 * @property <?= '\\'.$generator->nsModel.'\\'.$relation[$generator::REL_CLASS].($relation[$generator::REL_IS_MULTIPLE] ? '[]' : '').' $'.lcfirst($name)."\n" ?>
<?php } ?>
<?php } ?>
<?php } ?>
 */
class <?= $className ?> extends <?= $isTree ? '\kartik\tree\models\Tree'."\n" : ltrim($generator->baseModelClassNoSql, '\\')."\n" ?>
{
<?= (!$isTree) ? "\tuse NoSqlRelationTrait;\n" : '' ?>

<?php if ($generator->deletedBy) { ?>
    private $_rt_softdelete;
    private $_rt_softrestore;

    public function __construct(){
        parent::__construct();
        $this->_rt_softdelete = [
            '<?= $generator->deletedBy ?>' => <?= empty($generator->deletedByValue) ? 1 : $generator->deletedByValue ?>,
<?php if ($generator->deletedAt) { ?>
            '<?= $generator->deletedAt ?>' => <?= empty($generator->deletedAtValue) ? 1 : $generator->deletedAtValue ?>,
<?php } ?>
        ];
        $this->_rt_softrestore = [
            '<?= $generator->deletedBy ?>' => <?= empty($generator->deletedByValueRestored) ? 0 : $generator->deletedByValueRestored ?>,
<?php if ($generator->deletedAt) { ?>
            '<?= $generator->deletedAt ?>' => <?= empty($generator->deletedAtValueRestored) ? 0 : $generator->deletedAtValueRestored ?>,
<?php } ?>
        ];
    }
<?php } ?>
<?php if (!$isTree) { ?>

    /**
    * This function helps NoSqlRelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [<?php echo empty($relations) ? '' : "\n            '".implode("',\n            '", array_keys($relations))."'\n        " ?>];
    }

<?php } ?>
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            ".implode(",\n            ", $rules)."\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }

    /**
     * @return array
     */
    public function attributes(): array {
        return [
<?php foreach ($tableSchema->columns as $column) { ?>
            <?= "'{$column->name}',\n" ?>
<?php } ?>
        ];
    }

<?php if (!empty($generator->optimisticLock)) { ?>

    /**
     *
     * @return string
     * overwrite function optimisticLock
     * return string name of field are used to stored optimistic lock
     *
     */
    public function optimisticLock() {
        return '<?= $generator->optimisticLock ?>';
    }
<?php } ?>

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label) { ?>
<?php if (!in_array($name, $generator->skippedColumns)) { ?>
            <?= "'$name' => ".$generator->generateString($label).",\n" ?>
<?php } ?>
<?php } ?>
        ];
    }
<?php foreach ($relations as $name => $relation) { ?>
    <?php if (!in_array($name, $generator->skippedRelations)) { ?>

    /**
     * @return ActiveQuery
     */
    public function get<?= ucfirst($name) ?>()
    {
        <?= $relation[0]."\n" ?>
    }
    <?php } ?>
<?php } ?>
<?php if ($generator->createdAt || $generator->updatedAt
        || $generator->createdBy || $generator->updatedBy
        || $generator->UUIDColumn) {
    echo "\n"; ?>
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return <?= $isTree ? 'array_merge(parent::behaviors(), ' : ''; ?>[
<?php if ($generator->createdAt || $generator->updatedAt) { ?>
            'timestamp' => [
                'class' => MongoDateBehavior::class,
<?php if (!empty($generator->createdAt)) { ?>
                'createdAtAttribute' => '<?= $generator->createdAt?>',
<?php } else { ?>
                'createdAtAttribute' => false,
<?php } ?>
<?php if (!empty($generator->updatedAt)) { ?>
                'updatedAtAttribute' => '<?= $generator->updatedAt?>',
<?php } else { ?>
                'updatedAtAttribute' => false,
<?php } ?>
            ],
<?php } ?>
<?php if ($generator->createdBy || $generator->updatedBy) { ?>
            'blameable' => [
                'class' => BlameableBehavior::class,
<?php if (!empty($generator->createdBy)) { ?>
                'createdByAttribute' => '<?= $generator->createdBy?>',
<?php } else { ?>
                'createdByAttribute' => false,
<?php } ?>
<?php if (!empty($generator->updatedBy)) { ?>
                'updatedByAttribute' => '<?= $generator->updatedBy?>',
<?php } else { ?>
                'updatedByAttribute' => false,
<?php } ?>
<?php if (!empty($generator->blameableValue) && $generator->blameableValue != '\\Yii::$app->user->id') { ?>
                'value' => <?= $generator->blameableValue?>,
<?php } ?>
            ],
<?php } ?>
<?php if ($generator->UUIDColumn) { ?>
            'uuid' => [
                'class' => UUIDBehaviorUUID4::class,
<?php if (!empty($generator->UUIDColumn)) { ?>
                'column' => '<?= $generator->UUIDColumn?>',
<?php } ?>
            ],
<?php } ?>
<?php if (count($generator->fileFields) > 0) {
        foreach ($generator->fileFields as $fileField) {
            ?>
            [
                'class' => UploadBehavior::class,
                'attribute' => '<?= $fileField ?>',
                'scenarios' => ['insert', 'update'],
                'path' => '@webroot/files',
                'url' => '@web/files',
            ],
	<?php
        }
    } ?>]<?= $isTree ? ')' : '' ?>;
    }
<?php
} ?>
<?php if ($queryClassName) { ?>
<?php
    $queryClassFullName = '\\'.$generator->queryNs.'\\'.$queryClassName;
    echo "\n";
?>
<?php if ($generator->deletedBy) { ?>
    /**
     * The following code shows how to apply a default condition for all queries:
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->where(['deleted' => false]);
     *     }
     * }
     *
     * // Use andWhere()/orWhere() to apply the default condition
     * // SELECT FROM customer WHERE `deleted`=:deleted AND age>30
     * $customers = Customer::find()->andWhere('age>30')->all();
     *
     * // Use where() to ignore the default condition
     * // SELECT FROM customer WHERE age>30
     * $customers = Customer::find()->where('age>30')->all();
     * ```
     */
<?php } ?>

    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
<?php if ($generator->deletedBy) { ?>
        $query = new <?= $queryClassFullName ?>(get_called_class());
        return $query->where(['<?= $generator->deletedBy ?>' => null]);
<?php } else { ?>
        return new <?= $queryClassFullName ?>(get_called_class());
<?php } ?>
    }
<?php } ?>

	/**
     * @param $name string name
	 * @param $value string value from Excel
     * @return mixed processed value to save
     */
	 public function processImport($name, $value)
	 {
		 //return raw value by default
		 return $value;
	 }
}
