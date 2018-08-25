<?php

namespace inquid\enhancedgii\app_template;

use inquid\enhancedgii\app_template\component\AppTemplate;
use Yii;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\db\Expression;

/**
 * This generator will generate migration file for the specified database table.
 *
 * @author Inquid INC <contact@inquid.co>
 * @since 0.9
 */
class Generator extends \yii\gii\Generator
{
    public $appName = 'db';
    public $path = '/opt/lampp/htdocs/';
    public $folderName = 'inquid_app';
    public $repo = 'inquid';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'App Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator Creates a new app from a given repo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['path', 'repo'], 'filter', 'filter' => 'trim'],
            [['appName'], 'required']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'appName' => 'Application Name',
            'path' => 'Directory Path',
            'repo' => 'Repository URL'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'appName' => 'Application Name',
            'path' => 'Directory Path',
            'repo' => 'Repository URL'
        ]);
    }

    public function generate()
    {
        $appName = new AppTemplate(strtolower(str_replace(' ', '_', $this->appName)), $this->repo, $this->path);
        $appName->createApp();
    }
}