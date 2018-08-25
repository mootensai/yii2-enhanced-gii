<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 16/08/18
 * Time: 01:32 AM
 */

namespace inquid\enhancedgii\app_template\component;

use Yii;
use mikehaertl\shellcommand\Command;


class AppTemplate
{

    public $appFolder = "inquid_app";
    public $repo = "";
    public $path = "";

    /**
     * DocumentationGenerator constructor.
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
     * @return string
     */
    public function createApp()
    {
        $command = new Command(" cd {$this->path} && git clone {$this->repo} {$this->appFolder}");
        if ($command->execute()) {
            return $command->getOutput();
        } else {
            $exitCode = $command->getExitCode();
            return $command->getError();
        }
    }

}
