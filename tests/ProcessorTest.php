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
        $this->file->method('fileExists')
            ->willReturn(false);

        $this->fileFactory->method('create')
            ->willReturn($this->file);

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

        $this->file->method('fileExists')
            ->willReturn(true);
        $this->file->method('getVariables')
            ->willReturn($exampleValues);

        $this->fileFactory->method('create')
            ->willReturn($this->file);

        $this->composerIO->expects($this->never())
            ->method('write');

        $processor = new Processor($this->composerIO, $this->fileFactory);
        $processor->processFile(array());
    }

    public function testAsksForValueIfValueNeedsToBeUpdated()
    {

        $exampleFile = $this->file;

        $exampleFile->expects($this->once())
            ->method('fileExists')
            ->willReturn(true);
        $exampleFile->method('getVariables')
            ->willReturn(array('VALUE_1' => true));

        $generatedFile = $this->getMockBuilder(Env::class)
            ->setConstructorArgs(array('file.env'))
            ->getMock();
        $generatedFile->expects($this->once())
            ->method('fileExists')
            ->willReturn(false);
        $generatedFile->method('getVariables')
            ->willReturn(array());
        $generatedFile->expects($this->once())
            ->method('save');

        $this->fileFactory->method('create')
            ->will($this->onConsecutiveCalls($exampleFile, $generatedFile));

        $this->composerIO
            ->method('isInteractive')
            ->willReturn(true);
        $this->composerIO->expects($this->once())
            ->method('write');
        $this->composerIO->expects($this->once())
            ->method('ask')
            ->with($this->equalTo('<question>VALUE_1</question> (<comment>true</comment>): '));

        $processor = new Processor($this->composerIO, $this->fileFactory);
        $processor->processFile(array());
    }
}
