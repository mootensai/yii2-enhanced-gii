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
<?php if ($generator->createTableIfNotExists): ?>
        $tables = Yii::$app->db->schema->getTableNames();
<?php endif; ?>
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
<?php foreach ($tables as $table): 
        $tableRaw = trim($table['name'], '{}%');
        $t = '';
        if ($generator->createTableIfNotExists == 1) :
        $t = '  ';
    ?>
        if (!in_array(Yii::$app->db->tablePrefix.'<?= $tableRaw ?>', $tables))  {
<?php endif; ?>
        <?=$t?>$this->createTable('<?= $table['name'] ?>', [
<?php foreach ($table['columns'] as $column => $definition): ?>
            <?=$t?><?= "'$column' => $definition"?>,
<?php endforeach;?>
<?php if(isset($table['primary'])): ?>
            <?=$t?><?= "'{$table['primary']}'" ?>,
<?php endif; ?>
<?php foreach ($table['relations'] as $definition): ?>
            <?=$t?><?= "'$definition'" ?>,
<?php endforeach;?>
            <?=$t?>], $tableOptions);
        <?php if ($generator->createTableIfNotExists == 1) :?>
        } else {
          echo "\nTable `".Yii::$app->db->tablePrefix."<?= $tableRaw ?>` already exists!\n";
        }
         <?php endif; ?>
<?php endforeach;?>
        
    }

    public function down()
    {
<?php if ($generator->disableFkc) : ?>
        $this->execute('SET foreign_key_checks = 0');
<?php endif; ?>
<?php foreach (array_reverse($tables) as $table): ?>
        $this->dropTable('<?= $table['name'] ?>');
<?php endforeach;?>
<?php if ($generator->disableFkc) : ?>
        $this->execute('SET foreign_key_checks = 1');
<?php endif; ?>
    }
}
