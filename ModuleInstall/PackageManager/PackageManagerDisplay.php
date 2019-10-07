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
     * A Static method to Build the display for the package manager
     * @param string $form1 the form to display for manual downloading
     * @param string $hidden_fields the hidden fields related to downloading a package
     * @param string $form_action the form_action to be used when downloading from the server
     * @param string $type the type of package to display
     * @param bool $install
     * @return string HTML used to display the form
     */
    public static function buildPackageDisplay(
        $form1,
        $hidden_fields,
        $form_action,
        $install = false,
        $type = 'module'
    ) {
        global $current_language, $app_strings;
        $app_strings = return_application_language($current_language);
        $mod_strings = return_module_language($current_language, 'Administration');

        $ss = new Sugar_Smarty();
        $ss->assign('APP_STRINGS', $app_strings);
        $ss->assign('FORM_1_PLACE_HOLDER', $form1);
        $ss->assign('form_action', $form_action);
        $ss->assign('hidden_fields', $hidden_fields);
        $tree = self::buildTreeView('treeview');
        $tree->tree_style = 'include/ytree/TreeView/css/check/tree.css';
        $ss->assign('TREEHEADER', $tree->generate_header());
        $ss->assign('installation', ($install ? 'true' : 'false'));
        $ss->assign('MOD', $mod_strings);
        $ss->assign('module_load', 'true');
        if (UploadStream::getSuhosinStatus() === false) {
            $ss->assign('ERR_SUHOSIN', true);
        } else {
            $ss->assign('scripts',
                self::getDisplayScript(
                    $install,
                    $type,
                    null,
                    [],
                    true)
            );
        }
        $ss->assign('FORM_2_PLACE_HOLDER', '');
        $ss->assign('MOD', $mod_strings);

        return $ss->fetch('ModuleInstall/PackageManager/tpls/PackageForm.tpl');
    }

    /**
     * A Static method used to build the initial treeview when the page is first displayed
     *
     * @param String div_id - this div in which to display the tree
     * @return Tree - the tree that is built
     */
    public static function buildTreeView($div_id)
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
     * A Static method used to obtain the div for the license
     *
     * @param String license_file - the path to the license file
     * @param String form_action - the form action when accepting the license file
     * @param String next_step - the value for the next step in the installation process
     * @param String zipFile - a string representing the path to the zip file
     * @param String type - module/patch....
     * @param String manifest - the path to the manifest file
     * @param String modify_field - the field to update when the radio button is changed
     * @return String - a form used to display the license
     */
    public function getLicenseDisplay($license_file, $form_action, $next_step, $zipFile, $type, $manifest, $modify_field)
    {
        global $current_language;
        $mod_strings = return_module_language($current_language, "Administration");
        $contents = sugar_file_get_contents($license_file);
        $div_id = urlencode($zipFile);
        $display = "<form name='delete{$zipFile}' action='{$form_action}' method='POST'>";
        $display .= "<input type='hidden' name='current_step' value='{$next_step}'>";
        $display .= "<input type='hidden' name='languagePackAction' value='{$type}'>";
        $display .= "<input type='hidden' name='manifest' value='\".urlencode($manifest).\"'>";
        $display .= "<input type='hidden' name='zipFile' value='\".urlencode($zipFile).\"'>";
        $display .= "<table><tr>";
        $display .= "<td align=\"left\" valign=\"top\" colspan=2>";
        $display .= "<b><font color='red' >{$mod_strings['LBL_MODULE_LICENSE']}</font></b>";
        $display .= "</td>";
        $display .= "<td>";
        $display .= "<span><a class=\"listViewTdToolsS1\" id='href_animate' onClick=\"PackageManager.toggleLowerDiv('span_animate_div_$div_id', 'span_license_div_$div_id', 350, 0);\"><span id='span_animate_div_$div_id'<img src='".SugarThemeRegistry::current()->getImageURL('advanced_search.gif')."' width='8' height='8' alt='Advanced' border='0'>&nbsp;Expand</span></a></span></td>";
        $display .= "</td>";
        $display .= "</tr>";
        $display .= "</table>";
        $display .= "<div id='span_license_div_$div_id' style=\"display: none;\">";
        $display .= "<table>";
        $display .= "<tr>";
        $display .= "<td align=\"left\" valign=\"top\" colspan=2>";
        $display .= "<textarea cols=\"100\" rows=\"8\">{$contents}</textarea>";
        $display .= "</td>";
        $display .= "</tr>";
        $display .= "<tr>";
        $display .= "<td align=\"left\" valign=\"top\" colspan=2>";
        $display .= "<input type='radio' id='radio_license_agreement_accept' name='radio_license_agreement' value='accept' onClick=\"document.getElementById('$modify_field').value = 'yes';\">{$mod_strings['LBL_ACCEPT']}&nbsp;";
        $display .= "<input type='radio' id='radio_license_agreement_reject' name='radio_license_agreement' value='reject' checked onClick=\"document.getElementById('$modify_field').value = 'no';\">{$mod_strings['LBL_DENY']}";
        $display .= "</td>";
        $display .= "</tr>";
        $display .= "</table>";
        $display .= "</div>";
        $display .= "</form>";
        return $display;
    }

    /**
    * A Static method used to generate the javascript for the page
    *
    * @return String - the javascript required for the page
    */
    public static function getDisplayScript($install = false, $type = 'module', $releases = null, $types = array(), $isAlive = true)
    {
        global $sugar_version, $sugar_config;
        global $current_language;

        $mod_strings = return_module_language($current_language, "Administration");
        $ss = new Sugar_Smarty();
        $ss->assign('MOD', $mod_strings);
        if (!$install) {
            $install = 0;
        }
        $ss->assign('INSTALLATION', $install);
        $ss->assign('WAIT_IMAGE', SugarThemeRegistry::current()->getImage("loading", "border='0' align='bottom'", null, null, '.gif', "Loading"));

        $ss->assign('sugar_version', $sugar_version);
        $ss->assign('js_custom_version', $sugar_config['js_custom_version']);
        $ss->assign('IS_ALIVE', $isAlive);
        //if($type == 'patch' && $releases != null){
        if ($type == 'patch') {
            $ss->assign('module_load', 'false');
            $patches = PackageManagerDisplay::createJavascriptPackageArray($releases);
            $ss->assign('PATCHES', $patches);
            $ss->assign('GRID_TYPE', implode(',', $types));
        } else {
            $pm = new PackageManager();
            $releases = $pm->getPackagesInStaging();
            $patches = PackageManagerDisplay::createJavascriptModuleArray($releases);
            $ss->assign('PATCHES', $patches);
            $installeds = $pm->getinstalledPackages();
            $patches = PackageManagerDisplay::createJavascriptModuleArray($installeds, 'mti_installed_data');
            $ss->assign('INSTALLED_MODULES', $patches);
            $ss->assign('UPGARDE_WIZARD_URL', 'index.php?module=UpgradeWizard&action=index');
            $ss->assign('module_load', 'true');
        }
        if (!empty($GLOBALS['ML_STATUS_MESSAGE'])) {
            $ss->assign('ML_STATUS_MESSAGE', $GLOBALS['ML_STATUS_MESSAGE']);
        }

        //Bug 24064. Checking and Defining labels since these might not be cached during Upgrade
        if (!isset($mod_strings['LBL_ML_INSTALL']) || empty($mod_strings['LBL_ML_INSTALL'])) {
            $mod_strings['LBL_ML_INSTALL'] = 'Install';
        }
        if (!isset($mod_strings['LBL_ML_ENABLE_OR_DISABLE']) || empty($mod_strings['LBL_ML_ENABLE_OR_DISABLE'])) {
            $mod_strings['LBL_ML_ENABLE_OR_DISABLE'] = 'Enable/Disable';
        }
        if (!isset($mod_strings['LBL_ML_DELETE'])|| empty($mod_strings['LBL_ML_DELETE'])) {
            $mod_strings['LBL_ML_DELETE'] = 'Delete';
        }
        //Add by jchi 6/23/2008 to fix the bug 21667
        $filegrid_column_ary = array(
            'Name' => $mod_strings['LBL_ML_NAME'],
            'Install' => $mod_strings['LBL_ML_INSTALL'],
            'Delete' => $mod_strings['LBL_ML_DELETE'],
            'Type' => $mod_strings['LBL_ML_TYPE'],
            'Version' => $mod_strings['LBL_ML_VERSION'],
            'Published' => $mod_strings['LBL_ML_PUBLISHED'],
            'Uninstallable' => $mod_strings['LBL_ML_UNINSTALLABLE'],
            'Description' => $mod_strings['LBL_ML_DESCRIPTION']
        );

        $filegridinstalled_column_ary = array(
            'Name' => $mod_strings['LBL_ML_NAME'],
            'Install' => $mod_strings['LBL_ML_INSTALL'],
            'Action' => $mod_strings['LBL_ML_ACTION'],
            'Enable_Or_Disable' => $mod_strings['LBL_ML_ENABLE_OR_DISABLE'],
            'Type' => $mod_strings['LBL_ML_TYPE'],
            'Version' => $mod_strings['LBL_ML_VERSION'],
            'Date_Installed' => $mod_strings['LBL_ML_INSTALLED'],
            'Uninstallable' => $mod_strings['LBL_ML_UNINSTALLABLE'],
            'Description' => $mod_strings['LBL_ML_DESCRIPTION']
        );

        $ss->assign('ML_FILEGRID_COLUMN', $filegrid_column_ary);
        $ss->assign('ML_FILEGRIDINSTALLED_COLUMN', $filegridinstalled_column_ary);
        //end

        $ss->assign('SHOW_IMG', SugarThemeRegistry::current()->getImage('advanced_search', 'border="0"', 8, 8, '.gif', 'Show'));
        $ss->assign('HIDE_IMG', SugarThemeRegistry::current()->getImage('basic_search', 'border="0"', 8, 8, '.gif', 'Hide'));
        $str = $ss->fetch('ModuleInstall/PackageManager/tpls/PackageManagerScripts.tpl');
        return $str;
    }

    public function createJavascriptPackageArray($releases)
    {
        $output = "var mti_data = [";
        $count = count($releases);
        $index = 1;
        if (!empty($releases['packages'])) {
            foreach ($releases['packages'] as $release) {
                $release = PackageManager::fromNameValueList($release);
                $output .= "[";
                $output .= "'".$release['description']."', '".$release['version']."', '".$release['build_number']."', '".$release['id']."'";
                $output .= "]";
                if ($index < $count) {
                    $output .= ",";
                }
                $index++;
            }
        }
        $output .= "]\n;";
        return $output;
    }

    public static function createJavascriptModuleArray($modules, $variable_name = 'mti_data')
    {
        $output = "var ".$variable_name." = [";
        $count = count($modules);
        $index = 1;
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $output .= "[";
                $output .= "'".$module['name']."', '".$module['file_install']."', '".$module['file']."', '";
                if (!empty($module['enabled'])) {
                    $output .= $module['enabled'].'_'.$module['file']."', '";
                }

                $description = js_escape($module['description']);
                $output .= $module['type']."', '".$module['version']."', '".$module['published_date']."', '".$module['uninstallable']."', '".$description."'".(isset($module['upload_file'])?" , '".$module['upload_file']."']":"]");
                if ($index < $count) {
                    $output .= ",";
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
     */
    public function buildLicenseOutput($file)
    {
        global $current_language;

        $mod_strings = return_module_language($current_language, "Administration");
        $contents = '';
        $pm = new PackageManager();
        $contents = $pm->getLicenseFromFile($file);
        $ss = new Sugar_Smarty();
        $ss->assign('MOD', $mod_strings);
        $ss->assign('LICENSE_CONTENTS', $contents);
        $ss->assign('FILE', $file);
        $str = $ss->fetch('ModuleInstall/PackageManagerLicense.tpl');
        $GLOBALS['log']->debug('LICENSE OUTPUT: '.$str);
        return $str;
    }

    public static function getHeader()
    {
        global $current_language;

        $mod_strings = return_module_language($current_language, 'Administration');
        $header_text = '';
        $isAlive = false;
        $show_login = false;
        if (!function_exists('curl_init') && $show_login) {
            $header_text = "<font color='red'><b>".$mod_strings['ERR_ENABLE_CURL']."</b></font>";
            $show_login = false;
        }
        return ['text' => $header_text, 'isAlive' => $isAlive, 'show_login' => $show_login];
    }

    public function buildInstallGrid($view)
    {
        $uh = new UpgradeHistory();
        $installeds = $uh->getAll();
        $upgrades_installed = 0;
        $installed_objects = array();
        foreach ($installeds as $installed) {
            $filename = from_html($installed->filename);
            $date_entered = $installed->date_entered;
            $type = $installed->type;
            $version = $installed->version;
            $upgrades_installed++;
            $link = "";

            switch ($type) {
                case "theme":
                case "langpack":
                case "module":
                case "patch":
                $manifest_file = extractManifest($filename);
                require_once($manifest_file);

                $name = empty($manifest['name']) ? $filename : $manifest['name'];
                $description = empty($manifest['description']) ? $mod_strings['LBL_UW_NONE'] : $manifest['description'];
                if (($upgrades_installed==0 || $uh->UninstallAvailable($installeds, $installed))
                    && is_file($filename) && !empty($manifest['is_uninstallable'])) {
                    $link = urlencode($filename);
                } else {
                    $link = 'false';
                }

                break;
                default:
                    break;
            }

            if ($view == 'default' && $type != 'patch') {
                continue;
            }

            if ($view == 'module'
                && $type != 'module' && $type != 'theme' && $type != 'langpack') {
                continue;
            }

            $target_manifest = remove_file_extension($filename) . "-manifest.php";
            require_once((string)$target_manifest);

            if (isset($manifest['icon']) && $manifest['icon'] != "") {
                $manifest_copy_files_to_dir = isset($manifest['copy_files']['to_dir']) ? clean_path($manifest['copy_files']['to_dir']) : "";
                $manifest_copy_files_from_dir = isset($manifest['copy_files']['from_dir']) ? clean_path($manifest['copy_files']['from_dir']) : "";
                $manifest_icon = clean_path($manifest['icon']);
                $icon = "<img src=\"" . $manifest_copy_files_to_dir . ($manifest_copy_files_from_dir != "" ? substr($manifest_icon, strlen($manifest_copy_files_from_dir)+1) : $manifest_icon) . "\">";
            } else {
                $icon = getImageForType($manifest['type']);
            }
            $installed_objects[] = array('icon' => $icon, 'name' => $name, 'type' => $type, 'version' => $version, 'date_entered' => $date_entered, 'description' => $description, 'file' => $link);
            //print( "<form action=\"" . $form_action . "_prepare\" method=\"post\">\n" );
            //print( "<tr><td>$icon</td><td>$name</td><td>$type</td><td>$version</td><td>$date_entered</td><td>$description</td><td>$link</td></tr>\n" );
            //print( "</form>\n" );
        }
    }
}
