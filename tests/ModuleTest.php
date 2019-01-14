<?php

namespace yiiunit\gii;

use Yii;
use yii\gii\Module;

class ModuleTest extends TestCase
{
    public function testDefaultVersion()
    {
        Yii::$app->extensions['yiisoft/yii2-gii'] = [
            'name' => 'yiisoft/yii2-gii',
            'version' => '2.0.6',
        ];

        $module = new Module('gii');

        $this->assertEquals('2.0.6', $module->getVersion());
    }
}