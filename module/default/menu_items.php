<?php echo "<?php
/**
 * List all the controllers and actions you need in your module
 */
return [
  ['label' => 'Index', 'url' => [\"/{$moduleID}/index\"], 'visible' => Yii::\$app->user->identity->isAdmin]
];";
