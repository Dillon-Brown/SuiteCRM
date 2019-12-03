<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

class BeanFactoryTest extends SuitePHPUnitFrameworkTestCase
{
    public function testUnRegisterEmptyBeanID()
    {
        $result = (new BeanFactory())::unregisterBean('Accounts', '');
        $this->assertFalse('', $result);
    }

    public function testUnRegisterUnloadedBean()
    {
        $result = (new BeanFactory())::unregisterBean('Accounts', '1');
        $this->assertFalse('', $result);
    }
}
