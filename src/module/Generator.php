<?php

declare(strict_types=1);
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace inquid\enhancedgii\module;

use inquid\enhancedgii\BaseGenerator;
use inquid\enhancedgii\utils\DatabaseUtils;
use Yii;
use yii\gii\CodeFile;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This generator will generate the skeleton code needed by a module.
 *
 * @property string $controllerNamespace The controller namespace of the module. This property is read-only.
 * @property bool $modulePath The directory that contains the module class. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 *
 * @since 2.0
 */
class Generator extends BaseGenerator
{
    public $moduleClass;
    public $moduleID;
    public $databaseName = 'N/A';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'INQUID Generator (Module)';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator helps you to generate the skeleton code needed by a Yii module.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID', 'moduleClass'], 'filter', 'filter' => 'trim'],
            [['moduleID', 'moduleClass', 'db'], 'required'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['moduleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['moduleClass'], 'validateModuleClass'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'db'          => 'Database Connection ID',
            'moduleID'    => 'Module ID',
            'moduleClass' => 'Module Class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return [
            'db'          => 'This is the ID of the DB application component.',
            'moduleID'    => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\Module</code>.',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['db']);
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "The module has been generated successfully. You may $link.";
        }

        $output = <<<'EOD'
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->moduleID}' => [
            'class' => '{$this->moduleClass}',
        ],
    ],
    ......
EOD;

        return $output.'<pre>'.highlight_string($code, true).'</pre>';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['module.php', 'controller.php', 'view.php'];
    }

    /**
     * {@inheritdoc}
     * @throws \yii\base\UserException
     * @throws \yii\base\InvalidConfigException
     */
    public function generate(): array
    {
        $files = [];
        $db = $this->getDbConnection();
        $databaseUtils = new DatabaseUtils();
        $databaseUtils->dbConnection = $db;
        $modulePath = $this->getModulePath();
        $this->databaseName = $databaseUtils->getDatabaseName();

        $files[] = new CodeFile(
            $modulePath.'/'.StringHelper::basename($this->moduleClass).'.php',
            $this->render('module.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/controllers/DefaultController.php',
            $this->render('controller.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/views/default/index.php',
            $this->render('view.php', ['databaseName' => $this->databaseName])
        );
        $files[] = new CodeFile(
            $modulePath.'/menu_items.php',
            $this->render('menu_items.php', ['moduleID' => $this->moduleID, 'moduleClass' => $this->moduleClass])
        );
        $files[] = new CodeFile(
            $modulePath.'/config/config.php',
            $this->render('config.php', ['moduleID' => $this->moduleID, 'moduleClass' => $this->moduleClass])
        );

        return $files;
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass()
    {
        if (strpos($this->moduleClass, '\\') === false || Yii::getAlias('@'.str_replace('\\', '/', $this->moduleClass), false) === false) {
            $this->addError('moduleClass', 'Module class must be properly namespaced.');
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    /**
     * @return bool the directory that contains the module class
     */
    public function getModulePath()
    {
        return Yii::getAlias('@'.str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'))));
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\controllers';
    }
}
