<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2021 SalesAgility Ltd.
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

use Codeception\Exception\ModuleException;
use Helper\WebDriverHelper;
use Step\Acceptance\AccountsTester;

/**
 * Class ElasticsearchCest
 * @author gyula
 */
class ElasticsearchCest
{
    /**
     * @param AcceptanceTester $I
     * @param WebDriverHelper $helper
     * @throws ModuleException
     */
    public function testSearchSetup(AcceptanceTester $I, WebDriverHelper $helper): void
    {
        $I->loginAsAdmin();

        $I->click('admin');

        // Click on Admin menu:
        // TODO: Page css selector error: I found element #admin_link at 3 times. Html tag should have uniqe ID.
        $I->click('.navbar.navbar-inverse.navbar-fixed-top .container-fluid .desktop-bar #toolbar #globalLinks .dropdown-menu.user-dropdown.user-menu #admin_link');

        $I->click('Search Settings');
        $I->selectOption('#search-engine', 'Elasticsearch Engine');
        $I->click('Save');

        $I->waitForElementVisible('#elastic_search');
        $I->click('#elastic_search');
        $I->checkOption('#es-enabled');
        $I->fillField('#es-host', $helper->getElasticSearchHost());
        $I->fillField('#es-user', 'admin');
        $I->fillField('#es-password', 'admin');

        $I->click('Schedule full indexing');
        $I->wait(1);
        $I->seeInPopup('A full indexing has been scheduled and will start in the next 60 seconds. Search results might be inconsistent until the process is complete.');
        $I->acceptPopup();

        $I->click('Schedule partial indexing');
        $I->wait(1);
        $I->seeInPopup('A partial indexing has been scheduled and will start in the next 60 seconds.');
        $I->acceptPopup();

        $I->click('Test connection');
        $I->wait(1);
        $I->seeInPopup('Connection successful.');
        $I->acceptPopup();

        $I->click('Save');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testSearchNotFound(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();

        // lets try out elasticsearch..
        // TODO [Selenium browser Logs] 12:47:10.930 SEVERE - http://localhost/SuiteCRM/index.php?action=Login&module=Users - [DOM] Found 2 elements with non-unique id #form: (More info: https://goo.gl/9p2vKq)
        $I->fillField('div.desktop-bar ul#toolbar li #searchform .input-group #query_string',
            'I_bet_there_is_nothing_to_contains_this');

        // click on search icon: TODO: search icon ID is not unique:
        $I->click('.desktop-bar #searchform > div > span > button');

        $I->see('SEARCH');
        $I->see('Results');
        $I->see('No results matching your search criteria. Try broadening your search.');
        $I->see('Search performed in');
    }

    /**
     * @param AcceptanceTester $I
     * @param AccountsTester $accounts
     */
    public function testSearchFounds(AcceptanceTester $I, AccountsTester $accounts): void
    {
        $I->loginAsAdmin();

        $I->fillField(
            'div.desktop-bar ul#toolbar li #searchform .input-group #query_string',
            '5D Investments'
        );
        $I->click('.desktop-bar #searchform > div > span > button');

        $I->see('SEARCH');
        $I->see('Results');
        $I->see('Search performed in');
        $I->see('Page 1 of 1');
    }
}
