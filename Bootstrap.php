<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2014 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mootensai\enhancedgii;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package schmunk42\giiant
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) {
//            if (!isset($app->getModule('gii')->generators['enhanced-model'])) {
//                $app->getModule('gii')->generators['enhanced-model'] = 'mootensai\enhancedgii\model\Generator';
//            }
            if (!isset($app->getModule('gii')->generators['enhanced-gii'])) {
                $app->getModule('gii')->generators['enhanced-gii-model'] = 'mootensai\enhancedgii\model\Generator';
                $app->getModule('gii')->generators['enhanced-gii-crud'] = 'mootensai\enhancedgii\crud\Generator';
                $app->getModule('gii')->generators['enhanced-gii-nested'] = 'mootensai\enhancedgii\nested\Generator';
            }
        }
    }
}
