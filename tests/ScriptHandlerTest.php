<?php

namespace Diarmuidie\EnvPopulate\Tests;

use Diarmuidie\EnvPopulate\ScriptHandler;
use PHPUnit\Framework\TestCase;

class ScriptHandlerTest extends TestCase
{
    private function getClassMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param array $extras
     * @dataProvider singleFileExtrasProvider
     */
    public function testSingleFileConfiguration(array $extras)
    {
        $package = $this->getClassMock('\Composer\Package\RootPackageInterface');
        $package->expects($this->once())
            ->method('getExtra')
            ->willReturn($extras);

        $composer = $this->getClassMock('\Composer\Composer');
        $composer->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);

        $event = $this->getClassMock('\Composer\Script\Event');
        $event->expects($this->once())
            ->method('getComposer')
            ->willReturn($composer);
        $event->expects($this->once())
            ->method('getIo')
            ->willReturn($this->getClassMock('\Composer\IO\IOInterface'));

        ScriptHandler::populateEnv($event);
    }

    public function singleFileExtrasProvider()
    {
        return array(
            array(
                array(
                    ScriptHandler::EXTRA_KEY => array(
                        'example-file' => 'tests/fixtures/example.env',
                        'generated-file' => 'tests/fixtures/output.env'
                    )
                )
            )
        );
    }

    /**
     * @param array $extras
     * @dataProvider multipleFilesExtrasProvider
     */
    public function testMultipleFilesConfiguration(array $extras)
    {
        $package = $this->getClassMock('\Composer\Package\RootPackageInterface');
        $package->expects($this->once())
            ->method('getExtra')
            ->willReturn($extras);

        $composer = $this->getClassMock('\Composer\Composer');
        $composer->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);

        $event = $this->getClassMock('\Composer\Script\Event');
        $event->expects($this->once())
            ->method('getComposer')
            ->willReturn($composer);
        $event->expects($this->once())
            ->method('getIo')
            ->willReturn($this->getClassMock('\Composer\IO\IOInterface'));

        ScriptHandler::populateEnv($event);
    }

    public function multipleFilesExtrasProvider()
    {
        return array(
            array(
                array(
                    ScriptHandler::EXTRA_KEY => array(
                        'files' => array(
                            array(
                                'example-file' => 'tests/fixtures/example.env',
                                'generated-file' => 'tests/fixtures/output.env'
                            )
                        )
                    )
                ),
                array(
                    ScriptHandler::EXTRA_KEY => array(
                        'files' => array(
                            array(
                                'example-file' => 'tests/fixtures/example.env',
                                'generated-file' => 'tests/fixtures/output.env'
                            ),
                            array(
                                'example-file' => 'tests/fixtures/example.env',
                                'generated-file' => 'tests/fixtures/output.env'
                            )
                        )
                    )
                )
            )
        );
    }

    public function testNonInteractivity()
    {
        $extras = array(
            ScriptHandler::EXTRA_KEY => array(
                'run-non-interactively' => false
            )
        );

        $package = $this->getClassMock('\Composer\Package\RootPackageInterface');
        $package->expects($this->once())
            ->method('getExtra')
            ->willReturn($extras);

        $composer = $this->getClassMock('\Composer\Composer');
        $composer->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);

        $event = $this->getClassMock('\Composer\Script\Event');
        $event->expects($this->once())
            ->method('getComposer')
            ->willReturn($composer);

        $io = $this->getClassMock('\Composer\IO\IOInterface');
        $io->expects($this->once())
            ->method('isInteractive')
            ->willReturn(false);

        $event->expects($this->once())
            ->method('getIo')
            ->willReturn($io);

        ScriptHandler::populateEnv($event);
    }
}
