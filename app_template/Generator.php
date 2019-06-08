<?php

namespace inquid\enhancedgii\app_template;

use inquid\enhancedgii\app_template\component\AppTemplate;
use Yii;
use yii\gii\CodeFile;

/**
 * This generator will generate migration file for the specified database table.
 *
 * @author Inquid INC <contact@inquid.co>
 *
 * @since 0.9
 */
class Generator extends \yii\gii\Generator
{
    public $appName = '';
    public $path = '/opt/lampp/htdocs/';
    public $folderName = 'inquid_app';
    public $repo = 'inquid';
    public $updateDependencies = false;

    public $language = 'es';
    public $time_zone = 'America/Mexico_City';
    public $date_time_format = 'Y-m-d H:i:s';
    public $thousandSeparator = ',';
    public $decimalSeparator = '.';
    public $currencyCode = '$';

    public $db_ip_host = '';
    public $db_ip_host_test = '';
    public $db_username = 'root';
    public $db_password = '';
    public $db_prefix = 'inq_';

    public $db_ip_port = 3306;
    public $db_name;
    public $db_ip_port_test;
    public $db_name_test;

    public $google_project;
    public $google_sql_instance_name;
    public $google_bucket;

    public $email_smtp_host = 'smtp.gmail.com';
    public $email_username = 'email@gmail.com';
    public $email_port = 587;
    public $email_password = '';
    public $email_robot = 'robot@gmail.com';
    public $email_encryption = 'tls';

    public $github_client_id = '';
    public $github_client_secret = '';

    public $confirm_with = 21600;
    public $cost = 12;
    public $admins;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'INQUID Generator (APP)';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator Creates a new app from a given repo';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['.env'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['path', 'repo'], 'filter', 'filter' => 'trim'],
            [['appName'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'appName' => 'Application Name',
            'path'    => 'Directory Path',
            'repo'    => 'Repository URL',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'appName' => 'Application Name',
            'path'    => 'Directory Path',
            'repo'    => 'Repository URL',
        ]);
    }

    public function generate()
    {
        $files = [];
        $appName = new AppTemplate(strtolower(str_replace(' ', '_', $this->appName)), $this->repo, $this->path);
        Yii::debug('Creating app' . $appName->createApp($this->updateDependencies));
        $files[] = new CodeFile("{$this->path}/{$this->appName}/.env", $this->render('.env'));

        return $files;
        //$appName->createEnv();
    }
}
