<?php
/**
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

namespace SuiteCRM\Modules\Administration\Search;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

use Sugar_Smarty;
use SuiteCRM\Search\SearchWrapper;
use LoggerManager;
use SuiteCRM\Modules\Administration\Search\MVC\View as AbstractView;
use UnifiedSearchAdvanced;

/**
 * Class View renders the Search settings.
 */
class View extends AbstractView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . '/view.tpl');
    }

    public function preDisplay()
    {
        parent::preDisplay();

        $this->smarty->assign('selectedController', SearchWrapper::getController());
        $this->smarty->assign('selectedEngine', SearchWrapper::getDefaultEngine());
        $this->smarty->assign('engines', [
            translate('LBL_LEGACY_SEARCH_ENGINES') => [
                'BasicSearchEngine' => translate('LBL_BASIC_SEARCH_ENGINE'),
                'BasicAndAodEngine' => translate('LBL_BASIC_AND_AOD_ENGINE'),
            ],
            translate('LBL_SEARCH_WRAPPER_ENGINES') => $this->getEngines(),
        ]);
    }

    /**
     * @see SugarView::display()
     */
    public function display(): void
    {
        require_once __DIR__ . '/../../../modules/Home/UnifiedSearchAdvanced.php';
        $usa = new UnifiedSearchAdvanced();
        global $mod_strings, $app_strings;

        $sugar_smarty = new Sugar_Smarty();
        $sugar_smarty->assign('APP', $app_strings);
        $sugar_smarty->assign('MOD', $mod_strings);

        $modules = $usa->retrieveEnabledAndDisabledModules();

        $sugar_smarty->assign('enabled_modules', json_encode($modules['enabled'], JSON_THROW_ON_ERROR));
        $sugar_smarty->assign('disabled_modules', json_encode($modules['disabled'], JSON_THROW_ON_ERROR));
        $tpl = 'modules/Administration/Search/GlobalSearchSettings.tpl';
        if (file_exists('custom/' . $tpl)) {
            $tpl = 'custom/' . $tpl;
        }

        echo $sugar_smarty->fetch($tpl);
        $this->smarty->display($this->templateFile);
    }

    public function getButtons()
    {
        global $mod_strings;
        global $app_strings;

        return <<<EOQ
    <input title="{$app_strings['LBL_SAVE_BUTTON_TITLE']}"
        accessKey="{$app_strings['LBL_SAVE_BUTTON_KEY']}"
        class="button primary"
        type="submit"
        name="save"
        onclick="SUGAR.saveGlobalSearchSettings();return check_form('ConfigureSettings');"
        value="{$app_strings['LBL_SAVE_BUTTON_LABEL']}" >&nbsp;
    <input title="{$mod_strings['LBL_CANCEL_BUTTON_TITLE']}" 
        onclick="document.location.href='index.php?module=Administration&action=index'"
        class="button"
        type="button"
        name="cancel"
        value="{$app_strings['LBL_CANCEL_BUTTON_LABEL']}" >
EOQ;
    }
}
