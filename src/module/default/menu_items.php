<?php declare(strict_types=1);

/** @var string $moduleID */
echo "<?php
/**
 * List all the controllers and actions you need in your module
 */
return [
  ['label' => 'Index', 'url' => [\"/{$moduleID}/index\"], 'visible' => Yii::\$app->user->identity->isAdmin]
];";
