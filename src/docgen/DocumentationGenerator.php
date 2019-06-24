<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 16/08/18
 * Time: 01:32 AM.
 */

namespace inquid\enhancedgii\docgen;

use mikehaertl\shellcommand\Command;
use Yii;
use yii\helpers\Url;

class DocumentationGenerator
{
    private $script = "#!/bin/bash\n";
    public $module = 'pagos-construccion';
    public $models = ['categorias', 'recibo'];
    public $resolution = '1024x768';

    /**
     * DocumentationGenerator constructor.
     *
     * @param string $module
     * @param array  $models
     * @param string $resolution
     */
    public function __construct($module, array $models, $resolution = '1024x768')
    {
        $this->module = $module;
        $this->models = $models;
        $this->resolution = $resolution;
    }

    public function generateScreenshots()
    {
        $cookies = Yii::$app->request->cookies;
        $this->script .= 'pageres '.Url::base(true)."/{$this->module}/default/index {$this->resolution} --cookie='_csrf=".$cookies->get('_csrf')."' --cookie='PHPSESSID=".$_COOKIE['PHPSESSID']."' --filename=manual/images/'{$this->module}-index'\n";
        foreach ($this->models as $model) {
            $this->script .= 'pageres '.Url::base(true)."/{$this->module}/{$model}/index {$this->resolution} --cookie='_csrf=".$cookies->get('_csrf')."' --cookie='PHPSESSID=".$_COOKIE['PHPSESSID']."' --filename=manual/images/'{$model}-index'\n";
            $this->script .= 'pageres '.Url::base(true)."/{$this->module}/{$model}/create {$this->resolution} --cookie='_csrf=".$cookies->get('_csrf')."' --cookie='PHPSESSID=".$_COOKIE['PHPSESSID']."' --filename=manual/images/'{$model}-create'\n";
        }
        file_put_contents('screen.sh', $this->script);
    }

    public function createManual()
    {
        $manual = "# Manual del Usuario\n";
        $manual .= '## Aplicación '.Yii::$app->name."\n";
        $manual .= "![Imágen del Módulo](images/{$this->module}-index.png)"."\n";
        file_put_contents('manual/index.md', $manual);

        foreach ($this->models as $model) {
            $manual = "# {$model}\n";
            $manual .= "## Listar Registros\n";
            $manual .= "![Index](images/{$model}-index.png)"."\n";
            $manual .= "## Agregar Registros\n";
            $manual .= "![Index](images/{$model}-create.png)"."\n";
            file_put_contents("manual/{$model}.md", $manual);
        }
    }

    public function compileGuide($pdf = false)
    {
        $command = new Command(' ../vendor/bin/apidoc guide manual/ ./manual/final/ '.$pdf ? '--template=pdf' : '');
        if ($command->execute()) {
            return $command->getOutput();
        } else {
            $exitCode = $command->getExitCode();

            return $command->getError();
        }
    }

    public function compileApi($pdf = false)
    {
        $command = new Command(" ../modules/{$this->module} api manual/ ./manual/final/ ".$pdf ? '--template=pdf' : '');
        if ($command->execute()) {
            return $command->getOutput();
        } else {
            $exitCode = $command->getExitCode();

            return $command->getError();
        }
    }
}
