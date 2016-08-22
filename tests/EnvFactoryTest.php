<?php

namespace Diarmuidie\EnvPopulate\Tests;

use PHPUnit_Framework_TestCase;
use Diarmuidie\EnvPopulate\File\Factory\EnvFactory;
use Diarmuidie\EnvPopulate\File\Env;

class EnvFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactoryReturnsEnvClass()
    {
        $factory = new EnvFactory();

        $file = $factory->create('filename.txt');

        $this->assertInstanceOf(Env::class, $file);
    }
}
