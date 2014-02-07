<?php
namespace Ethna\ComposerHandler\Tests;

use Ethna\ComposerHandler\ScriptHandler;
use Prophecy\PhpUnit\ProphecyTestCase;

class ScriptHandlerTest extends ProphecyTestCase
{
    protected $event;
    protected $io;
    protected $package;

    protected function setUp()
    {
        parent::setUp();

        $this->event = $this->prophesize('Composer\Script\Event');
        $this->io = $this->prophesize('Composer\IO\IOInterface');
        $this->package = $this->prophesize('Composer\Package\PackageInterface');
        $composer = $this->prophesize('Composer\Composer');

        $composer->getPackage()->willReturn($this->package);
        $this->event->getComposer()->willReturn($composer);
        $this->event->getIO()->willReturn($this->io);
    }


    public function testHoge()
    {
        $this->package->getExtra()->willReturn(array(
            "ethna" => array(
                array('ethna-app-name' => 'hoge'),
                "hoge"
            )
        ));

        ScriptHandler::buildProject($this->event->reveal());
    }
}