<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator dee\gii\generators\migration\Generator */
/* @var $migrationName string migration name */

echo "<?php\n";
?>

use yii\db\Schema;

class <?= $migrationName ?> extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
<?php foreach ($tables as $table): ?>
        
        $this->createTable('<?= $table['name'] ?>', [
<?php foreach ($table['columns'] as $column => $definition): ?>
            <?= "'$column' => $definition"?>,
<?php endforeach;?>
<?php if(isset($table['primary'])): ?>
            <?= "'{$table['primary']}'" ?>,
<?php endif; ?>
<?php foreach ($table['relations'] as $definition): ?>
            <?= "'$definition'" ?>,
<?php endforeach;?>
        ], $tableOptions);
<?php endforeach;?>
    }

    public function down()
    {
<?php foreach (array_reverse($tables) as $table): ?>
        $this->dropTable('<?= $table['name'] ?>');
<?php endforeach;?>
    }
}
