<?php declare(strict_types=1);
/*
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator inquid\enhancedgii\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

use yii\web\Controller;

/**
 * Default controller for the `<?= $generator->moduleID ?>` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
