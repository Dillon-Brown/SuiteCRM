<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;
use SuiteCRM\Utility\SuiteLogger;

class SugarLoggerTest extends SuitePHPUnitFrameworkTestCase
{
    private static $logger;
    private static $oldLogLevel;

    public function testGetLogLevel()
    {
        if (self::$logger === null) {
            self::$logger = new SuiteLogger();
        }

        $loggerManager = LoggerManager::getLogger();

        if (self::$logger === null) {
            self::$oldLogLevel = $loggerManager::getLogLevel();
        }
    }
}
