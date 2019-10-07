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

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once __DIR__ . '/../../ModuleInstall/PackageManager/PackageManager.php';
require_once __DIR__ . '/../../include/ytree/Tree.php';
require_once __DIR__ . '/../../include/ytree/Node.php';
require_once __DIR__ . '/../../ModuleInstall/PackageManager/ListViewPackages.php';

class PackageManagerDisplay
{
    /**
     * A method to Build the display for the package manager
     * @param string $form1 the form to display for manual downloading
     * @param string $hidden_fields the hidden fields related to downloading a package
     * @param string $form_action the form_action to be used when downloading from the server
     * @param string $type the type of package to display
     * @param bool $install
     * @return string HTML used to display the form
     */
    public function buildPackageDisplay(
        $form1,
        $hidden_fields,
        $form_action,
        $install = false,
        $type = 'module'
    ) {
        global $current_language, $app_strings;
        $mod_strings = return_module_language($current_language, 'Administration');

        $ss = new Sugar_Smarty();
        $ss->assign('APP_STRINGS', $app_strings);
        $ss->assign('FORM_1_PLACE_HOLDER', $form1);
        $ss->assign('form_action', $form_action);
        $ss->assign('hidden_fields', $hidden_fields);
        $tree = $this->buildTreeView('treeview');
        $tree->tree_style = 'include/ytree/TreeView/css/check/tree.css';
        $ss->assign('TREEHEADER', $tree->generate_header());
        $ss->assign('installation', ($install ? 'true' : 'false'));
        $ss->assign('MOD', $mod_strings);
        $ss->assign('module_load', 'true');
        if (UploadStream::getSuhosinStatus() === false) {
            $ss->assign('ERR_SUHOSIN', true);
        } else {
            $ss->assign('scripts',
                $this->getDisplayScript(
                    $install,
                    $type,
                    null,
                    [],
                    true)
            );
        }
        $ss->assign('FORM_2_PLACE_HOLDER', '');

        return $ss->fetch('ModuleInstall/PackageManager/tpls/PackageForm.tpl');
    }

    /**
     * A method used to build the initial treeview when the page is first displayed
     *
     * @param String div_id - this div in which to display the tree
     * @return Tree - the tree that is built
     */
    public function buildTreeView($div_id)
    {
        $tree = new Tree($div_id);
        foreach ([] as $arr_node) {
            $node = new Node($arr_node['id'], $arr_node['label']);
            $node->dynamicloadfunction = 'PackageManager.loadDataForNodeForPackage';
            $node->expanded = false;
            $node->dynamic_load = true;
            $node->set_property('href', "javascript:PackageManager.catClick('treeview');");
            $tree->add_node($node);
            $node->set_property('description', $arr_node['description']);
        }

        return $tree;
    }

    /**
     * A method used to obtain the div for the license
     *
     * @param string $license_file - the path to the license file
     * @param string $form_action - the form action when accepting the license file
     * @param string $next_step - the value for the next step in the installation process
     * @param string $zipFile - a string representing the path to the zip file
     * @param string $type - module/patch....
     * @param string $manifest - the path to the manifest file
     * @param string $modify_field - the field to update when the radio button is changed
     * @return string - a form used to display the license
     */
    public function getLicenseDisplay(
        $license_file,
        $form_action,
        $next_step,
        $zipFile,
        $type,
        $manifest,
        $modify_field
    ) {
        global $current_language;
        $mod_strings = return_module_language($current_language, 'Administration');
        $contents = sugar_file_get_contents($license_file);
        $div_id = urlencode($zipFile);

        $ss = new Sugar_Smarty();
        $ss->assign('MOD', $mod_strings);
        $ss->assign('LICENSE_CONTENT', $contents);
        $ss->assign('DIV_ID', $div_id);
        $ss->assign('ZIP_FILE', $zipFile);
        $ss->assign('FORM_ACTION', $form_action);
        $ss->assign('NEXT_STEP', $next_step);
        $ss->assign('TYPE', $type);
        $ss->assign('MANIFEST', $manifest);
        $ss->assign('MODIFY_FIELD', $modify_field);

        return $ss->fetch('ModuleInstall/PackageManager/tpls/PackageLicense.tpl');
    }

    /**
     * A method used to generate the javascript for the page
     *
     * @param bool|int $install
     * @param string $type
     * @param null $releases
     * @param array $types
     * @param bool $isAlive
     * @return string - the javascript required for the page
     */
    public function getDisplayScript($install = false, $type = 'module', $releases = null, $types = [], $isAlive = true)
    {
        global $sugar_version, $sugar_config, $current_language;

        $mod_strings = return_module_language($current_language, 'Administration');
        $ss = new Sugar_Smarty();
        $ss->assign('MOD', $mod_strings);
        if (!$install) {
            $install = 0;
        }
        $ss->assign('INSTALLATION', $install);
        $ss->assign('WAIT_IMAGE',
            SugarThemeRegistry::current()->getImage('loading', "border='0' align='bottom'", null, null, '.gif',
                'Loading'));

        $ss->assign('sugar_version', $sugar_version);
        $ss->assign('js_custom_version', $sugar_config['js_custom_version']);
        $ss->assign('IS_ALIVE', $isAlive);
        if ($type === 'patch') {
            $ss->assign('module_load', 'false');
            $patches = $this->createJavascriptPackageArray($releases);
            $ss->assign('PATCHES', $patches);
            $ss->assign('GRID_TYPE', implode(',', $types));
        } else {
            $pm = new PackageManager();
            $releases = $pm->getPackagesInStaging();
            $patches = $this->createJavascriptModuleArray($releases);
            $ss->assign('PATCHES', $patches);
            $installed = $pm->getinstalledPackages();
            $patches = $this->createJavascriptModuleArray($installed, 'mti_installed_data');
            $ss->assign('INSTALLED_MODULES', $patches);
            $ss->assign('UPGARDE_WIZARD_URL', 'index.php?module=UpgradeWizard&action=index');
            $ss->assign('module_load', 'true');
        }
        if (!empty($GLOBALS['ML_STATUS_MESSAGE'])) {
            $ss->assign('ML_STATUS_MESSAGE', $GLOBALS['ML_STATUS_MESSAGE']);
        }
        if (empty($mod_strings['LBL_ML_INSTALL'])) {
            $mod_strings['LBL_ML_INSTALL'] = 'Install';
        }
        if (empty($mod_strings['LBL_ML_ENABLE_OR_DISABLE'])) {
            $mod_strings['LBL_ML_ENABLE_OR_DISABLE'] = 'Enable/Disable';
        }
        if (empty($mod_strings['LBL_ML_DELETE'])) {
            $mod_strings['LBL_ML_DELETE'] = 'Delete';
        }
        $fileGridColumnArray = [
            'Name' => $mod_strings['LBL_ML_NAME'],
            'Install' => $mod_strings['LBL_ML_INSTALL'],
            'Delete' => $mod_strings['LBL_ML_DELETE'],
            'Type' => $mod_strings['LBL_ML_TYPE'],
            'Version' => $mod_strings['LBL_ML_VERSION'],
            'Published' => $mod_strings['LBL_ML_PUBLISHED'],
            'Uninstallable' => $mod_strings['LBL_ML_UNINSTALLABLE'],
            'Description' => $mod_strings['LBL_ML_DESCRIPTION']
        ];

        $fileGridInstalledColumnArray = [
            'Name' => $mod_strings['LBL_ML_NAME'],
            'Install' => $mod_strings['LBL_ML_INSTALL'],
            'Action' => $mod_strings['LBL_ML_ACTION'],
            'Enable_Or_Disable' => $mod_strings['LBL_ML_ENABLE_OR_DISABLE'],
            'Type' => $mod_strings['LBL_ML_TYPE'],
            'Version' => $mod_strings['LBL_ML_VERSION'],
            'Date_Installed' => $mod_strings['LBL_ML_INSTALLED'],
            'Uninstallable' => $mod_strings['LBL_ML_UNINSTALLABLE'],
            'Description' => $mod_strings['LBL_ML_DESCRIPTION']
        ];

        $ss->assign('ML_FILEGRID_COLUMN', $fileGridColumnArray);
        $ss->assign('ML_FILEGRIDINSTALLED_COLUMN', $fileGridInstalledColumnArray);
        $ss->assign('SHOW_IMG',
            SugarThemeRegistry::current()->getImage('advanced_search', 'border="0"', 8, 8, '.gif', 'Show'));
        $ss->assign('HIDE_IMG',
            SugarThemeRegistry::current()->getImage('basic_search', 'border="0"', 8, 8, '.gif', 'Hide'));

        return $ss->fetch('ModuleInstall/PackageManager/tpls/PackageManagerScripts.tpl');
    }

    public function createJavascriptPackageArray($releases)
    {
        $output = 'var mti_data = [';
        $count = count($releases);
        $index = 1;
        if (!empty($releases['packages'])) {
            foreach ($releases['packages'] as $release) {
                $release = (new PackageManager)->fromNameValueList($release);
                $output .= '[';
                $output .= "'" . $release['description'] . "', '" . $release['version'] . "', '" . $release['build_number'] . "', '" . $release['id'] . "'";
                $output .= ']';
                if ($index < $count) {
                    $output .= ',';
                }
                $index++;
            }
        }
        $output .= "]\n;";

        return $output;
    }

    public function createJavascriptModuleArray($modules, $variable_name = 'mti_data')
    {
        $output = 'var ' . $variable_name . ' = [';
        $count = count($modules);
        $index = 1;
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $output .= '[';
                $output .= "'" . $module['name'] . "', '" . $module['file_install'] . "', '" . $module['file'] . "', '";
                if (!empty($module['enabled'])) {
                    $output .= $module['enabled'] . '_' . $module['file'] . "', '";
                }

                $description = js_escape($module['description']);
                $output .= $module['type'] . "', '" . $module['version'] . "', '" . $module['published_date'] . "', '" . $module['uninstallable'] . "', '" . $description . "'" . (isset($module['upload_file']) ? " , '" . $module['upload_file'] . "']" : ']');
                if ($index < $count) {
                    $output .= ',';
                }
                $index++;
            }
        }
        $output .= "]\n;";

        return $output;
    }

    /**
     *  This method is meant to be used to display the license agreement inline on the page
     *  if the system would like to perform the installation on the same page via an Ajax call
     * @param $file
     * @return string
     */
    public function buildLicenseOutput($file)
    {
        global $current_language;
        $mod_strings = return_module_language($current_language, 'Administration');
        $contents = (new PackageManager())->getLicenseFromFile($file);

        $ss = new Sugar_Smarty();
        $ss->assign('MOD', $mod_strings);
        $ss->assign('LICENSE_CONTENTS', $contents);
        $ss->assign('FILE', $file);
        $str = $ss->fetch('ModuleInstall/PackageManagerLicense.tpl');
        LoggerManager::getLogger()->debug('LICENSE OUTPUT: ' . $str);

        return $str;
    }
}
