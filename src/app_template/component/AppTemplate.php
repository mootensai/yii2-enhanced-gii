<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 16/08/18
 * Time: 01:32 AM.
 */

namespace inquid\enhancedgii\app_template\component;

use mikehaertl\shellcommand\Command;

class AppTemplate
{
    public $appFolder = 'inquid_app';
    public $repo = '';
    public $path = '';

    /**
     * DocumentationGenerator constructor.
     *
     * @param $app
     * @param $repo
     * @param $path
     */
    public function __construct($app, $repo, $path)
    {
        $this->appFolder = $app;
        $this->repo = $repo;
        $this->path = $path;
    }

    /**
     * @param bool $pdf
     *
     * @return string
     */
    public function createApp($updateDependencies)
    {
        $command = new Command(" cd {$this->path} && git clone {$this->repo} {$this->appFolder}");
        if ($command->execute()) {
            $command = new Command(" cd {$this->path} && composer update");
            $command->execute();

            return $command->getOutput();
        } else {
            $exitCode = $command->getExitCode();

            return $command->getError();
        }
    }

    public function createEnv()
    {
        $env = '# Framework
# ---------
YII_DEBUG   = true
YII_ENV     = dev

# Application
# -----------
LINK_ASSETS=true
APP_NAME = app_name

# Localization and Internalization
SOURCE_LANGUAGE = en-US
LANGUAGE = es
TIME_ZONE = America/Chicago


# Databases
# ---------
DB_DSN           = mysql:host=ip_host;port=3306;dbname=db_name
DB_DSN_PRUEBAS   = mysql:host=ip_host;port=3306;dbname=db_name
DB_USERNAME      = root
DB_PASSWORD      = password
DB_TABLE_PREFIX  = inq_

GOOGLE_PROJECT_NAME = project_id
SQL_INSTANCE_NAME   = sql
GOOGLE_BUCKET = bucket

TEST_DB_DSN           = mysql:host=localhost;port=3306;dbname=yii2-starter-kit-test
TEST_DB_USERNAME      = root
TEST_DB_PASSWORD      = root

# Urls
# ----
FRONTEND_HOST_INFO    = http://yii2-starter-kit.dev
BACKEND_HOST_INFO     = http://backend.yii2-starter-kit.dev
STORAGE_HOST_INFO     = http://storage.yii2-starter-kit.dev

# MAIL - GSUITE DEFAULT
# -----
SMTP_HOST = smtp.gmail.com
SMTP_USERNAME = mail@gmail.com
SMTP_PORT = 587
SMTP_PASSWORD = email_password
SMTP_ENCRYPTION = tls
ROBOT_EMAIL    = robot@yii2-starter-kit.dev

# GITHUB_CLIENT_ID = your-client-id
# GITHUB_CLIENT_SECRET = your-client-secret';
        file_put_contents("{$this->path}/{$this->appFolder}/.env", $env);
    }
}
