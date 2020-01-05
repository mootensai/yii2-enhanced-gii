<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator inquid\enhancedgii\crud\Generator */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->nsModel !== $generator->queryNs) {
    $modelFullClassName = '\\'.$generator->queryNs.'\\'.$modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

use yii\mongodb\ActiveQuery;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName."\n" ?>
 */
class <?= $className ?> extends ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>Query[]|array
     */
    public function all($db = null)
    {
        $result = parent::all($db);
        foreach ($result as $key => $item) {
            $result[$key]['_id'] = $result[$key]['_id']->__toString();
        }
        return $result;
    }

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>Query|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
