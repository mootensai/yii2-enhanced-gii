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

class <?= $testName ?>UnitTest extends \Codeception\Test\Unit
{
    public $id = 1;

    public function testCreate()
    {
        $faker = Factory::create();
        $construction = new Construction();
        $construction->name = $faker->text(10);

        $construction->save();
        expect_that(empty($construction->getErrors()));
    }

    public function testUpdate()
    {
        $faker = Factory::create();
        $construction = new Construction();
        $construction->name = $faker->text(10);

        $construction->save();
        expect_that(empty($construction->getErrors()));
    }

    public function testView()
    {
        $id = $this->id;
        $construction = \app\modules\construcciones\models\base\Construction::find()->where(['id' => $id])->one();
        expect_that($construction !== null);
    }

    public function testList()
    {
        expect_not(empty(Construction::find()->all()));
    }

    public function testDelete()
    {
        $id = $this->id;
        $construction = Construction::find()->where(['id' => $id])->one();
        expect_that($construction->delete());
    }

}
