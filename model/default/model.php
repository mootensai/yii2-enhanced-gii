<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $isTree boolean */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

// Used to check if a feature is enabled (by the field being filled in) and if the field actually exists in the database
$enabled = new stdClass();
foreach (['deletedBy', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt', 'deletedBy', 'deletedAt', 'UUIDColumn', 'optimisticLock'] AS $check) $enabled->$check = ($generator->$check && isset($generator->tableSchema->columns[$generator->$check]));

echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>\base;

use Yii;
<?php if ($enabled->createdAt || $enabled->updatedAt): ?>
use yii\behaviors\TimestampBehavior;
<?php endif; ?>
<?php if ($enabled->createdBy || $enabled->updatedBy): ?>
use yii\behaviors\BlameableBehavior;
<?php endif; ?>
<?php if ($generator->UUIDColumn): ?>
use mootensai\behaviors\UUIDBehavior;
<?php endif; ?>

/**
 * This is the base model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
<?php if (!in_array($name, $generator->skippedRelations)): ?>
 * @property <?= '\\' . $generator->nsModel . '\\' . $relation[$generator::REL_CLASS] . ($relation[$generator::REL_IS_MULTIPLE] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= ($isTree) ? '\kartik\tree\models\Tree' . "\n" : '\\' . ltrim($generator->baseModelClass, '\\') . "\n" ?>
{
<?= (!$isTree) ? "  use \\mootensai\\relation\\RelationTrait;\n" : "" ?>

<?php if ($enabled->deletedBy): ?>
    private $_rt_softdelete;
    private $_rt_softrestore;

    public function __construct(){
        parent::__construct();
        $this->_rt_softdelete = [
            '<?= $generator->deletedBy ?>' => <?= (empty($generator->deletedByValue)) ? 1 : $generator->deletedByValue ?>,
<?php if ($enabled->deletedAt): ?>
            '<?= $generator->deletedAt ?>' => <?= (empty($generator->deletedAtValue)) ? 1 : $generator->deletedAtValue ?>,
<?php endif; ?>
        ];
        $this->_rt_softrestore = [
            '<?= $generator->deletedBy ?>' => <?= (empty($generator->deletedByValueRestored)) ? 0 : $generator->deletedByValueRestored ?>,
<?php if ($enabled->deletedAt): ?>
            '<?= $generator->deletedAt ?>' => <?= (empty($generator->deletedAtValueRestored)) ? 0 : $generator->deletedAtValueRestored ?>,
<?php endif; ?>
        ];
    }
<?php endif; ?>
<?php if (!$isTree): ?>

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public static function relationNames()
    {
        return [<?= "\n\t\t\t'" . implode("',\n\t\t\t'", array_keys($relations)) . "'\n\t\t" ?>];
    }

<?php endif; ?>
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n\t\t\t" . implode(",\n\t\t\t", $rules) . "\n\t\t" ?>];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>
<?php if ($enabled->optimisticLock): ?>

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
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
<?php if (!in_array($name, $generator->skippedColumns)): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endif; ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>
    <?php if (!in_array($name, $generator->skippedRelations)): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= ucfirst($name) ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($enabled->createdAt || $enabled->updatedAt
        || $enabled->createdBy || $enabled->updatedBy
        || $enabled->UUIDColumn):
    echo "\n"; ?>
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return <?= ($isTree) ? "array_merge(parent::behaviors(), " : ""; ?>[
<?php if ($enabled->createdAt || $enabled->updatedAt):?>
            'timestamp' => [
                'class' => TimestampBehavior::className(),
<?php if ($enabled->createdAt):?>
                'createdAtAttribute' => '<?= $generator->createdAt?>',
<?php else :?>
                'createdAtAttribute' => false,
<?php endif; ?>
<?php if ($enabled->updatedAt):?>
                'updatedAtAttribute' => '<?= $generator->updatedAt?>',
<?php else :?>
                'updatedAtAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->timestampValue) && $generator->timestampValue != 'time()'):?>
                'value' => <?= $generator->timestampValue?>,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($enabled->createdBy || $enabled->updatedBy):?>
            'blameable' => [
                'class' => BlameableBehavior::className(),
<?php if ($enabled->createdBy):?>
                'createdByAttribute' => '<?= $generator->createdBy?>',
<?php else :?>
                'createdByAttribute' => false,
<?php endif; ?>
<?php if ($enabled->updatedBy):?>
                'updatedByAttribute' => '<?= $generator->updatedBy?>',
<?php else :?>
                'updatedByAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->blameableValue) && $generator->blameableValue != '\\Yii::$app->user->id'):?>
                'value' => <?= $generator->blameableValue?>,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($generator->UUIDColumn):?>
            'uuid' => [
                'class' => UUIDBehavior::className(),
<?php if (!empty($generator->UUIDColumn) && isset($generator->tableSchema->columns[$generator->UUIDColumn])):?>
                'column' => '<?= $generator->UUIDColumn?>',
<?php endif; ?>
            ],
<?php endif; ?>
        ]<?= ($isTree) ? ")" : "" ?>;
    }
<?php endif; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
<?php if ($enabled->deletedBy): ?>
    /**
     * The following code shows how to apply a default condition for all queries:
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *   public static function find()
     *   {
     *       return parent::find()->where(['deleted' => false]);
     *   }
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
<?php endif; ?>

    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
<?php if ($enabled->deletedBy): ?>
        $query = new <?= $queryClassFullName ?>(get_called_class());
        return $query->where(['<?= $tableName ?>.<?= $generator->deletedBy ?>' => <?= $generator->deletedByValueRestored ?>]);
<?php else: ?>
        return new <?= $queryClassFullName ?>(get_called_class());
<?php endif; ?>
    }
<?php endif; ?>
}