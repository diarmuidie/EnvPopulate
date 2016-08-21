<?php

namespace Diarmuidie\EnvPopulate\File\Factory;

use Diarmuidie\EnvPopulate\File\Env;

class EnvFactory extends FileFactory
{
    public function create($filename)
    {
        return new Env($filename);
    }
}
