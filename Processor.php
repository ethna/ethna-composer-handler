<?php
namespace Ethna\ComposerHandler;

use Composer\IO\IOInterface;

class Processor
{
    protected $io;

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    public function perform($configs)
    {
        $config = array_merge(array(
            "project" => "example"
        ), $configs);

        $config = $this->processParams($config, $config);
        var_dump($config);
    }

    protected function processParams($params, $actualParams)
    {
        if (!$this->io->isInteractive()) {
            return $actualParams;
        }

        foreach ($params as $key => $value) {
             $actualParams[$key] = $this->io->ask($this->io->ask("<question>Hello World</question> (<comment>hoge</comment>"));
        }
        return $actualParams;
    }
}