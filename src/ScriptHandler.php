<?php

namespace Diarmuidie\EnvPopulate;

use Diarmuidie\EnvPopulate\Processor;
use Diarmuidie\EnvPopulate\File\Factory\EnvFactory;
use Composer\Script\Event;

class ScriptHandler
{
    const EXTRA_KEY = 'env-process';

    public static function populateEnv(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();
        $config = array();

        if (isset($extras[self::EXTRA_KEY]) && is_array($extras[self::EXTRA_KEY])) {
            $config = $extras[self::EXTRA_KEY];
        }

        $envFileFactory = new EnvFactory();

        $processor = new Processor($event->getIO(), $envFileFactory);
        $processor->processFile($config);
    }
}
