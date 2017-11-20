<?php

namespace Diarmuidie\EnvPopulate;

use Diarmuidie\EnvPopulate\File\Factory\EnvFactory;
use Composer\Script\Event;

class ScriptHandler
{
    const EXTRA_KEY = 'env-populate';

    public static function populateEnv(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();
        $config = array();

        if (isset($extras[self::EXTRA_KEY]) && is_array($extras[self::EXTRA_KEY])) {
            $config = $extras[self::EXTRA_KEY];
        }

        if (isset($config['run-non-interactively'])
            && !$config['run-non-interactively']
            && !$event->getIO()->isInteractive()
        ) {
            return;
        }

        $envFileFactory = new EnvFactory();

        $processor = new Processor($event->getIO(), $envFileFactory);

        if (isset($config['files']) && is_array($config['files'])) {
            foreach ($config['files'] as $file) {
                $processor->processFile($file);
            }
        } else {
            # Process legacy config file
            $processor->processFile($config);
        }
    }
}
