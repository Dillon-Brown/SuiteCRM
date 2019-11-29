<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

class BeanFactoryTest extends SuitePHPUnitFrameworkTestCase
{
    public function unRegisterEmptyBeanIDTest()
    {
        $result = (new BeanFactory())::unregisterBean('Accounts', '');
        $this->assertFalse('', $result);
    }

    public function unRegisterUnloadedBeanTest()
    {
        $result = (new BeanFactory())::unregisterBean('Accounts', '1');
        $this->assertFalse('', $result);
    }
}
