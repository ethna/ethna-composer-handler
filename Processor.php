<?php
namespace Ethna\ComposerHandler;

use Composer\IO\IOInterface;

class Processor
{
    /** @var \Composer\IO\IOInterface $io */
    protected $io;

    protected $messages = array(
        "project" => "project name",
        "renderer" => "default renderer",
    );

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    public function perform($configs)
    {
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
            "app/Example_UrlHandler.php",
            "app/Example_ViewClass.php",
            "etc/example-ini.php",
            "skel/skel.action.php",
            "skel/skel.action_cli.php",
            "skel/skel.action_test.php",
            "skel/skel.app_object.php",
            "skel/skel.entry_cli.php",
            "skel/skel.entry_www.php",
            "skel/skel.template.tpl",
            "skel/skel.view.php",
            "skel/skel.view_test.php",
            "www/index.php",
            "www/info.php",
            "www/xmlrpc.php",
        );

        $project_class = self::camerize($config['project']);
        foreach ($targets as $target) {
            $data = file_get_contents($target);
            $data = preg_replace("/Example/", $project_class, $data);
            file_put_contents($target, $data);
            $name = basename($target);

            if (preg_match("/Example/", $name)) {
                $name = preg_replace("/Example/", $project_class, $name);
                $dir = dirname($target);
                rename($target, $dir . DIRECTORY_SEPARATOR . $name);
            } else if (preg_match("/example-ini.php/", $name)) {
                $dir = dirname($target);
                rename($target, $dir . DIRECTORY_SEPARATOR . $config['project']);
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