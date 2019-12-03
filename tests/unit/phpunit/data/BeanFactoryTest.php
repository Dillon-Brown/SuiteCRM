<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

class BeanFactoryTest extends SuitePHPUnitFrameworkTestCase
{
    public function testUnRegisterEmptyBeanID()
    {
        $beanFactory = new BeanFactory();
        $result = $beanFactory::unregisterBean('Accounts', '');
        $this->assertFalse('', $result);
    }

    public function testUnRegisterUnloadedBean()
    {
        $beanFactory = new BeanFactory();
        $result = $beanFactory::unregisterBean('Accounts', '1');
        $this->assertFalse('', $result);
    }
}
