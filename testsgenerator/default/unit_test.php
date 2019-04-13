<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator inquid\enhancedgii\migration\Generator */
/* @var $testName string migration name */

echo "<?php\n";
?>

use Faker\Factory;
use yii\helpers\Json;
use <?= $generator->nsModel ?>\<?=$className?>;

class <?= $testName ?> extends \Codeception\Test\Unit
{
    public $id = 1;

    public function testCreate()
    {
        $faker = Factory::create();
        $<?=lcfirst($className)?> = new <?=$className?>();

        <?php foreach ($tableSchema->columns as $column): ?>
            $<?=lcfirst($className)?>-><?= $column->name ?> = <?= $generator->generateFakerType($column) ?>;
        <?php endforeach; ?>

        $<?=lcfirst($className)?>->save();
        expect_that(empty($<?=lcfirst($className)?>->getErrors()));
    }

    public function testUpdate()
    {
        $faker = Factory::create();
        $<?=lcfirst($className)?> = new <?=$className?>();

        <?php foreach ($tableSchema->columns as $column): ?>
            $<?=lcfirst($className)?>-><?= $column->name ?> = <?= $generator->generateFakerType($column) ?>;
        <?php endforeach; ?>

        $<?=lcfirst($className)?>->save();
        expect_that(empty($<?=lcfirst($className)?>->getErrors()));
    }

    public function testView()
    {
        $id = $this->id;
        $<?=lcfirst($className)?> = <?=$className?>::find()->where(['<?=$tableSchema->primaryKey[0]?>' => $id])->one();
        expect_that($<?=lcfirst($className)?> !== null);
    }

    public function testList()
    {
        expect_not(empty(<?=$className?>::find()->all()));
    }

    public function testDelete()
    {
        $id = $this->id;
        $<?=lcfirst($className)?> = <?=$className?>::find()->where(['<?=$tableSchema->primaryKey[0]?>' => $id])->one();
        expect_that($<?=lcfirst($className)?>->delete());
    }

}
