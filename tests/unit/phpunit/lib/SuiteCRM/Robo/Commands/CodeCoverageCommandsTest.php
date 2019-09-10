<?php

use \SuiteCRM\Robo\Plugin\Commands\CodeCoverageCommands;

class CodeCoverageCommandsTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var \SuiteCRM\Robo\Plugin\Commands\CodeCoverageCommands **/
    protected static $testClass;

    public function setUp()
    {
        parent::setUp();

        if (self::$testClass === null) {
            self::$testClass = new CodeCoverageCommands();
        }
    }

    public function testIsEnvironmentTravisCI()
    {
        $reflection = new ReflectionClass(CodeCoverageCommands::class);
        $method = $reflection->getMethod('isEnvironmentTravisCI');
        $method->setAccessible(true);

        $actual = $method->invoke(
            self::$testClass
        );

        $returnType = is_string($actual) || is_array($actual) || is_bool($actual);
        $this->assertTrue($returnType);
    }

    public function testGetCommitRangeForTravisCi()
    {
        $reflection = new ReflectionClass(CodeCoverageCommands::class);
        $method = $reflection->getMethod('getCommitRangeForTravisCi');
        $method->setAccessible(true);

        $actual = $method->invoke(
            self::$testClass
        );

        $returnType = is_string($actual) || is_array($actual) || is_bool($actual);
        $this->assertTrue($returnType);
    }

    public function testDisableStateChecker()
    {
        // backup configure override
        $configOverrideData = '';
        $configOverridePath = 'config_override.php';
        if (file_exists($configOverridePath)) {
            $configOverrideData = \file_get_contents($configOverridePath);
        }

        // Run tests
        $reflection = new ReflectionClass(CodeCoverageCommands::class);
        $method = $reflection->getMethod('disableStateChecker');
        $method->setAccessible(true);

        $actual = $method->invoke(
            self::$testClass
        );

        $this->assertTrue($actual);

        // restore config override
        if (!empty($configOverrideData)) {
            \file_put_contents($configOverridePath, $configOverrideData);
        }
    }

    public function testGetPHPUnitCodeCoverageCommand()
    {
        $commandExpected = './vendor/bin/phpunit --configuration ./tests/phpunit.xml.dist --coverage-php ./tests/_output/unitCoverage.serialized ./tests/unit/phpunit';
        // Run tests
        $method = (new ReflectionClass(CodeCoverageCommands::class))->getMethod('getPHPUnitCodeCoverageCommand');
        $method->setAccessible(true);

        $actual = $method->invoke(
            self::$testClass
        );

        $this->assertEquals($commandExpected, $actual);
    }

    public function testGetCodeceptionCodeCoverageCommand()
    {
        $commandExpected = './vendor/bin/codecept run api --coverage';
        // Run tests
        $method = (new ReflectionClass(CodeCoverageCommands::class))->getMethod('getCodeceptionCodeCoverageCommand');
        $method->setAccessible(true);

        $actual = $method->invoke(
            self::$testClass
        );

        $this->assertEquals($commandExpected, $actual);
    }
}
