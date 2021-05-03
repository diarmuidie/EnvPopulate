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

    protected function setUp(): void
    {
        chdir(__DIR__);

        $this->file = $this->getMockBuilder('Diarmuidie\EnvPopulate\File\Env')
            ->setConstructorArgs(array('file.env'))
            ->getMock();
        $this->composerIO = $this->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $this->fileFactory = $this->getMockBuilder('Diarmuidie\EnvPopulate\File\Factory\FileFactory')
            ->getMock();
    }

    public function testInitialiseClass()
    {
        $processor = new Processor($this->composerIO, $this->fileFactory);

        $this->assertInstanceOf('Diarmuidie\EnvPopulate\Processor', $processor);
    }

    public function testThrowsExceptionForMissingExampleFile()
    {
        $this->file->method('fileExists')
            ->willReturn(false);

        $this->fileFactory->method('create')
            ->willReturn($this->file);

        $this->expectException(InvalidArgumentException::class);

        $processor = new Processor($this->composerIO, $this->fileFactory);
        $processor->processFile('example.file', 'generated.file');
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
        $processor->processFile('example.file', 'generated.file');
    }

    public function testDoesntAskForValueIfNotInInteractiveMode()
    {

        $exampleFile = $this->file;

        $exampleFile->expects($this->once())
            ->method('fileExists')
            ->willReturn(true);
        $exampleFile->method('getVariables')
            ->willReturn(array('VALUE_1' => true));

        $generatedFile = $this->getMockBuilder('Diarmuidie\EnvPopulate\File\Env')
            ->setConstructorArgs(array('file.env'))
            ->getMock();
        $generatedFile->expects($this->once())
            ->method('fileExists')
            ->willReturn(false);
        $generatedFile->method('getVariables')
            ->willReturn(array());
        $generatedFile->expects($this->once())
            ->method('setVariable')
            ->with(
                $this->equalTo('VALUE_1'),
                $this->equalTo(true)
            );
        $generatedFile->expects($this->once())
            ->method('save');

        $this->fileFactory->method('create')
            ->will($this->onConsecutiveCalls($exampleFile, $generatedFile));

        $this->composerIO
            ->method('isInteractive')
            ->willReturn(false);
        $this->composerIO->expects($this->never())
            ->method('write');

        $processor = new Processor($this->composerIO, $this->fileFactory);
        $processor->processFile('example.file', 'generated.file');
    }

    public function testAsksForValueIfValueNeedsToBeUpdated()
    {

        $exampleFile = $this->file;

        $exampleFile->expects($this->once())
            ->method('fileExists')
            ->willReturn(true);
        $exampleFile->method('getVariables')
            ->willReturn(array('VALUE_1' => true));

        $generatedFile = $this->getMockBuilder('Diarmuidie\EnvPopulate\File\Env')
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
        $processor->processFile('example.file', 'generated.file');
    }
}
