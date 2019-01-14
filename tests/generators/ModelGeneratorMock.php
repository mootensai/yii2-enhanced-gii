<?php

namespace yiiunit\gii\generators;

use yii\gii\generators\model\Generator;

/**
 * Just a mock for testing porpouses.
 *
 * @author Sidney Lins slinstj@gmail.com
 */
class ModelGeneratorMock extends Generator
{
    public function publicGenerateClassName($tableName, $useSchemaName = null)
    {
        return $this->generateClassName($tableName, $useSchemaName);
    }
}
