<?php
namespace Ethna\ComposerHandler;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\IOInterface;
use Composer\Plugin\CommandEvent;

class Processor
{
    /** @var IOInterface $io */
    protected $io;

    /** @var Composer $composer */
    protected $composer;

    protected $messages = array(
        "project" => "project name",
        "renderer" => "default renderer",
    );

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
    }

    public function perform($configs)
    {
        if (is_file(".ethna")) {
            return;
        }

        $config = array_merge(array(
            "project" => "example",
            "renderer" => "smarty"
        ), $configs);

        $config = $this->processParams($config, $config);

        $targets = array(
            "app/action/Index.php",
            "app/view/Index.php",
            "app/Example_ActionClass.php",
            "app/Example_ActionForm.php",
            "app/Example_Controller.php",
            "app/Example_Error.php",
            "app/Example_ViewClass.php",
            "etc/example-ini.php",
            "skel/skel.action.php",
            "skel/skel.action_cli.php",
            "skel/skel.app_object.php",
            "skel/skel.entry_cli.php",
            "skel/skel.entry_www.php",
            "skel/skel.template.tpl",
            "skel/skel.view.php",
            "www/index.php",
        );

        $project_class = self::camerize($config['project']);
        if ($config['renderer'] == 'twig') {
            if (file_exists("app/Example_Controller.php")) {
                $data = file_get_contents("app/Example_Controller.php");
                $data = preg_replace("/Ethna_Renderer_Smarty/", "Ethna_Renderer_Twig", $data);
                file_put_contents("app/Example_Controller.php", $data);
            }
        }

        foreach ($targets as $target) {
            if (file_exists($target)) {
                $data = file_get_contents($target);
                $data = preg_replace("/Example/", $project_class, $data);
                $data = preg_replace("/EXAMPLE/", strtoupper($project_class), $data);
                file_put_contents($target, $data);
                $name = basename($target);

                if (preg_match("/Example/", $name)) {
                    $name = preg_replace("/Example/", $project_class, $name);
                    $dir = dirname($target);
                    rename($target, $dir . DIRECTORY_SEPARATOR . $name);
                } else if (preg_match("/example-ini.php/", $name)) {
                    $dir = dirname($target);
                    $name = preg_replace("/example/", $config['project'], $name);
                    rename($target, $dir . DIRECTORY_SEPARATOR . $name);
                }
            }
        }

        file_put_contents(".ethna", sprintf("[project]\ncontroller_file = '%s'\ncontroller_class = '%s'",
            "app/{$project_class}_Controller.php",
            "{$project_class}_Controller"));

        if ($config['renderer'] == 'twig') {
            $file = Factory::getComposerFile();
            $prior_json = $composer_json = json_decode(file_get_contents($file), true);
            if (!isset($composer_json['require']['twig/twig'])) {
                $composer_json['require']['twig/twig'] = '1.*';
                file_put_contents($file, json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

                $composer = $this->composer = Factory::create($this->io, null, true);
                $composer->getDownloadManager()->setOutputProgress(true);
                $install = Installer::create($this->io, $composer);
                $install
                    ->setVerbose(true)
                    ->setPreferSource(true)
                    ->setPreferDist(false)
                    ->setDevMode(true)
                    ->setUpdate(true)
                    ->setUpdateWhitelist(array_keys($prior_json['require']));
                ;

                // とりあえず
                $status = $install->run();
            }
        }
    }

    protected static function camerize($name)
    {
        return str_replace(' ','', ucwords(str_replace('_', ' ', $name)));
    }

    protected function processParams($params, $actualParams)
    {
        if (!$this->io->isInteractive()) {
            return $actualParams;
        }
        $this->io->write('<comment>Some parameters are missing. Please provide them.</comment>');

        foreach ($params as $key => $value) {
            $result = $this->io->ask(
                sprintf("<question>%s</question> (<comment>%s</comment>): ", $this->messages[$key], $value)
            );

            if (!empty($result)) {
                $actualParams[$key] = $result;
            }
        }

        return $actualParams;
    }
}