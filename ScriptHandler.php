<?php
namespace Ethna\ComposerHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildProject(Event $event)
    {
        $event->getComposer()->getPackage()->getExtra();
        var_dump($event->getComposer());
        var_dump($event->getComposer()->getPackage()->getName());
        var_dump($event->getComposer()->getPackage()->getTargetDir());
        var_dump($event->getName());
        //var_dump($event-)

        $processor = new Processor($event->getIO());
        $processor->perform(array(
            "project" => "example",
        ));
    }
}