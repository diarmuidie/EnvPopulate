<?php

namespace Diarmuidie\EnvPopulate\Tests;

use PHPUnit\Framework\TestCase;
use Diarmuidie\EnvPopulate\File\Env;

class EnvTest extends TestCase
{
    protected $env;

    public function setUp()
    {
        chdir(__DIR__);
        $this->env = new Env('fixtures/example.env');
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
        return [
            [true, 'true'],
            [false, 'false'],
            ['true', 'true'],
            ['false', 'false'],
            [123, 123],
            ["string", '"string"'],
            ['string', '"string"'],
            [1.234, '"1.234"']
        ];
    }

}