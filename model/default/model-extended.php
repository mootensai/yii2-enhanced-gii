<?php
/**
 * This is the template for generating the model class of a specified table.
 *
 * @var yii\web\View $this
 * @var mootensai\enhancedgii\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 * @var yii\db\TableSchema $tableSchema
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 */

echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>;

use Yii;
use \<?= $generator->nsModel ?>\base\<?= $className ?> as Base<?= $className ?>;

/**
* This is the model class for table "<?= $tableName ?>".
*/
class <?= $className ?> extends Base<?= $className . "\n" ?>
{
<?php foreach ($generator->tableSchema->columns as $column) {
    if ($generator->containsAnnotation($column, "@file")) {
        echo "public $" . $column->name . "File;\n";
        echo "if (" . $column->name . "File != null) {
                $column->name = 'parte_' . $model->no_parte;
                $model->filePicture->saveAs('images/partes/' . $partePicture . '.' . $model->filePicture->extension);
                $model->imagen = $partePicture . '.' . $model->filePicture->extension;
            }";
    } elseif ($generator->containsAnnotation($column, "@image")) {
        echo "public $" . $column->name . "Image;\n";
    }
} ?>
<?php if ($generator->generateAttributeHints): ?>
    /**
    * @inheritdoc
    */
    public function attributeHints()
    {
    return [
    <?php foreach ($labels as $name => $label): ?>
        <?php if (!in_array($name, $generator->skippedColumns)): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
        <?php endif; ?>
    <?php endforeach; ?>
    ];
    }
<?php endif; ?>
}
