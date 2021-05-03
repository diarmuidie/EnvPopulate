<?php

namespace Diarmuidie\EnvPopulate\Tests;

use PHPUnit\Framework\TestCase;
use Diarmuidie\EnvPopulate\File\Env;

class EnvTest extends TestCase
{
    protected $env;

    protected function setUp(): void
    {
        chdir(__DIR__);
        $this->env = new Env('fixtures/example.env');
    }

    public function testGettingFilename()
    {
        $this->assertEquals("fixtures/example.env", $this->env->getFilename());
    }

    public function testLoadingEnvFile()
    {
        $this->env->load();

        $expected = array(
            'VALUE_1' => true,
            'VALUE_2' => 'string'
        );

        $this->assertEquals($expected, $this->env->getVariables());
    }

    public function testSettingVariables()
    {
        $this->env->setVariable('NAME', 'value');

        $expected = array(
            'NAME' => 'value'
        );

        $this->assertEquals($expected, $this->env->getVariables());
    }

    public function testSavingFile()
    {
        $env = new Env('fixtures/output.env');
        $env->setVariable('STRING', 'string');
        $env->save();

        $this->assertFileEquals('fixtures/output.env', 'fixtures/expected-output.env');
    }

    /**
     * @dataProvider variableProvider
     */
    public function testVariableFormatting($variable, $expected)
    {
        $actual = $this->env->formatValueForEnv($variable);

        $this->assertEquals($expected, $actual);
    }

    public function variableProvider()
    {
        return array(
            array(true, 'true'),
            array(false, 'false'),
            array('true', 'true'),
            array('false', 'false'),
            array(123, 123),
            array("string", '"string"'),
            array('string', '"string"'),
            array(1.234, '"1.234"'),
            array('', ''),
            array(null, '')
        );
    }
}
