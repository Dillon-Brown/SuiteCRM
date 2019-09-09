<?php

use SuiteCRM\StateCheckerPHPUnitTestCaseAbstract;

class indexTest extends StateCheckerPHPUnitTestCaseAbstract
{
    public function testV8ApiIndex()
    {
        $index = require __DIR__ . '/../../../../Api/index.php';

        echo $test;
    }
}
