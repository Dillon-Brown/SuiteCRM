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

require_once('include/utils/zip_utils.php');
require_once('ModuleInstall/ModuleInstaller.php');
require_once('include/entryPoint.php');

class PackageManager
{
    /**
     * @var DBManager
     */
    public $db;

    /**
     * @var string
     */
    public $upload_dir;

    /**
     * @var array
     */
    protected $cleanUpDirs;

    /**
     * PackageManager constructor.
     */
    public function __construct()
    {
        $this->db = DBManagerFactory::getInstance();
        $this->upload_dir = empty($GLOBALS['sugar_config']['upload_dir']) ? 'upload' : rtrim($GLOBALS['sugar_config']['upload_dir'],
            '/\\');
    }

    private function addToCleanup($dir)
    {
        if (empty($this->cleanUpDirs)) {
            register_shutdown_function([$this, 'cleanUpTempDir']);
        }
        $this->cleanUpDirs[] = $dir;
    }

    public function cleanUpTempDir()
    {
        foreach ($this->cleanUpDirs as $dir) {
            rmdir_recursive($dir);
        }
    }

    public function extractFile($zip_file, $file_in_zip, $base_tmp_upgrade_dir)
    {
        $my_zip_dir = mk_temp_dir($base_tmp_upgrade_dir);
        $this->addToCleanup($my_zip_dir);
        unzip_file($zip_file, $file_in_zip, $my_zip_dir);

        return ("$my_zip_dir/$file_in_zip");
    }

    public function extractManifest($zip_file, $base_tmp_upgrade_dir)
    {
        $base_upgrade_dir = $this->upload_dir . "/upgrades";
        $base_tmp_upgrade_dir = "$base_upgrade_dir/temp";

        return $this->extractFile($zip_file, "manifest.php", $base_tmp_upgrade_dir);
    }

    public function validate_manifest($manifest)
    {
        global $sugar_version, $mod_strings;

        if (!isset($manifest['type'])) {
            die($mod_strings['ERROR_MANIFEST_TYPE']);
        }
        $type = $manifest['type'];
        $GLOBALS['log']->debug("Getting InstallType");
        if ($this->getInstallType("/$type/") == "") {
            $GLOBALS['log']->debug("Error with InstallType" . $type);
            die($mod_strings['ERROR_PACKAGE_TYPE'] . ": '" . $type . "'.");
        }
        $GLOBALS['log']->debug("Passed with InstallType");
        if (isset($manifest['acceptable_sugar_versions'])) {
            $version_ok = false;
            $matches_empty = true;
            if (isset($manifest['acceptable_sugar_versions']['exact_matches'])) {
                $matches_empty = false;
                foreach ($manifest['acceptable_sugar_versions']['exact_matches'] as $match) {
                    if ($match == $sugar_version) {
                        $version_ok = true;
                    }
                }
            }
            if (!$version_ok && isset($manifest['acceptable_sugar_versions']['regex_matches'])) {
                $matches_empty = false;
                foreach ($manifest['acceptable_sugar_versions']['regex_matches'] as $match) {
                    if (preg_match("/$match/", $sugar_version)) {
                        $version_ok = true;
                    }
                }
            }

            if (!$matches_empty && !$version_ok) {
                die($mod_strings['ERROR_VERSION_INCOMPATIBLE'] . $sugar_version);
            }
        }
    }

    public function getInstallType($type_string)
    {
        // detect file type
        global $subdirs;
        $subdirs = array('full', 'langpack', 'module', 'patch', 'theme', 'temp');


        foreach ($subdirs as $subdir) {
            if (preg_match("#/$subdir/#", $type_string)) {
                return ($subdir);
            }
        }

        // return empty if no match
        return ("");
    }

    public function performSetup($tempFile, $view = 'module', $display_messages = true)
    {
        global $sugar_config, $mod_strings;
        $base_filename = urldecode($tempFile);
        $GLOBALS['log']->debug("BaseFileName: " . $base_filename);
        $base_upgrade_dir = $this->upload_dir . '/upgrades';
        $base_tmp_upgrade_dir = "$base_upgrade_dir/temp";
        $manifest_file = $this->extractManifest($base_filename, $base_tmp_upgrade_dir);
        $GLOBALS['log']->debug("Manifest: " . $manifest_file);
        if ($view == 'module') {
            $license_file = $this->extractFile($base_filename, 'LICENSE.txt', $base_tmp_upgrade_dir);
        }
        if (is_file($manifest_file)) {
            $GLOBALS['log']->debug("VALIDATING MANIFEST" . $manifest_file);
            require_once($manifest_file);
            $this->validate_manifest($manifest);
            $upgrade_zip_type = $manifest['type'];
            $GLOBALS['log']->debug("VALIDATED MANIFEST");
            // exclude the bad permutations
            if ($view == "module") {
                if ($upgrade_zip_type != "module" && $upgrade_zip_type != "theme" && $upgrade_zip_type != "langpack") {
                    $this->unlinkTempFiles();
                    if ($display_messages) {
                        die($mod_strings['ERR_UW_NOT_ACCEPTIBLE_TYPE']);
                    }
                }
            } elseif ($view == "default") {
                if ($upgrade_zip_type != "patch") {
                    $this->unlinkTempFiles();
                    if ($display_messages) {
                        die($mod_strings['ERR_UW_ONLY_PATCHES']);
                    }
                }
            }

            $base_filename = preg_replace("#\\\\#", "/", $base_filename);
            $base_filename = basename($base_filename);
            mkdir_recursive("$base_upgrade_dir/$upgrade_zip_type");
            $target_path = "$base_upgrade_dir/$upgrade_zip_type/$base_filename";
            $target_manifest = remove_file_extension($target_path) . "-manifest.php";

            if (isset($manifest['icon']) && $manifest['icon'] != "") {
                $icon_location = $this->extractFile($tempFile, $manifest['icon'], $base_tmp_upgrade_dir);
                $path_parts = pathinfo($icon_location);
                copy($icon_location, remove_file_extension($target_path) . "-icon." . $path_parts['extension']);
            }

            if (copy($tempFile, $target_path)) {
                copy($manifest_file, $target_manifest);
                if ($display_messages) {
                    $messages = '<script>ajaxStatus.flashStatus("' . $base_filename . $mod_strings['LBL_UW_UPLOAD_SUCCESS'] . ', 5000");</script>';
                }
            } else {
                if ($display_messages) {
                    $messages = '<script>ajaxStatus.flashStatus("' . $mod_strings['ERR_UW_UPLOAD_ERROR'] . ', 5000");</script>';
                }
            }
        }//fi
        else {
            $this->unlinkTempFiles();
            if ($display_messages) {
                die($mod_strings['ERR_UW_NO_MANIFEST']);
            }
        }
        if (isset($messages)) {
            return $messages;
        }
    }

    public function unlinkTempFiles()
    {
        @unlink($_FILES['upgrade_zip']['tmp_name']);
        @unlink("upload://" . $_FILES['upgrade_zip']['name']);
    }

    public function performInstall($file, $silent = true)
    {
        global $mod_strings, $current_language;
        $base_upgrade_dir = $this->upload_dir . '/upgrades';
        $base_tmp_upgrade_dir = "$base_upgrade_dir/temp";
        if (!file_exists($base_tmp_upgrade_dir)) {
            mkdir_recursive($base_tmp_upgrade_dir, true);
        }

        $GLOBALS['log']->debug("INSTALLING: " . $file);
        $mi = new ModuleInstaller();
        $mi->silent = $silent;
        $mod_strings = return_module_language($current_language, "Administration");
        $GLOBALS['log']->debug("ABOUT TO INSTALL: " . $file);
        if (preg_match("#.*\.zip\$#", $file)) {
            $GLOBALS['log']->debug("1: " . $file);
            // handle manifest.php
            $target_manifest = remove_file_extension($file) . '-manifest.php';
            include($target_manifest);
            $GLOBALS['log']->debug("2: " . $file);
            $unzip_dir = mk_temp_dir($base_tmp_upgrade_dir);
            $this->addToCleanup($unzip_dir);
            unzip($file, $unzip_dir);
            $GLOBALS['log']->debug("3: " . $unzip_dir);
            $id_name = $installdefs['id'];
            $version = $manifest['version'];
            $uh = new UpgradeHistory();
            $previous_install = array();
            if (!empty($id_name) & !empty($version)) {
                $previous_install = $uh->determineIfUpgrade($id_name, $version);
            }
            $previous_version = (empty($previous_install['version'])) ? '' : $previous_install['version'];
            $previous_id = (empty($previous_install['id'])) ? '' : $previous_install['id'];

            if (!empty($previous_version)) {
                $mi->install($unzip_dir, true, $previous_version);
            } else {
                $mi->install($unzip_dir);
            }
            $GLOBALS['log']->debug("INSTALLED: " . $file);
            $new_upgrade = new UpgradeHistory();
            $new_upgrade->filename = $file;
            $new_upgrade->md5sum = md5_file($file);
            $new_upgrade->type = $manifest['type'];
            $new_upgrade->version = $manifest['version'];
            $new_upgrade->status = "installed";
            $new_upgrade->name = $manifest['name'];
            $new_upgrade->description = $manifest['description'];
            $new_upgrade->id_name = $id_name;
            $serial_manifest = array();
            $serial_manifest['manifest'] = (isset($manifest) ? $manifest : '');
            $serial_manifest['installdefs'] = (isset($installdefs) ? $installdefs : '');
            $serial_manifest['upgrade_manifest'] = (isset($upgrade_manifest) ? $upgrade_manifest : '');
            $new_upgrade->manifest = base64_encode(serialize($serial_manifest));
            $new_upgrade->save();
        }
    }

    public function performUninstall($name)
    {
        $uh = new UpgradeHistory();
        $uh->name = $name;
        $uh->id_name = $name;
        $found = $uh->checkForExisting($uh);
        if ($found != null) {
            $base_upgrade_dir = $this->upload_dir . '/upgrades';
            $base_tmp_upgrade_dir = "$base_upgrade_dir/temp";
            if (is_file($found->filename)) {
                if (!isset($GLOBALS['mi_remove_tables'])) {
                    $GLOBALS['mi_remove_tables'] = true;
                }
                $unzip_dir = mk_temp_dir($base_tmp_upgrade_dir);
                unzip($found->filename, $unzip_dir);
                $mi = new ModuleInstaller();
                $mi->silent = true;
                $mi->uninstall((string)$unzip_dir);
                $found->delete();
                unlink(remove_file_extension($found->filename) . '-manifest.php');
                unlink($found->filename);
            } else {
                //file(s_ have been deleted or are not found in the directory, allow database delete to happen but no need to change filesystem
                $found->delete();
            }
        }
    }
}
