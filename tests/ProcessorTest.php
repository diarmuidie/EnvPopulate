<?php

namespace Diarmuidie\EnvPopulate\Tests;

use Diarmuidie\EnvPopulate\Processor;
use PHPUnit\Framework\TestCase;
use Composer\IO\IOInterface;
use Diarmuidie\EnvPopulate\File\Factory\FileFactory;
use Diarmuidie\EnvPopulate\File\Env;
use InvalidArgumentException;

class ProcessorTest extends TestCase
{
    protected $file;
    protected $composerIO;
    protected $fileFactory;

    public function setup()
    {
        chdir(__DIR__);

        $this->file = $this->getMockBuilder(Env::class)
            ->setConstructorArgs(array('file.env'))
            ->getMock();
        $this->composerIO = $this->getMockBuilder(IOInterface::class)
            ->getMock();
        $this->fileFactory = $this->getMockBuilder(FileFactory::class)
            ->getMock();
    }

    public function testInitialiseClass()
    {
        $processor = new Processor($this->composerIO, $this->fileFactory);

        $this->assertInstanceOf(Processor::class, $processor);
    }

    public function testThrowsExceptionForMissingExampleFile()
    {
        $this->fileFactory->method('create')
            ->willReturn($this->file);
        $this->file->method('fileExists')
            ->willReturn(false);

        $this->expectException(InvalidArgumentException::class);

        $processor = new Processor($this->composerIO, $this->fileFactory);
        $processor->processFile(array());
    }

    public function testDoesWriteOutputIfNoValueNeedsToBeUpdated()
    {
        $exampleValues = array(
            'VALUE_1' => '123',
            'VALUE_2' => 'true'
        );

        $this->fileFactory->method('create')
            ->willReturn($this->file);
        $this->file->method('fileExists')
            ->willReturn(true);
        $this->file->method('getVariables')
            ->willReturn($exampleValues);

        $this->composerIO->expects($this->never())
            ->method('write');

        $processor = new Processor($this->composerIO, $this->fileFactory);
        $processor->processFile(array());
    }

}