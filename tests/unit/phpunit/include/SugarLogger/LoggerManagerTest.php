<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;
use SuiteCRM\Utility\SuiteLogger;

/**
 * Class LoggerManagerTest
 */
class LoggerManagerTest extends SuitePHPUnitFrameworkTestCase
{
    private static $logger;

    public function setUp()
    {
        parent::setUp();

    }

    public function testGetLoggerLevels()
    {
        $loggerManager = LoggerManager::getLogger();
        $loggerManager::setLevelMapping('test', 125);

        $test = $loggerManager::getLoggerLevels();
        $this->assertArrayHasKey('test', $test);
    }

    public function testGetLogLevel()
    {
        $loggerManager = LoggerManager::getLogger();
        $logLevel = $loggerManager::getLogLevel();
        if (self::$logger === null) {
            self::$logger = new SuiteLogger();
        }
        var_dump($logLevel);
    }
}
