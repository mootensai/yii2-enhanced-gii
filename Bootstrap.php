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
 * @package mootensai\yii2-enhanced-gii
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
//        \Yii::setAlias('@mtengii','@vendor/mootensai/yii2-enhanced-gii');
        if ($app->hasModule('gii')) {
            if (!isset($app->getModule('gii')->generators['enhanced-gii'])) {
                $app->getModule('gii')->generators['enhanced-gii-model'] = 'mootensai\enhancedgii\model\Generator';
                $app->getModule('gii')->generators['enhanced-gii-crud']['class'] = 'mootensai\enhancedgii\crud\Generator';
//                $app->getModule('gii')->generators['enhanced-gii-crud']['templates'] = [
//                    'default' => '@mtengii/crud/default',
//                    'nested' => '@mtengii/crud/nested'
//                ];
                $app->getModule('gii')->generators['enhanced-gii-migration'] = 'mootensai\enhancedgii\migration\Generator';
            }
        }
    }
}
