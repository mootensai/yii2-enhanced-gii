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
echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>\base;

use Yii;
<?php if ($generator->createdAt || $generator->updatedAt): ?>
use yii\behaviors\TimestampBehavior;
<?php endif; ?>
<?php if ($generator->createdBy || $generator->updatedBy): ?>
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
<?= (!$isTree) ? "    use \\mootensai\\relation\\RelationTrait;\n" : "" ?>

<?php if($generator->deletedBy): ?>
    private $_rt_softdelete = [
        '<?= $generator->deletedBy ?>' => <?= (empty($generator->deletedByValue)) ? 1 : $generator->deletedByValue ?>,
<?php if($generator->deletedAt): ?>
        '<?= $generator->deletedAt ?>' => <?= (empty($generator->deletedAtValue)) ? 1 : $generator->deletedAtValue ?>,
<?php endif; ?>
    ];
    private $_rt_softrestore = [
        '<?= $generator->deletedBy ?>' => <?= (empty($generator->deletedByValueRestored)) ? 0 : $generator->deletedByValueRestored ?>,

<?php if($generator->deletedAt): ?>
        '<?= $generator->deletedAt ?>' => <?= (empty($generator->deletedAtValueRestored)) ? 0 : $generator->deletedAtValueRestored ?>,
<?php endif; ?>
    ];
<?php endif; ?>
<?php if (!$isTree): ?>

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [<?= "\n            '" . implode("',\n            '", array_keys($relations)) . "'\n        " ?>];
    }

<?php endif; ?>
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
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
<?php if (!empty($generator->optimisticLock)): ?>

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
    <?php if(!in_array($name, $generator->skippedRelations)): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= ucfirst($name) ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($generator->createdAt || $generator->updatedAt
        || $generator->createdBy || $generator->updatedBy
        || $generator->UUIDColumn):
    echo "\n"; ?>/**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return <?= ($isTree) ? "array_merge(parent::behaviors(), " : ""; ?>[
<?php if ($generator->createdAt || $generator->updatedAt):?>
            'timestamp' => [
                'class' => TimestampBehavior::className(),
<?php if (!empty($generator->createdAt)):?>
                'createdAtAttribute' => '<?= $generator->createdAt?>',
<?php else :?>
                'createdAtAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->updatedAt)):?>
                'updatedAtAttribute' => '<?= $generator->updatedAt?>',
<?php else :?>
                'updatedAtAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->timestampValue) && $generator->timestampValue != 'time()'):?>
                'value' => <?= $generator->timestampValue?>,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($generator->createdBy || $generator->updatedBy):?>
            'blameable' => [
                'class' => BlameableBehavior::className(),
<?php if (!empty($generator->createdBy)):?>
                'createdByAttribute' => '<?= $generator->createdBy?>',
<?php else :?>
                'createdByAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->updatedBy)):?>
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
<?php if (!empty($generator->UUIDColumn)):?>
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
<?php if( $generator->deletedBy): ?>
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
<?php endif; ?>

    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
<?php if($generator->deletedBy): ?>
        $query = new <?= $queryClassFullName ?>(get_called_class());
        return $query->where(['<?= $generator->deletedBy ?>' => <?= $generator->deletedByValueRestored ?>]);
<?php else: ?>
        return new <?= $queryClassFullName ?>(get_called_class());
<?php endif; ?>
    }
<?php endif; ?>
}
