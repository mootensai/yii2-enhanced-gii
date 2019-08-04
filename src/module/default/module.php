<?php declare(strict_types=1);
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator inquid\enhancedgii\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

namespace <?= $ns ?>;

use Yii;

/**
 * <?= $generator->moduleID ?> module definition class
 */
class <?= $className ?> extends \yii\base\Module
{
    public $menu = [];

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        Yii::configure($this, require(__DIR__ . '/config/config.php'));
        if (isset(Yii::$app->user->identity))
            $this->menu = ['label' => 'Opciones',
                'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin,
                'url' => ['/<?= $generator->moduleID ?>/default/index'],
                'template' => '<a href="{url}">{label}<i class="fa fa-angle-left pull-right"></i></a>',
                'items' => 
                    <?= 'include(\'menu_items.php\')' ?>
                ,
            ];
        // custom initialization code goes here
    }
}
