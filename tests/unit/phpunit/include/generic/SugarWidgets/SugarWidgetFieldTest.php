<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2019 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

require_once __DIR__ . '/../../../../../../include/generic/SugarWidgets/SugarWidgetField.php';
require_once __DIR__ . '/../../../../../../include/generic/LayoutManager.php';

class SugarWidgetFieldTest extends SuitePHPUnitFrameworkTestCase
{
    public function testDisplay()
    {
        $layoutManager = new LayoutManager();
        $_REQUEST['module'] = 'Accounts';
        $layoutManager->setAttribute('context', 'HeaderCell');
        $actual = (new SugarWidgetField($layoutManager))->display([
            'label' => 'testLabelGet'
        ]);

        $this->assertEquals('testLabelGet', $actual);
    }

    public function testDisplayNotFound()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('context', 'invalid');
        $actual = (new SugarWidgetField($layoutManager))->display([
            'label' => 'testLabelGet'
        ]);

        $this->assertEquals('display not found:displayinvalid', $actual);
    }

    public function testGetColumnAlias()
    {
        $layoutManager = new LayoutManager();
        $actual = (new SugarWidgetField($layoutManager))->_get_column_alias([
            'name' => 'testName',
            'table_alias' => 'testAlias'
        ]);

        $this->assertEquals('testAlias_testName', $actual);
    }

    public function testGetColumnAliasCount()
    {
        $layoutManager = new LayoutManager();
        $actual = (new SugarWidgetField($layoutManager))->_get_column_alias([
            'name' => 'count',
        ]);

        $this->assertEquals('count', $actual);
    }

    public function testGetColumnAliasCharLimit()
    {
        $layoutManager = new LayoutManager();
        $actual = (new SugarWidgetField($layoutManager))->_get_column_alias([
            'name' => 'testName',
            'table_alias' => 'testAliasNameExceedsMaxCharLimitOf28'
        ]);

        $this->assertEquals('TESTALIASNAMEEXCEEDSMAFBC769', $actual);
    }

    public function testDisplayHeaderCellPlain()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('module_name', 'Accounts');
        $layoutManager->setAttribute('context', 'HeaderCellPlain');

        $actual = (new SugarWidgetField($layoutManager))->display([
            'vname' => 'LNK_NEW_ACCOUNT'
        ]);

        $this->assertEquals('Create Account', $actual);
    }

    public function testDisplayHeaderCellPlainEmpty()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('context', 'HeaderCellPlain');

        $actual = (new SugarWidgetField($layoutManager))->display([]);

        $this->assertEquals('', $actual);
    }

    public function testDisplayHeaderCell()
    {
        $layoutManager = new LayoutManager();
        $_REQUEST['module'] = 'Accounts';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $layoutManager->setAttribute('context', 'HeaderCell');
        $actual = (new SugarWidgetField($layoutManager))->display([
            'name' => 'testHeaderCell',
            'subpanel_module' => 'Contacts',
            'sort_by' => 'last_name',
            'sort' => '_down'
        ]);

        $this->assertContains('listViewThLinkS1', $actual);
        $this->assertContains('&module=Accounts&_Contacts_CELL', $actual);
        $this->assertContains('&subpanel=Contacts&_Contacts_CELL_ORDER_BY=last_name', $actual);
        $this->assertContains('suitepicon-action-sorting-ascending', $actual);
    }

    public function testDisplayList()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('context', 'List');
        $actual = (new SugarWidgetField($layoutManager))->display([
            'widget_type' => 'checkbox',
        ]);

        $this->assertContains("name='checkbox_display'", $actual);
        $this->assertContains("disabled='true'", $actual);
    }

    public function testDisplayListPlain()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('context', 'ListPlain');
        $actual = (new SugarWidgetField($layoutManager))->display([
            'widget_type' => 'checkbox',
            'varname' => 'status',
            'fields' => [
                'STATUS' => 1
            ]
        ]);

        $this->assertContains("name='checkbox_display'", $actual);
        $this->assertContains("disabled='true' checked>", $actual);
    }

    public function testDisplayListPlainEmptyWidget()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('context', 'ListPlain');
        $actual = (new SugarWidgetField($layoutManager))->display([
            'widget_type' => '',
            'varname' => 'status',
            'fields' => [
                'STATUS' => 1
            ]
        ]);

        $this->assertEquals(1, $actual);
    }

    public function testGetVardef()
    {
        $layoutManager = new LayoutManager();
        $layoutManager->setAttribute('context', 'Report');
        $temp = (object)[
            'all_fields' => [
                'test_key' => 'test_value'
            ],
        ];
        $layoutManager->setAttributePtr('reporter', $temp);

        $actual = (new SugarWidgetField($layoutManager))->getVardef([
            'column_key' => 'test_key'
        ]);

        $this->assertEquals('test_value', $actual);
    }

    public function testGetVardefEmpty()
    {
        $layoutManager = new LayoutManager();
        $actual = (new SugarWidgetField($layoutManager))->getVardef([]);

        $this->assertEquals([], $actual);
    }
}
