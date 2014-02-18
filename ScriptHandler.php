<?php
namespace Ethna\ComposerHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildProject(Event $event)
    {
        $processor = new Processor($event->getIO(), $event->getComposer());
        $processor->perform(array(
            /* NOTE(chobie): How do I get target install directory? */
            "project" => basename(getcwd()),
        ));
    }
}