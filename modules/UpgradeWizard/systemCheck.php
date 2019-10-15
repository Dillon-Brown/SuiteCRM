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

logThis('[At systemCheck.php]');
logThis('Starting file permission check...');
$filesNotWritable = [];

$upgradeDir = scandir(__DIR__ . '/../../modules/UpgradeWizard', SCANDIR_SORT_NONE);

foreach ($upgradeDir as $file) {
    $file = __DIR__ . '/../../modules/UpgradeWizard/' . $file;
    if (!is_writable($file)) {
        $filesNotWritable[] = $file;
    }
}
logThis('Finished file permission check.');

logThis('Starting database permissions check...');
$db = DBManagerFactory::getInstance();
$output = testPermsCreate($db, $output);
$output = testPermsInsert($db, $output);
$output = testPermsUpdate($db, $output);
$output = testPermsSelect($db, $output);
$output = testPermsDelete($db, $output);
$output = testPermsAlterTableAdd($db, $output);
$output = testPermsAlterTableChange($db, $output);
$output = testPermsAlterTableDrop($db, $output);
$output = testPermsDropTable($db, $output);
logThis('Finished database permissions check.');

///////////////////////////////////////////////////////////////////////////////
////	INSTALLER TYPE CHECKS
$result = checkSystemCompliance();
$checks = array(
    'phpVersion'				=> $mod_strings['LBL_UW_COMPLIANCE_PHP_VERSION'],
    'dbVersion'                 => $mod_strings['LBL_UW_COMPLIANCE_DB'],
    'xmlStatus'					=> $mod_strings['LBL_UW_COMPLIANCE_XML'],
    'curlStatus'				=> $mod_strings['LBL_UW_COMPLIANCE_CURL'],
    'imapStatus'				=> $mod_strings['LBL_UW_COMPLIANCE_IMAP'],
    'mbstringStatus'			=> $mod_strings['LBL_UW_COMPLIANCE_MBSTRING'],
    'safeModeStatus'			=> $mod_strings['LBL_UW_COMPLIANCE_SAFEMODE'],
    'callTimeStatus'			=> $mod_strings['LBL_UW_COMPLIANCE_CALLTIME'],
    'memory_msg'				=> $mod_strings['LBL_UW_COMPLIANCE_MEMORY'],
    'stream_msg'                => $mod_strings['LBL_UW_COMPLIANCE_STREAM'],
    'ZipStatus'			        => $mod_strings['LBL_UW_COMPLIANCE_ZIPARCHIVE'],
    'pcreVersion'			    => $mod_strings['LBL_UW_COMPLIANCE_PCRE_VERSION'],
    //commenting mbstring overload.
    //'mbstring.func_overload'	=> $mod_strings['LBL_UW_COMPLIANCE_MBSTRING_FUNC_OVERLOAD'],
);
if ($result['error_found'] == true || !empty($result['warn_found'])) {
    if ($result['error_found']) {
        $stop = true;
    }
    $phpIniLocation = get_cfg_var("cfg_file_path");

    $sysCompliance  = "<a href='javascript:void(0); toggleNwFiles(\"sysComp\");'>{$mod_strings['LBL_UW_SHOW_COMPLIANCE']}</a>";
    $sysCompliance .= "<div id='sysComp' >";
    $sysCompliance .= "<table cellpadding='0' cellspacing='0' border='0'>";
    foreach ($result as $k => $v) {
        if ($k == 'error_found') {
            continue;
        }
        $sysCompliance .= "<tr><td valign='top'>{$checks[$k]}</td>";
        $sysCompliance .= "<td valign='top'>{$v}</td></tr>";
    }
    $sysCompliance .= "<tr><td valign='top'>{$mod_strings['LBL_UW_COMPLIANCE_PHP_INI']}</td>";
    $sysCompliance .= "<td valign='top'><b>{$phpIniLocation}</b></td></tr>";
    $sysCompliance .= "</table></div>";
} else {
    $sysCompliance = "<b>{$mod_strings['LBL_UW_COMPLIANCE_ALL_OK']}</b>";
}

////	END INSTALLER CHECKS
///////////////////////////////////////////////////////////////////////////////

////	stop on all errors
foreach ($errors as $k => $type) {
    if (is_array($type) && count($type) > 0) {
        foreach ($type as $k => $subtype) {
            if ($subtype == true) {
                $stop = true;
            }
        }
    }

    if ($type === true) {
        logThis('Found errors during system check - disabling forward movement.');
        $stop = true;
    }
}
