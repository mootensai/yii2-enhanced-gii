<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 1/7/19
 * Time: 10:41 PM.
 */

use inquid\enhancedgii\crud\Generator;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var Generator $generator */
/** @var string $tableNameComment */
$componentClass = StringHelper::basename($generator->componentClass);
$modelClass = StringHelper::basename($generator->modelClass);

if ($generator->useTableComment) {
    $customName = $tableNameComment;
} else {
    $customName = Inflector::camel2id(StringHelper::basename($modelClass));
}

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->componentClass, '\\')) ?>;

use inquid\date_time\DateTimeHandler;
use inquid\pdf\FPDF;
use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;

/**
* <?= $componentClass ?> implements the PDF printable view for <?= $modelClass ?> model.
*/
class <?= $componentClass ?> extends FPDF
{
    /** @var array $color */
    private $color = ['5', '100', '36'];
    /** @var <?= $modelClass ?> */
    <?= 'public $' . strtolower($modelClass) . ";\n" ?>
<?php
echo "\tpublic function __construct(string \$orientation = 'P', string \$unit = 'mm', string \$size = 'A4')
        \t\t{
        \t\t\$this->".strtolower($modelClass). ' = ' .strtolower($modelClass)."::find()->where(['id'=> Yii::\$app->request->get('id')])->one();
        \t\tparent::__construct(\$orientation, \$unit, \$size);
    }";
?>
<?php
echo "\tpublic function Header(){
        \t\t\$this->SetTitle('".$customName."-' . \$this->".$modelClass."->id);
        \t\t\$this->SetFont('Arial', 'B', 12);
        \t\t\$this->SetFillColor(\$this->color[0], \$this->color[1], \$this->color[2]);
        \t\t\$this->Cell(40, 4, '', 0, 0, 'C');
        \t\t\$this->Cell(105, 4, utf8_decode('Título ...'), 0, 0, 'C');
\t}\n";
echo "\tpublic function Body(){
        \t\t\$this->AliasNbPages();
        \t\t\$this->AddPage();
        \t\t\$this->Ln(4);
        \t\t\$this->SetFillColor(\t\t\$this->color[0], \t\t\$this->color[1], \t\t\$this->color[2]);
        \t\t\$this->SetFont('Arial', 'B', 8);
        \t\t\$this->SetTextColor(255, 255, 255);
        \t\t\$this->Ln(2);   
\t}\n";
echo "\tpublic function Footer(){
                         // Position at 1.5 cm from bottom
        \t\t\$this->SetY(-40);
        \t\t\$this->SetFont('Arial', 'B', 8);
\t}\n";
echo "    /**
     * @return string
     */
    public function saveToFile()
    {
        \$this->Output('F', Yii::getAlias('@app/web/files/".strtolower($modelClass).'/'.Inflector::camel2id(StringHelper::basename($modelClass))."' . DateTimeHandler::getDateTime('Y-m-d') . '.pdf'));
        return Yii::getAlias('@app/web/files/".strtolower($modelClass).'/'.Inflector::camel2id(StringHelper::basename($modelClass))."-' . DateTimeHandler::getDateTime('Y-m-d') . '.pdf');
    }\n";
?>
}
