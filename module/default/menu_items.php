<?php echo "<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2019-04-21
 * Time: 01:38
 */
return [['label' => 'Index', 'url' => [\"/{$moduleID}/index\"], 'visible' => Yii::$app->user->identity->isAdmin]];";