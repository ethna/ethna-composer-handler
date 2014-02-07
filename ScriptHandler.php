<?php
namespace Ethna\ComposerHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildProject(Event $event)
    {
        var_dump($event->getComposer()->getPackage());
    }
}