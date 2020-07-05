<?php
/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2014 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace inquid\enhancedgii;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap.
 *
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
            if (!isset($app->getModule('gii')->generators['enhanced-gii'])) {
                $app->getModule('gii')->generators['enhanced-gii-repo'] = 'inquid\enhancedgii\repo\Generator';
                $app->getModule('gii')->generators['enhanced-gii-app_template'] = 'inquid\enhancedgii\app_template\Generator';
                $app->getModule('gii')->generators['enhanced-gii-model'] = [
                    'class'     => 'inquid\enhancedgii\model\Generator',
                    'templates' => [ //setting for out templates
                        'default' => '@vendor/inquid/yii2-enhanced-gii/src/model/default',
                        'mongo'   => '@vendor/inquid/yii2-enhanced-gii/src/model/mongo',
                    ],
                ];
                $app->getModule('gii')->generators['enhanced-gii-crud']['class'] = 'inquid\enhancedgii\crud\Generator';
                $app->getModule('gii')->generators['enhanced-gii-module'] = 'inquid\enhancedgii\module\Generator';
                $app->getModule('gii')->generators['enhanced-gii-testsgenerator'] = 'inquid\enhancedgii\testsgenerator\Generator';
                $app->getModule('gii')->generators['enhanced-gii-migration'] = 'inquid\enhancedgii\migration\Generator';
            }
        }
    }
}
