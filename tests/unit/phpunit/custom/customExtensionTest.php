<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

class customExtensionTest extends SuitePHPUnitFrameworkTestCase
{
    public function testSearchModulesDisplay()
    {
        $unified_search_modules_display = [];

        require_once __DIR__ . '/../../../../custom/modules/unified_search_modules_display.php';

        $this->assertCount(34, $unified_search_modules_display);
    }

    public function testAOWWorkFlowHook()
    {
        $hook_array = [];

        require_once __DIR__ . '/../../../../custom/Extension/application/Ext/LogicHooks/AOW_WorkFlow_Hook.php';

        $this->assertCount(5, $hook_array['after_save'][0]);
    }
}
