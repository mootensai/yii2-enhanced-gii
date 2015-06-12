<?php
/**
 * This is the template for generating the trait used for the generated model.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->nsTrait ?>;

use Yii;

/**
 * This is the trait for all models ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
trait Relation
{
    public function loadWithRelation($POST) {
        if ($this->load($POST)) {
            foreach ($this->relations() as $name => $relation) {
                if (isset($POST[$relation[2]])) {
                    $container = [];
                    $relationPKAttr = $relation[1]::getTableSchema()->primaryKey[0];
                    foreach ($POST[$relation[2]] as $rModel) {
                        $tmp = (!empty($rModel[$relationPKAttr])) ? $relation[1]::findOne($rModel[$relationPKAttr]) : new $relation[1];
                        $tmp->load([$relation[2] => $rModel]);
                        $container[] = $tmp;
                    }
                    $this->populateRelation($name, $container);
                }
            }
            return true;
        }
        return false;
    }

    public function saveWithRelation() {
        $db = $this->getDb();
        $trans = $db->beginTransaction();
        try {
            $error = 0;
            //save parent
            if ($this->save()) {
                foreach ($this->relations() as $name => $relation) {
                    $relationPKAttr = $relation[1]::getTableSchema()->primaryKey[0];
                    $ids = [];
                    $deleteString = [];
                    foreach ($this->$name as $rModel) {
                        //if hasMany
                        if ($relation[0]) {
                            //set child row Foreign Key to parent PK
                            foreach ($relation[3] as $relFK => $id) {
                                if (array_key_exists($relFK, $rModel->attributes)) {
                                    $rModel->$relFK = $this->$id;
                                    $deleteString[] = " $relFK = '".$this->$id."'";
                                }
                            }
                            //try to save child row, if not success add error to parent class & set $error to true
                            if (!$rModel->save()) {
                                foreach ($rModel->errors as $error) {
                                    foreach ($error as $value) {
                                        $this->addError($relation[2] . " : " . $value);
                                    }
                                }
                                $error = 1;
                            }
                            //get the deleted child row ID by users
                            if (!empty($rModel->$relationPKAttr)) {
                                $ids[] = $rModel->$relationPKAttr;
                            }
                        }
                    }
                    //delete the deleted child row ID by users
                    if (!empty($ids)) {
                        $ids = implode(', ', $ids);
                        $deleteString = implode("AND ", $deleteString);
                        print_r($relationPKAttr . " NOT IN('$ids') AND $deleteString");
                        $relation[1]::deleteAll($relationPKAttr . " NOT IN('$ids') AND $deleteString");
                    }
                }
            } else {
                $error = 1;
            }
            if ($error) {
                $trans->rollback();
                return false;
            }
            $trans->commit();
            return true;
        } catch (Exception $e) {
            $trans->rollBack();
            throw $e;
        }
    }
}
