<?php
namespace Ethna\ComposerHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildProject(Event $event)
    {
        $event->getComposer()->getPackage()->getExtra();

        $processor = new Processor($event->getIO());
        $processor->perform(array(
            "project" => "example",
        ));
    }
}