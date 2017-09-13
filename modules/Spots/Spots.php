<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
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

class Spots extends Basic
{
    public $new_schema = true;
    public $module_dir = 'Spots';
    public $object_name = 'Spots';
    public $table_name = 'spots';
    public $importable = false;
    // to ensure that modules created and deployed under CE will continue to function
    // under team security if the instance is upgraded to PRO
    public $disable_row_level_security = true;

    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $SecurityGroups;
    public $config;
    public $type;

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL': return true;
        }

        return false;
    }

    public function fill_in_additional_detail_fields()
    {
        $this->config = htmlspecialchars(json_encode($this->replaceKeyValueWithLabel
        (htmlspecialchars_decode($this->config))));
        parent::fill_in_additional_detail_fields();
    }

    /**
     *This replaces the label values in the config with the corresponding keys (to make language agnostic).
     */
    public function save($check_notify = false)
    {
        $type = $_REQUEST['type'];
        $name = $_REQUEST['name'];
        $config = htmlspecialchars_decode($_REQUEST['config']);
        $jsonConfig = json_decode($config, true);

        $colsLabels = array();
        $rowsLabels = array();
        if (isset($jsonConfig['cols']) && count($jsonConfig['cols']) > 0) {
            $colsLabels = $this->getKeysForLabels($type, $jsonConfig['cols']);
            $jsonConfig['cols'] = $colsLabels;
        }
        if (isset($jsonConfig['rows']) && count($jsonConfig['rows']) > 0) {
            $rowsLabels = $this->getKeysForLabels($type, $jsonConfig['rows']);
            $jsonConfig['rows'] = $rowsLabels;
        }

        //Set the key value for the inclusions / exclusions
        if (isset($jsonConfig['exclusions']) && count($jsonConfig['exclusions']) > 0) {
            foreach ($jsonConfig['exclusions'] as $key => $value) {
                $newKey = $this->getKeyForLabel($type, $key);

                $jsonConfig['exclusions'][reset($newKey)] = $jsonConfig['exclusions'][$key];
                unset($jsonConfig['exclusions'][$key]);
                //Check that this is an array with 1 element
                if (count($newKey) !== 1) {
                    $this->logSpotsErrorWithKeyMatching($type);
                }
            }
        }

        if (isset($jsonConfig['inclusions']) && count($jsonConfig['inclusions']) > 0) {
            foreach ($jsonConfig['inclusions'] as $key => $value) {
                $newKey = $this->getKeyForLabel($type, $key);

                $jsonConfig['inclusions'][reset($newKey)] = $jsonConfig['inclusions'][$key];
                unset($jsonConfig['inclusions'][$key]);
                //Check that this is an array with 1 element
                if (count($newKey) !== 1) {
                    $this->logSpotsErrorWithKeyMatching($type);
                }
            }
        }

        if (isset($jsonConfig['inclusionsInfo']) && count($jsonConfig['inclusionsInfo']) > 0) {
            foreach ($jsonConfig['inclusionsInfo'] as $key => $value) {
                $newKey = $this->getKeyForLabel($type, $key);

                $jsonConfig['inclusionsInfo'][reset($newKey)] = $jsonConfig['inclusionsInfo'][$key];
                unset($jsonConfig['inclusionsInfo'][$key]);
                //Check that this is an array with 1 element
                if (count($newKey) !== 1) {
                    $this->logSpotsErrorWithKeyMatching($type);
                }
            }
        }
        $this->config = json_encode($jsonConfig);

        return parent::save($check_notify);
    }

    /**
     *This parses the spots config and replaces the key values with the appropriate, language-specific labels.
     *
     * @param string $config the configuration file for the spot
     *
     * @return string is the config file with the label values in place of the key names
     */
     public function replaceKeyValueWithLabel($config)
    {
        //Strings are loaded this way as the dashlet mod_strings was set to Home
       $spotStrings = return_module_language($GLOBALS['current_language'], 'Spots');

        $jsonConfig = json_decode($config, true);
        if (isset($jsonConfig['cols']) && count($jsonConfig['cols']) > 0) {
            foreach ($jsonConfig['cols'] as $k => $v) {
                $jsonConfig['cols'][$k] = $spotStrings[$v];
            }
        }

        if (isset($jsonConfig['rows']) && count($jsonConfig['rows']) > 0) {
            foreach ($jsonConfig['rows'] as $k => $v) {
                $jsonConfig['rows'][$k] = $spotStrings[$v];
            }
        }

        if (isset($jsonConfig['exclusions']) && count($jsonConfig['exclusions']) > 0) {
            foreach ($jsonConfig['exclusions'] as $key => $value) {
                $newKey = $spotStrings[$key];

                $jsonConfig['exclusions'][$newKey] = $jsonConfig['exclusions'][$key];
                unset($jsonConfig['exclusions'][$key]);
            }
        }
        elseif(isset($jsonConfig['exclusions']) && count($jsonConfig['exclusions']) == 0)
        {
            $jsonConfig['exclusions'] = new stdClass();
        }

        if (isset($jsonConfig['inclusions']) && count($jsonConfig['inclusions']) > 0) {
            foreach ($jsonConfig['inclusions'] as $key => $value) {
                $newKey = $spotStrings[$key];
                $jsonConfig['inclusions'][$newKey] = $jsonConfig['inclusions'][$key];
                unset($jsonConfig['inclusions'][$key]);
            }
        }
        elseif(isset($jsonConfig['inclusions']) && count($jsonConfig['inclusions']) == 0)
        {
            $jsonConfig['inclusions'] = new stdClass();
        }

        if (isset($jsonConfig['inclusionsInfo']) && count($jsonConfig['inclusionsInfo']) > 0) {
            foreach ($jsonConfig['inclusionsInfo'] as $key => $value) {
                $newKey = $spotStrings[$key];
                $jsonConfig['inclusionsInfo'][$newKey] = $jsonConfig['inclusionsInfo'][$key];
                unset($jsonConfig['inclusionsInfo'][$key]);
            }
        }
        elseif(isset($jsonConfig['inclusionsInfo']) && count($jsonConfig['inclusionsInfo']) == 0)
        {
            $jsonConfig['inclusionsInfo'] = new stdClass();
        }

        return $jsonConfig;
    }

    /**
     *This returns the keys for the provided labels (to allow for translation of the saved spots configurations).
     *
     * @param string $type  the type spot, e.g. accounts / leads, etc.
     * @param array  $items the labels that the key is requested for
     *
     * @return array $keys is the array of matching key values for the label items
     */
    public function getKeysForLabels($type, $items)
    {
        $keys = array();
        foreach ($items as $i) {
            $key = $this->getKeyForLabel($type, $i);
            //Check that the returned array has only 1 element, else there is a potential error
            //Log error and return empty keys
            //Error if 0 || >1
            $countOfMatches = count($key);
            if ($countOfMatches !== 1) {
                $this->logSpotsErrorWithKeyMatching($type);

                return array();
            } else {
                $keys[] = reset($key);
            }
        }

        return $keys;
    }

    /**
     *This logs an error when there is <> 1 matching keys for a label.
     *
     * @param string $type the type spot, e.g. accounts / leads, etc.
     */
    public function logSpotsErrorWithKeyMatching($type)
    {
        global $mod_strings;
        $GLOBALS['log']->error($mod_strings['LBL_AN_DUPLICATE_LABEL_FOR_SUBAREA'].' '.$type);
    }

    /**
     *This returns the key for the provided label (to allow for translation of the saved spots configurations).
     *
     * @param string $type  the type spot, e.g. accounts / leads, etc.
     * @param string $label the label that the key is requested for
     *
     * @return array $matches is the array of matching key values (if <> 1, then there is an issue matching this)
     */
    public function getKeyForLabel($type, $label)
    {
        global $mod_strings;
        $labelPrefix = '';
        switch ($type) {
            case 'getAccountsSpotsData':
                $labelPrefix = 'LBL_AN_ACCOUNTS_';
                break;
            case 'getLeadsSpotsData':
                $labelPrefix = 'LBL_AN_LEADS_';
                break;
            case 'getSalesSpotsData':
                $labelPrefix = 'LBL_AN_SALES_';
                break;
            case 'getServiceSpotsData':
                $labelPrefix = 'LBL_AN_SERVICE_';
                break;
            case 'getActivitiesSpotsData':
                $labelPrefix = 'LBL_AN_ACTIVITIES_';
                break;
            case 'getMarketingSpotsData':
                $labelPrefix = 'LBL_AN_MARKETING_';
                break;
            case 'getMarketingActivitySpotsData':
                $labelPrefix = 'LBL_AN_MARKETINGACTIVITY';
                break;
            case 'getQuotesSpotsData':
                $labelPrefix = 'LBL_AN_QUOTES_';
                break;
        }
        $allMatchingLabels = array_keys($mod_strings, $label);

        $matches = array_filter($allMatchingLabels, function ($e) use ($labelPrefix) {
            return strpos($e, $labelPrefix) !== false;
        });

        return $matches;
    }
}
