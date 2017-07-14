<?php
/**
     * This is the template for generating the ActiveQuery class.
     */

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */
/* @var $className string class name */
/* @var $relations array relations of Model class */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->nsModel !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->queryNs . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
<?php foreach($relations as $rel): ?>
        $this->joinwith(['namarelasi', true,])->where(['deleted_by' => '0']);
<?php endforeach; ?>
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}