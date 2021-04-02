<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/**
 * @var yii\web\View $this
 * @var mootensai\enhancedgii\crud\Generator $generator
 * @var string $className class name
 * @var string $modelClassName related model class name
 */

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
