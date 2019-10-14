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

if (!is_admin($current_user)) {
    ACLController::displayNoAccess(true);
    sugar_cleanup(true);
}

require_once __DIR__ . '/../../include/utils/db_utils.php';
require_once __DIR__ . '/../../include/utils/zip_utils.php';
require_once __DIR__ . '/../../modules/UpgradeWizard/uw_utils.php';
require_once __DIR__ . '/../../modules/Administration/UpgradeHistory.php';
require_once __DIR__ . '/../../modules/Trackers/TrackerManager.php';

$GLOBALS['top_message'] = '';

$trackerManager = TrackerManager::getInstance();
$trackerManager->pause();
$trackerManager->unsetMonitors();

prepSystemForUpgrade();
set_upgrade_vars();
initialize_session_vars();

if (!isset($_SESSION['totalUpgradeTime'])) {
    $_SESSION['totalUpgradeTime'] = 0;
}

if (isset($_REQUEST['delete_package']) && $_REQUEST['delete_package'] === 'true') {
    logThis('running delete old package');
    $error = '';
    if (!isset($_REQUEST['install_file']) || ($_REQUEST['install_file'] === '')) {
        logThis('ERROR: trying to delete non-existent file: ['.$_REQUEST['install_file'].']');
        $error .= $mod_strings['ERR_UW_NO_FILE_UPLOADED'].'<br>';
    }

    // delete file in upgrades/patch
    $delete_me = 'upload://upgrades/patch/'.basename(urldecode($_REQUEST['install_file']));
    if (is_file($delete_me) && !@unlink($delete_me)) {
        logThis('ERROR: could not delete: '.$delete_me);
        $error .= $mod_strings['ERR_UW_FILE_NOT_DELETED'].$delete_me.'<br>';
    }

    // delete back up instance
    $delete_dir = 'upload://upgrades/patch/'.remove_file_extension(urldecode($_REQUEST['install_file'])) . '-restore';
    if (is_dir($delete_dir) && !@rmdir_recursive($delete_dir)) {
        logThis('ERROR: could not delete: '.$delete_dir);
        $error .= $mod_strings['ERR_UW_FILE_NOT_DELETED'].$delete_dir.'<br>';
    }

    if (!empty($error)) {
        $out = "<b><span class='error'>{$error}</span></b><br />";
        if (!empty($GLOBALS['top_message'])) {
            $GLOBALS['top_message'] .= "<br />{$out}";
        } else {
            $GLOBALS['top_message'] = $out;
        }
    }
}

require_once __DIR__ . '/../../modules/UpgradeWizard/start.php';
