<?php

namespace Diarmuidie\EnvPopulate\Tests;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\RootPackageInterface;
use Composer\Script\Event;
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
        return [
            [
                [
                    ScriptHandler::EXTRA_KEY => [
                        'example-file' => 'tests/fixtures/example.env',
                        'generated-file' => 'tests/fixtures/output.env'
                    ]
                ]
            ]
        ];
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
        return [
            [
                [
                    ScriptHandler::EXTRA_KEY => [
                        [
                            'example-file' => 'tests/fixtures/example.env',
                            'generated-file' => 'tests/fixtures/output.env'
                        ]
                    ]
                ],
                [
                    ScriptHandler::EXTRA_KEY => [
                        [
                            'example-file' => 'tests/fixtures/example.env',
                            'generated-file' => 'tests/fixtures/output.env'
                        ],
                        [
                            'example-file' => 'tests/fixtures/example.env',
                            'generated-file' => 'tests/fixtures/output.env'
                        ]
                    ]
                ]
            ]
        ];
    }
}
